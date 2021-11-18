<?php


namespace matze\flagwars\shop;


use matze\flagwars\FlagWars;
use matze\flagwars\player\FlagWarsPlayer;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\session\PlayerManager;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\block\BlockIds;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\LeatherBoots;
use pocketmine\item\LeatherCap;
use pocketmine\item\LeatherPants;
use pocketmine\item\LeatherTunic;
use pocketmine\utils\TextFormat;

class ShopMenu
{
    /** @var InvMenu */
    private InvMenu $menu;
    private ?ShopCategory $category;
    /** @var FlagWarsPlayer */
    private FlagWarsPlayer $player;

    public function __construct(FlagWarsPlayer $player)
    {
        $this->player = $player;
        $this->category = null;
        $this->menu = InvMenu::create(InvMenu::TYPE_CHEST)
            ->setName(TextFormat::RED . "REWE " . TextFormat::GRAY . "- " . TextFormat::RED . "Besser leben!")
            ->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
                $clickedItem = $transaction->getItemClicked();
                $player = $transaction->getPlayer();
                $itemName = TextFormat::clean($clickedItem->getCustomName());
                $fwPlayer = FlagWars::getPlayer($player);
                if($fwPlayer === null) return $transaction->discard();

                $category = $clickedItem->getNamedTag()->getString("Category", "#CoVid19");
                if($category != "#CoVid19") {
                    if(empty(ShopManager::$categories[$category])) return $transaction->discard();

                    $fwPlayer->getShopMenu()->updateCategory(ShopManager::$categories[$category]);
                    return $transaction->discard();
                }

                if(empty($clickedItem->getLore()[0])) return $transaction->discard();

                $infos = explode(" ", $clickedItem->getLore()[0]);
                if(empty($infos[0]) || empty($infos[1])) return $transaction->discard();

                $resource = TextFormat::clean($infos[1]);
                $price = TextFormat::clean($infos[0]);

                if($resource == "Iron") {
                    $resource_obj = ItemIds::IRON_INGOT;
                }else if($resource == "Gold") {
                    $resource_obj = ItemIds::GOLD_INGOT;
                }else {
                    $resource_obj = ItemIds::BRICK;
                }

                $price = ShopManager::setPrice($player, $price, $resource_obj);
                if($price) {
                    $item = $clickedItem;
                    $item->setLore([]);
                    if($item instanceof LeatherBoots || $item instanceof LeatherCap || $item instanceof LeatherTunic || $item instanceof LeatherPants) {
                        $teamColor = $fwPlayer->getTeam()->getColor();
                        $color = ShopManager::teamColorIntoColor($teamColor);
                        $item->setCustomColor($color);
                    }
                    if($item->getId() === BlockIds::WOOL)
                        $item = Item::get(BlockIds::WOOL, $item->getDamage(), $item->getCount());

                    $player->getInventory()->addItem($item);
                    $player->playSound("note.bass", 1, 2, [$player]);
                }else {
                    $player->playSound("note.bass", 1, 1, [$player]);
                }

                if($itemName == "Wool" && !$price) {
                    $count = ShopManager::count($player) * 4;
                    $teamColor = ShopManager::teamColorIntoMeta($fwPlayer->getTeam()->getColor());
                    $sandstone = Item::get(BlockIds::WOOL, $teamColor, $count);
                    $player->getInventory()->addItem($sandstone);
                    ShopManager::rm($player, $resource_obj, ShopManager::count($player, $resource_obj));
                }
                return $transaction->discard();
            });
    }

    public function open()
    {
        if($this->player->getPlayer() == null || !$this->player->getPlayer()->isConnected()) return;

        PlayerManager::getNonNullable($this->player->getPlayer())->getNetwork()->setGraphicWaitDuration(10);
        $category = $this->getCategory();
        $this->menu->getInventory()->setContents($category->getItems($this->player->getTeam()));
        $this->menu->send($this->player->getPlayer(), $category->getCustomName());
    }

    /**
     * @return ShopCategory
     */
    public function getCategory(): ShopCategory
    {
        return $this->category;
    }

    /**
     * @param ShopCategory $category
     */
    public function updateCategory(ShopCategory $category): void
    {
        $this->category = $category;

        $this->getMenu()->getInventory()->setContents($category->getItems($this->player->getTeam()));
    }

    /**
     * @return InvMenu
     */
    public function getMenu(): InvMenu
    {
        return $this->menu;
    }
}
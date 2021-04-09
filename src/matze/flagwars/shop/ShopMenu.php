<?php


namespace matze\flagwars\shop;


use matze\flagwars\FlagWars;
use matze\flagwars\player\FlagWarsPlayer;
use matze\flagwars\shop\categories\RushCategory;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\item\Item;
use pocketmine\item\LeatherBoots;
use pocketmine\item\LeatherCap;
use pocketmine\item\LeatherPants;
use pocketmine\item\LeatherTunic;
use pocketmine\utils\TextFormat;

class ShopMenu
{
    /** @var \muqsit\invmenu\InvMenu */
    private $menu;
    /** @var \matze\flagwars\shop\ShopCategory */
    private $category;
    /** @var FlagWarsPlayer */
    private $player;

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
                    $resource_obj = Item::IRON_INGOT;
                }else if($resource == "Gold") {
                    $resource_obj = Item::GOLD_INGOT;
                }else {
                    $resource_obj = Item::BRICK;
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
                    if($item->getId() === Item::WOOL)
                        $item = Item::get(Item::WOOL, $item->getDamage(), $item->getCount());

                    $player->getInventory()->addItem($item);
                    $player->playSound("note.bass", 1, 2, [$player]);
                }else {
                    $player->playSound("note.bass", 1, 1, [$player]);
                }

                if($itemName == "Wool" && !$price) {
                    $count = ShopManager::count($player) * 4;
                    $teamColor = ShopManager::teamColorIntoMeta($fwPlayer->getTeam()->getColor());
                    $sandstone = Item::get(Item::WOOL, $teamColor, $count);
                    $player->getInventory()->addItem($sandstone);
                    ShopManager::rm($player, $resource_obj, ShopManager::count($player, $resource_obj));
                }
                return $transaction->discard();
            });
    }

    public function open()
    {
        if($this->player->getPlayer() == null || !$this->player->getPlayer()->isConnected()) return;

        $category = $this->getCategory();
        $this->menu->getInventory()->setContents($category->getItems($this->player->getTeam()));
        $this->menu->send($this->player->getPlayer(), $category->getCustomName());
    }

    /**
     * @return \matze\flagwars\shop\ShopCategory
     */
    public function getCategory(): ShopCategory
    {
        return $this->category;
    }

    /**
     * @param \matze\flagwars\shop\ShopCategory $category
     */
    public function updateCategory(ShopCategory $category): void
    {
        $this->category = $category;

        $this->getMenu()->getInventory()->setContents($category->getItems($this->player->getTeam()));
    }

    /**
     * @return \muqsit\invmenu\InvMenu
     */
    public function getMenu(): \muqsit\invmenu\InvMenu
    {
        return $this->menu;
    }
}
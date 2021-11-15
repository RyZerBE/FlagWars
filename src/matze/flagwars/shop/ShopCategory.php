<?php


namespace matze\flagwars\shop;



use matze\flagwars\game\Team;
use pocketmine\block\BlockIds;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\utils\TextFormat;

abstract class ShopCategory
{

    abstract public function getItems(?Team $team): array;
    abstract public function getName(): string;
    abstract public function getCustomName(): string;

    public function categoryList(): array
    {
        $block_category = Item::get(BlockIds::END_STONE)->setCustomName(TextFormat::RED."Blocks");
        $nbt = $block_category->getNamedTag();
        $nbt->setString("Category", "Block");
        $block_category->setNamedTag($nbt);

        $swords_category = Item::get(ItemIds::GOLDEN_SWORD)->setCustomName(TextFormat::RED."Swords");
        $nbt = $swords_category->getNamedTag();
        $nbt->setString("Category", "Sword");
        $swords_category->setNamedTag($nbt);

        $pickaxe_category = Item::get(ItemIds::STONE_PICKAXE)->setCustomName(TextFormat::RED."Tools");
        $nbt = $pickaxe_category->getNamedTag();
        $nbt->setString("Category", "Tools");
        $pickaxe_category->setNamedTag($nbt);

        $protect_category = Item::get(ItemIds::DIAMOND_CHESTPLATE)->setCustomName(TextFormat::RED."Protect");
        $nbt = $protect_category->getNamedTag();
        $nbt->setString("Category", "Protection");
        $protect_category->setNamedTag($nbt);

        $special_category = Item::get(ItemIds::EXPERIENCE_BOTTLE)->setCustomName(TextFormat::RED."Special");
        $nbt = $special_category->getNamedTag();
        $nbt->setString("Category", "Special");
        $special_category->setNamedTag($nbt);

        $potion_category = Item::get(ItemIds::POTION, 8)->setCustomName(TextFormat::RED."Potions");
        $nbt = $potion_category->getNamedTag();
        $nbt->setString("Category", "Potion");
        $potion_category->setNamedTag($nbt);

        $bow_category = Item::get(ItemIds::BOW)->setCustomName(TextFormat::RED."Bows");
        $nbt = $bow_category->getNamedTag();
        $nbt->setString("Category", "Bow");
        $bow_category->setNamedTag($nbt);

        $eat_category = Item::get(ItemIds::COOKED_CHICKEN)->setCustomName(TextFormat::RED."Eat");
        $nbt = $eat_category->getNamedTag();
        $nbt->setString("Category", "Eat");
        $eat_category->setNamedTag($nbt);

        return [
            0 => $block_category,
            1 => $swords_category,
            2 => $pickaxe_category,
            3 => $protect_category,
            5 => $eat_category,
            6 => $bow_category,
            7 => $potion_category,
            8 => $special_category,
        ];
    }
}
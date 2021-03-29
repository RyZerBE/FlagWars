<?php

namespace matze\flagwars\utils;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\nbt\tag\StringTag;

class ItemUtils {

    /**
     * @param Item $item
     * @param string $tag
     * @param string $tagName
     * @return Item
     */
    public static function addItemTag(Item $item, string $tag, string $tagName): Item {
        $nbt = $item->getNamedTag();
        $nbt->setString($tagName, $tag, true);
        $item->setCompoundTag($nbt);
        return $item;
    }

    /**
     * @param Item $item
     * @param array $tags
     * @return Item
     */
    public static function addItemTags(Item $item, array $tags): Item {
        foreach ($tags as $key => $value) {
            $item = self::addItemTag($item, $value, $key);
        }
        return $item;
    }

    /**
     * @param Item $item
     * @param string $tagName
     * @return bool
     */
    public static function hasItemTag(Item $item, string $tagName): bool {
        $nbt = $item->getNamedTag();
        return $nbt->hasTag($tagName, StringTag::class);
    }

    /**
     * @param Item $item
     * @param string $tagName
     * @return string
     */
    public static function getItemTag(Item $item, string $tagName): string {
        $nbt = $item->getNamedTag();
        return $nbt->getString($tagName);
    }

    /**
     * @param Item $item
     * @param string $tagName
     * @return Item
     */
    public static function removeItemTag(Item $item, string $tagName): Item {
        if(!self::hasItemTag($item, $tagName)) {
            return $item;
        }
        $nbt = $item->getNamedTag();
        $nbt->removeTag($tagName);
        $item->setCompoundTag($nbt);
        return $item;
    }

    /**
     * @param Item $item
     * @param array $enchantments
     * @return Item
     */
    public static function addEnchantments(Item $item, array $enchantments): Item {
        foreach ($enchantments as $enchantmentId => $enchantmentLevel) {
            $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($enchantmentId), $enchantmentLevel));
        }
        return $item;
    }
}
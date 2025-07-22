<?php

class IblockElementList
{
    public static function groupItemsByIblockId(array $items): array
    {
        $groupedItems = [];

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $iblockId = $item['IBLOCK_ID'] ?? 0;

            if ($iblockId <= 0) {
                continue;
            }

            $groupedItems[$iblockId][] = $item;
        }

        return $groupedItems;
    }
}
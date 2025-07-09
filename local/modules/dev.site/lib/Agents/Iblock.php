<?php

namespace Only\Site\Agents;

use Only\Site\Handlers\Iblock;
use Bitrix\Main\Loader;
use CIBlockElement;

class IblockAgent
{
    public static function clearOldLogs()
    {
        if (!Loader::includeModule('iblock')) {
            return '\\' . __CLASS__ . '::' . __FUNCTION__ . '();';
        }

        $iblockLogID = Iblock::getIblockIdByCode('LOG');

        $rsElements = CIBlockElement::GetList(
            ['DATE_ACTIVE_FROM' => 'DESC'],
            ['IBLOCK_ID' => $iblockLogID],
            false,
            false,
            ['ID']
        );

        $count = 0;
        $toDelete = [];

        while ($arElement = $rsElements->Fetch()) {
            $count++;
            if ($count > 10) {
                $toDelete[] = $arElement['ID'];
            }
        }

        foreach ($toDelete as $id) {
            CIBlockElement::Delete($id);
        }

        return '\\' . __CLASS__ . '::' . __FUNCTION__ . '();';
    }

    public static function example()
    {
        global $DB;
        if (\Bitrix\Main\Loader::includeModule('iblock')) {
            $iblockId = \Only\Site\Helpers\IBlock::getIblockID('QUARRIES_SEARCH', 'SYSTEM');
            $format = $DB->DateFormatToPHP(\CLang::GetDateFormat('SHORT'));
            $rsLogs = \CIBlockElement::GetList(['TIMESTAMP_X' => 'ASC'], [
                'IBLOCK_ID' => $iblockId,
                '<TIMESTAMP_X' => date($format, strtotime('-1 months')),
            ], false, false, ['ID', 'IBLOCK_ID']);
            while ($arLog = $rsLogs->Fetch()) {
                \CIBlockElement::Delete($arLog['ID']);
            }
        }
        return '\\' . __CLASS__ . '::' . __FUNCTION__ . '();';
    }
}
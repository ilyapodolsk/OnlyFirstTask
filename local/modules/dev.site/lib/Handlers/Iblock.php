<?php

namespace Only\Site\Handlers;

use Bitrix\Main\Loader;
use CUser;
use CIBlock;
use CIBlockElement;
use CIBlockProperty;
use CIBlockSection;

class Iblock
{
    const IBLOCK_CODE = 'LOG';

    public static function onAfterIblockElementAdd($arFields)
    {
        if (!Loader::includeModule('iblock')) {
            return;
        }

        $logIblockId = self::getIblockIdByCode(self::IBLOCK_CODE);

        if ($arFields['IBLOCK_ID'] == $logIblockId) {
            return;
        }

        $USER = new CUser();

        $iblock = CIBlock::GetByID($arFields['IBLOCK_ID'])->Fetch();
        if (!$iblock) {
            return;
        }

        $section = self::findOrCreateSection($iblock);

        $path = self::buildElementPath($arFields, $iblock);

        $newElement = [
            'MODIFIED_BY' => $USER->GetID(),
            'IBLOCK_SECTION_ID' => $section['ID'],
            'IBLOCK_ID' => $logIblockId,
            'NAME' => $arFields['ID'],
            'DATE_ACTIVE_FROM' => date('d.m.Y H:i:s'),
            'PREVIEW_TEXT' => $path,
            'ACTIVE' => 'Y'
        ];

        $el = new CIBlockElement();
        $el->Add($newElement);
    }


    public static function onAfterIblockElementUpdate($arFields)
    {
        if (!Loader::includeModule('iblock')) {
            return;
        }

        $logIblockId = self::getIblockIdByCode(self::IBLOCK_CODE);

        if ($arFields['IBLOCK_ID'] == $logIblockId) {
            return;
        }

        $USER = new CUser();

        $iblock = CIBlock::GetByID($arFields['IBLOCK_ID'])->Fetch();
        if (!$iblock) {
            return;
        }

        $section = self::findOrCreateSection($iblock);

        $path = self::buildElementPath($arFields, $iblock);

        $newElement = [
            'MODIFIED_BY' => $USER->GetID(),
            'IBLOCK_SECTION_ID' => $section['ID'],
            'IBLOCK_ID' => $logIblockId,
            'NAME' => $arFields['ID'],
            'DATE_ACTIVE_FROM' => date('d.m.Y H:i:s'),
            'PREVIEW_TEXT' => $path,
            'ACTIVE' => 'Y'
        ];

        $arFilter = [
            "IBLOCK_ID" => $logIblockId,
            "NAME" => $arFields['ID']
        ];

        $res = CIBlockElement::GetList([], $arFilter, false, false, ['ID']);
        if ($element = $res->Fetch()) {
            $el = new CIBlockElement();
            $el->Update($element['ID'], $newElement);
        } else {
            $el = new CIBlockElement();
            $el->Add($newElement);
        }
    }

    private static function findOrCreateSection($iblock)
    {
        if (!Loader::includeModule('iblock')) {
            return false;
        }

        $logIblockId = self::getIblockIdByCode(self::IBLOCK_CODE);

        $rsSections = CIBlockSection::GetList(
            [],
            [
                "IBLOCK_ID" => $logIblockId,
                "NAME" => $iblock['NAME'],
            ],
            false,
            ['ID']
        );

        if ($section = $rsSections->Fetch()) {
            return $section;
        }

        $section = new CIBlockSection();
        $section->Add([
            'IBLOCK_ID' => $logIblockId,
            'NAME' => $iblock['NAME'],
            'CODE' => $iblock['CODE'],
            'ACTIVE' => 'Y'
        ]);

        return CIBlockSection::GetList(
            [],
            [
                "IBLOCK_ID" => $logIblockId,
                "NAME" => $iblock['NAME'],
            ],
            false,
            ['ID']
        )->Fetch();
    }

    private static function buildElementPath($arFields, $iblock)
    {
        if (!Loader::includeModule('iblock')) {
            return '';
        }

        $sectionPath = [];

        $elementId = $arFields['ID'];
        $element = CIBlockElement::GetByID($elementId)->Fetch();
        if ($element && $element['IBLOCK_SECTION_ID']) {
            $sectionId = $element['IBLOCK_SECTION_ID'];
            while ($sectionId > 0) {
                $section = CIBlockSection::GetByID($sectionId)->Fetch();
                if (!$section) break;
                array_unshift($sectionPath, $section['NAME']);
                $sectionId = $section['IBLOCK_SECTION_ID'];
            }
        }

        $path = [$iblock['NAME']];
        if (!empty($sectionPath)) {
            $path = array_merge($path, $sectionPath);
        }
        $path[] = $arFields['NAME'];

        return implode(' -> ', $path);
    }

    public static function getIblockIdByCode($code)
    {
        if (!Loader::includeModule('iblock')) {
            return false;
        }

        $rsIblock = CIBlock::GetList(
            [],
            [
                "CODE" => $code,
                "ACTIVE" => "Y"
            ],
            false,
            false,
            ['ID']
        );

        if ($arIblock = $rsIblock->Fetch()) {
            return $arIblock["ID"];
        }

        return false;
    }

    function OnBeforeIBlockElementAddHandler(&$arFields)
    {
        $iQuality = 95;
        $iWidth = 1000;
        $iHeight = 1000;

        $dbIblockProps = \Bitrix\Iblock\PropertyTable::getList([
            'select' => ['ID', 'PROPERTY_TYPE'],
            'filter' => ['IBLOCK_ID' => $arFields['IBLOCK_ID']]
        ]);

        $arUserFields = [];
        while ($arIblockProp = $dbIblockProps->Fetch()) {
            if ($arIblockProp['PROPERTY_TYPE'] == 'F') {
                $arUserFields[] = $arIblockProp['ID'];
            }
        }

        foreach ($arUserFields as $iFieldId) {
            if (!isset($arFields['PROPERTY_VALUES'][$iFieldId])) continue;

            foreach ($arFields['PROPERTY_VALUES'][$iFieldId] as &$file) {
                if (!empty($file['VALUE']['tmp_name'])) {
                    $sTempName = $file['VALUE']['tmp_name'] . '_temp';
                    $res = \CAllFile::ResizeImageFile(
                        $file['VALUE']['tmp_name'],
                        $sTempName,
                        ["width" => $iWidth, "height" => $iHeight],
                        BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                        false,
                        $iQuality
                    );
                    if ($res) {
                        rename($sTempName, $file['VALUE']['tmp_name']);
                    }
                }
            }
        }

        if ($arFields['CODE'] == 'brochures') {
            $RU_IBLOCK_ID = \Only\Site\Helpers\IBlock::getIblockID('DOCUMENTS', 'CONTENT_RU');
            $EN_IBLOCK_ID = \Only\Site\Helpers\IBlock::getIblockID('DOCUMENTS', 'CONTENT_EN');

            if ($arFields['IBLOCK_ID'] == $RU_IBLOCK_ID || $arFields['IBLOCK_ID'] == $EN_IBLOCK_ID) {
                Loader::includeModule('iblock');

                $arFiles = [];
                foreach ($arFields['PROPERTY_VALUES'] as $id => &$arValues) {
                    $arProp = \CIBlockProperty::GetByID($id, $arFields['IBLOCK_ID'])->Fetch();

                    if ($arProp['PROPERTY_TYPE'] == 'F' && $arProp['CODE'] == 'FILE') {
                        $key_index = 0;
                        while (isset($arValues['n' . $key_index])) {
                            $arFiles[] = $arValues['n' . $key_index++];
                        }
                    } elseif ($arProp['PROPERTY_TYPE'] == 'L' && $arProp['CODE'] == 'OTHER_LANG' && $arValues[0]['VALUE']) {
                        $arValues[0]['VALUE'] = null;
                        if (!empty($arFiles)) {
                            $OTHER_IBLOCK_ID = $arFields['IBLOCK_ID'] == $RU_IBLOCK_ID ? $EN_IBLOCK_ID : $RU_IBLOCK_ID;
                            $arOtherElement = \CIBlockElement::GetList(
                                [],
                                ['IBLOCK_ID' => $OTHER_IBLOCK_ID, 'CODE' => $arFields['CODE']],
                                false,
                                false,
                                ['ID']
                            )->Fetch();

                            if ($arOtherElement) {
                                \CIBlockElement::SetPropertyValuesEx($arOtherElement['ID'], $OTHER_IBLOCK_ID, ['FILE' => $arFiles]);
                            }
                        }
                    } elseif ($arProp['PROPERTY_TYPE'] == 'E') {
                        $elementIds = [];
                        foreach ($arValues as &$arValue) {
                            if ($arValue['VALUE']) {
                                $elementIds[] = $arValue['VALUE'];
                                $arValue['VALUE'] = null;
                            }
                        }

                        if (!empty($elementIds) && !empty($arFiles)) {
                            $CATALOG_IBLOCK_ID = \Only\Site\Helpers\IBlock::getIblockID(
                                'PRODUCTS',
                                'CATALOG_' . ($arFields['IBLOCK_ID'] == $RU_IBLOCK_ID ? '_RU' : '_EN')
                            );

                            $rsElement = \CIBlockElement::GetList(
                                [],
                                ['IBLOCK_ID' => $CATALOG_IBLOCK_ID, 'ID' => $elementIds],
                                false,
                                false,
                                ['ID', 'IBLOCK_ID']
                            );

                            while ($arElement = $rsElement->Fetch()) {
                                \CIBlockElement::SetPropertyValuesEx($arElement['ID'], $arElement['IBLOCK_ID'], ['FILE' => $arFiles]);
                            }
                        }
                    }
                }
            }
        }
    }
}
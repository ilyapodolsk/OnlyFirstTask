<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

if (!$USER->IsAdmin()) {
    LocalRedirect('/');
}

\Bitrix\Main\Loader::includeModule('iblock');

$res = CIBlock::GetList([], ["CODE" => "vacancy", "TYPE" => "vacancy"], false, false, ["ID"]);
if ($iblock = $res->Fetch()) {
    $IBLOCK_ID = $iblock["ID"];
} else {
    die("Инфоблок не найден");
}

$IBLOCK_ID = $iblock['ID'];

$arProps = [];
$rsProp = CIBlockPropertyEnum::GetList(["SORT" => "ASC", "VALUE" => "ASC"], ['IBLOCK_ID' => $IBLOCK_ID]);
while ($arProp = $rsProp->Fetch()) {
    $key = trim($arProp['VALUE']);
    $arProps[$arProp['PROPERTY_CODE']][$key] = $arProp['ID'];
}

$rsElements = CIBlockElement::GetList([], ['IBLOCK_ID' => $IBLOCK_ID], false, false, ['ID']);
while ($element = $rsElements->GetNext()) {
    CIBlockElement::Delete($element['ID']);
}

$handle = fopen("vacancy.csv", "r");
if (!$handle) {
    die("Ошибка при открытии файлы vacancy.csv");
}

fgetcsv($handle, 1000, ",");

$row = 1;
$el = new CIBlockElement;

while (($data = fgetcsv($handle, 1000, ",")) !== false) {
    $row++;

    if (empty($data[3])) {
        echo "Ошибка в строке {$row}: отсутствует название должности<br>";
        continue;
    }

    $PROP = [
        'ACTIVITY'     => trim($data[9]),
        'FIELD'        => trim($data[11]),
        'OFFICE'       => trim($data[1]),
        'LOCATION'     => trim($data[2]),
        'REQUIRE'      => trim($data[4]),
        'DUTY'         => trim($data[5]),
        'CONDITIONS'   => trim($data[6]),
        'EMAIL'        => trim($data[12]),
        'DATE'         => date('d.m.Y'),
        'TYPE'         => trim($data[8]),
        'SALARY_TYPE'  => '',
        'SALARY_VALUE' => trim($data[7]),
        'SCHEDULE'     => trim($data[10]),
    ];

    foreach ($PROP as $key => &$value) {
        if (stripos($value, '•') !== false) {
            $value = explode('•', $value);
            array_splice($value, 0, 1);
            $value = array_map('trim', $value);
        } elseif (isset($arProps[$key])) {
            foreach ($arProps[$key] as $_key => $_val) {
                if (stripos($_key, $value) !== false || similar_text($_key, $value) > 50) {
                    $value = $_val;
                    break;
                }
            }
        }
    }

    if ($PROP['SALARY_VALUE'] == '-' || $PROP['SALARY_VALUE'] == '') {
        $PROP['SALARY_VALUE'] = '';
    } elseif ($PROP['SALARY_VALUE'] == 'по договоренности') {
        $PROP['SALARY_VALUE'] = '';
        $PROP['SALARY_TYPE'] = $arProps['SALARY_TYPE']['договорная'];
    } else {
        $arSalary = explode(' ', $PROP['SALARY_VALUE']);
        if (in_array($arSalary[0], ['от', 'до'])) {
            $PROP['SALARY_TYPE'] = $arProps['SALARY_TYPE'][$arSalary[0]];
            array_shift($arSalary);
            $PROP['SALARY_VALUE'] = implode(' ', $arSalary);
        } else {
            $PROP['SALARY_TYPE'] = $arProps['SALARY_TYPE']['='];
        }
    }

    if (!empty($PROP['OFFICE']) && is_string($PROP['OFFICE'])) {
        $PROP['OFFICE'] = strtolower($PROP['OFFICE']);

        if ($PROP['OFFICE'] === 'центральный офис') {
            $PROP['OFFICE'] = $arProps['OFFICE']['центральный офис'];
        } elseif ($PROP['OFFICE'] === 'лесозаготовка') {
            $PROP['OFFICE'] = $arProps['OFFICE']['лесозаготовка'];
        } elseif ($PROP['OFFICE'] === 'свеза тюмень') {
            $PROP['OFFICE'] = $arProps['OFFICE']['свеза тюмени'];
        } else {
            $PROP['OFFICE'] = $arProps['OFFICE'][$PROP['OFFICE']] ?? $PROP['OFFICE'];
        }
    }

    $arLoadProductArray = [
        "MODIFIED_BY" => $USER->GetID(),
        "IBLOCK_SECTION_ID" => false,
        "IBLOCK_ID" => $IBLOCK_ID,
        "PROPERTY_VALUES" => $PROP,
        "NAME" => trim($data[3]),
        "ACTIVE" => !empty($data[13]) ? 'Y' : 'N',
    ];

    if ($PRODUCT_ID = $el->Add($arLoadProductArray)) {
        echo "Добавлен элемент с ID: {$PRODUCT_ID}<br>";
    } else {
        echo "Ошибка в строке {$row}: " . $el->LAST_ERROR . "<br>";
    }
}

fclose($handle);
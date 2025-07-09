<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

require_once($_SERVER["DOCUMENT_ROOT"] . "/local/modules/dev.site/lib/Handlers/Iblock.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/modules/dev.site/lib/Agents/Iblock.php");

AddEventHandler('iblock', 'OnAfterIBlockElementAdd', [
    '\Only\Site\Handlers\Iblock',
    'OnAfterIBlockElementAdd'
]);

AddEventHandler('iblock', 'OnAfterIBlockElementUpdate', [
    '\Only\Site\Handlers\Iblock',
    'OnAfterIBlockElementUpdate'
]);
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->addExternalCss($this->GetFolder().'/css/common.css');?>

<div id="barba-wrapper">
    <div class="article-list">
        <?foreach($arResult["ITEMS"] as $arItem):
        ?>
            <a class="article-item article-list__item"
               href="<?=$arItem["DETAIL_PAGE_URL"]?>">
                <div class="article-item__background">
                    <img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"] ?: $this->GetFolder().'/images/article-item-bg-default.jpg'?>"
                         alt="<?=htmlspecialcharsbx($arItem["NAME"])?>" />
                </div>
                <div class="article-item__wrapper">
                    <div class="article-item__title"><?=$arItem["NAME"]?></div>
                    <div class="article-item__content"><?=TruncateText($arItem["PREVIEW_TEXT"], 100)?></div>
                </div>
            </a>
        <?endforeach;?>
    </div>
</div>
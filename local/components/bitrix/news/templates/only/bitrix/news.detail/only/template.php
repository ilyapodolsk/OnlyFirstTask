<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$this->addExternalCss($this->GetFolder().'/css/common.css');
?>

<div class="article-card">
    <div class="article-card__title"><?=$arResult["NAME"]?></div>
    <div class="article-card__date"><?=FormatDate("d м Y", MakeTimeStamp($arResult["ACTIVE_FROM"]))?></div>
    <div class="article-card__content">

        <?if (!empty($arResult["DETAIL_PICTURE"])):?>
            <div class="article-card__image sticky">
                <img src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" alt="<?=$arResult["NAME"]?>" data-object-fit="cover"/>
            </div>
        <?endif;?>
        <div class="article-card__text">
            <div class="block-content" data-anim="anim-3">
                <?=$arResult["DETAIL_TEXT"]?>
            </div>
            <a class="article-card__button" href="/">Назад к новостям</a>
        </div>

    </div>
</div>
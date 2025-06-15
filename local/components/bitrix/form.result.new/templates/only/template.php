<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->addExternalCss($this->GetFolder().'/css/common.css');?>
<?=$arResult["FORM_NOTE"]?>
<?if ($arResult["isFormNote"] != "Y")
{
?>
<div class="contact-form">
    <div class="contact-form__head">
        <div class="contact-form__head-title">Связаться</div>
        <div class="contact-form__head-text"><?=$arResult["FORM_DESCRIPTION"]?></div>
    </div>
    <?=str_replace('<form', '<form class="contact-form__form"', $arResult["FORM_HEADER"])?>

    <!-- Ваше имя -->
        <div class="contact-form__form-inputs">
            <div class="input contact-form__input"><label class="input__label" for="medicine_name">
                <div class="input__label-text">Ваше имя*</div>
                       <?=str_replace('<input', '<input class="input__input"', $arResult["QUESTIONS"]["SIMPLE_QUESTION_169"]["HTML_CODE"])?>
                <?if (!empty($arResult["ERROR"]["SIMPLE_QUESTION_169"])):?>
                    <div class="input__notification">Поле должно содержать не менее 3-х символов</div>
                <?endif;?>
            </label></div>

            <!-- Компания/Должность -->
            <div class="input contact-form__input"><label class="input__label" for="medicine_company">
                <div class="input__label-text">Компания/Должность*</div>
                <?=str_replace('<input', '<input class="input__input"', $arResult["QUESTIONS"]["SIMPLE_QUESTION_166"]["HTML_CODE"])?>
                <?if (!empty($arResult["ERROR"]["SIMPLE_QUESTION_166"])):?>
                    <div class="input__notification">Поле должно содержать не менее 3-х символов</div>
                <?endif;?>
            </label></div>

            <!-- Email -->
            <div class="input contact-form__input"><label class="input__label" for="medicine_email">
                <div class="input__label-text">Email*</div>
                <?=str_replace('<input', '<input class="input__input"', $arResult["QUESTIONS"]["SIMPLE_QUESTION_491"]["HTML_CODE"])?>
                <?if (!empty($arResult["ERROR"]["SIMPLE_QUESTION_491"])):?>
                    <div class="input__notification">Неверный формат почты</div>
                <?endif;?>
            </label></div>

            <!-- Номер телефона -->
            <div class="input contact-form__input"><label class="input__label" for="medicine_phone">
                <div class="input__label-text">Номер телефона*</div>
                <?=str_replace('<input', '<input class="input__input" type="tel"
                       data-inputmask="\'mask\': \'+79999999999\', \'clearIncomplete\': \'true\'" maxlength="12"
                       x-autocompletetype="phone-full"', $arResult["QUESTIONS"]["SIMPLE_QUESTION_961"]["HTML_CODE"])?>
            </label></div>
        </div>

        <!-- Сообщение -->
        <div class="contact-form__form-message">
            <div class="input"><label class="input__label" for="medicine_message">
                <div class="input__label-text">Сообщение</div>
                <?=str_replace('<textarea', '<textarea class="input__input"', $arResult["QUESTIONS"]["SIMPLE_QUESTION_310"]["HTML_CODE"])?>
                <div class="input__notification"></div>
            </label></div>
        </div>
        <div class="contact-form__bottom">
            <div class="contact-form__bottom-policy">Нажимая &laquo;Отправить&raquo;, Вы&nbsp;подтверждаете, что
                ознакомлены, полностью согласны и&nbsp;принимаете условия &laquo;Согласия на&nbsp;обработку персональных
                данных&raquo;.
                </div>
            <input class="form-button contact-form__bottom-button"
                type="submit"
                name="web_form_submit"
                value="Оставить заявку"
            />
        </div>
    <?=$arResult["FORM_FOOTER"]?>
</div>

<?
}
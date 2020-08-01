<?
/**
 * Copyright (c) 1/8/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<script>
var IsSub = <?if($arResult["IS_SUB"]):?>true<?else:?>false<?endif;?>;
var SubBut = '<?=GetMessage("IMPROVED_TP_SUBS_BUT")?>';
</script>
<div style="float: right;margin-top: -44px;font-size: 12px;">
<form name="review_subs" action="<?=POST_FORM_ACTION_URI?>" method="post" id="review_subs">
<?=bitrix_sessid_post()?>    
<input type="hidden" value="Y" name="SUBS">
<input type="hidden" value="" name="EMAIL" id="SUBS_EMAIL_F">
<?if(!$USER->IsAuthorized()):?>
<input type="hidden" value="" name="captcha_word" id="captcha_wordF">
<input type="hidden" value="" name="captcha_sid" id="captcha_sidF">
<?endif;?>
<?if($arResult["IS_SUB"]):?>
<input type="hidden" value="UNSUBS" name="ACTION">
    (<a href="javascript:void(0)" onclick="document.forms.review_subs.submit();"><?=GetMessage("IMPROVED_TP_UNSUBS")?></a>)
<?else:?>
<input type="hidden" value="SUBS" name="ACTION">
    (<a href="javascript:void(0)" <?if(!$USER->IsAuthorized()):?>onclick="ElSub();return false;"<?else:?>onclick="document.forms.review_subs.submit();"<?endif;?>><?=GetMessage("IMPROVED_TP_SUBS")?></a>)
<?endif;?>
</form>
</div>
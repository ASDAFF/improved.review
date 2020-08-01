<?
/**
 * Copyright (c) 1/8/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/interface/admin_lib.php');
CComponentUtil::__IncludeLang(BX_PERSONAL_ROOT."/components/improved/review.subs", "window.php");

$obJSPopup = new CJSPopup('',Array("SUFFIX"=>"review_subs","TITLE"=>GetMessage("IMPROVED_REVIEW_LIST_T_WINDOW_HEAD_TITLE")));
$obJSPopup->ShowTitlebar();
$obJSPopup->StartContent();
$CAPTCHA_CODE = htmlspecialcharsEx($APPLICATION->CaptchaGetCode());
?>
<div id="SUBS_ERROR" style="color: red;"></div>
<table class="bx-width100 internal">
       <tr>
           <td valign="top"><?=GetMessage("IMPROVED_REVIEW_LIST_T_WINDOW_EMAIL")?>:</td>
           <td>
    	      <input type="text" name="EMAIL" id="SUBS_EMAIL" value="<?=$APPLICATION->get_cookie("REVIEW_AUTHOR_EMAIL", "IMPROVED_REVIEW");?>" />
           </td>
       </tr>
       
       <tr>
        <td colspan="2"><?=GetMessage("IMPROVED_REVIEW_LIST_T_WINDOW_FORM_CAPT_1");?></td>
       </tr>
       <tr>
        <td></td>
        <td><img src="/bitrix/tools/captcha.php?captcha_sid=<?=$CAPTCHA_CODE?>" id="captcha_img_s" width="180" height="40" alt="CAPTCHA"/></td>
       </tr>       
       <tr>
           <td valign="top"><?=GetMessage("IMPROVED_REVIEW_LIST_T_WINDOW_FORM_CAPT_2")?>:</td>
           <td>
    	      <input type="hidden" id="captcha_sid" value="<?=$CAPTCHA_CODE?>" />
              <input type="text" class="inputtext" id="captcha_word" />
           </td>
       </tr>
       
</table>
<?
       $obJSPopup->EndContent();
?>
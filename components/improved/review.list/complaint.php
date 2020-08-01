<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$APPLICATION->RestartBuffer();
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/interface/admin_lib.php');
//CUtil::JSPostUnescape();
$popupWindow = new CJSPopup('', '');
$popupWindow->ShowTitlebar(GetMessage("MAIN_EDIT"));
$popupWindow->StartContent();
$arReview = aReview::GetById(intval($_POST["RID"]),array("MESSAGE"))->fetch();
?>
<div id="send-complain">
<form name="complain" id="complain" action="<?=POST_FORM_ACTION_URI?>" method="post">
	<?=bitrix_sessid_post()?>
    <input type="hidden" value="COMPLAINT_ADD" name="ACTION">
    <input type="hidden" value="<?=$_POST["RID"]?>" name="RID">
<table class="bx-width100">
       <tr>
                <td colspan="2">
                <img style="float: left; margin-right: 15px;" src="/bitrix/components/improved/review.list/templates/.default/images/warning.png" width="50" height="44" /><br />
                <?=GetMessage("IMPROVED_REVIEW_LIST_T_JS_FOOTER")?><br /><br /><b><?=GetMessage("IMPROVED_REVIEW_LIST_T_JS_HEAD")?>:</b><?=$arReview["MESSAGE"]?><br /><br />
                </td>
       </tr>
       <?if(!$USER->IsAuthorized()):?>
       <tr id="snaf">
                <td>*<?=GetMessage("IMPROVED_REVIEW_LIST_T_JS_SENDER_NAME")?>: <input type="text" size="25" id="SENDER_NAME" name="SENDER_NAME" value="<?=$SENDER_NAME?>"></td>
                <td>*<?=GetMessage("IMPROVED_REVIEW_LIST_T_JS_SENDER_EMAIL")?>: <input type="text" size="25" id="SENDER_EMAIL" name="SENDER_EMAIL" value="<?=$SENDER_EMAIL?>"></td>
        </tr>
       <?endif;?>
       <tr>
                <td colspan="2" class="no-bootom-border" valign="center"><small>*<?=GetMessage("IMPROVED_REVIEW_LIST_T_JS_COMMENT")?>:</small>
                <textarea name="comment" id="cp-review-text" rows=2 cols=5 style="width:100%;"><?=$MESSAGE?></textarea></td>
        </tr>
       <tr>
                <td colspan="2" class="no-bootom-border" valign="center"><font color="red"><small><?=GetMessage("IMPROVED_REVIEW_LIST_T_JS_REQ")?></small></font></td>
        </tr>
</table>
</form>
</div>
<?
if($strWarning <> "")
	$popupWindow->ShowValidationError($strWarning);
    
$popupWindow->ShowStandardButtons();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");die();?>
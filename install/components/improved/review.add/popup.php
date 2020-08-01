<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
define('BX_PUBLIC_MODE',true);
$arParams['SHOW_FORM'] = true;
$APPLICATION->RestartBuffer();
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/interface/admin_lib.php');
CUtil::JSPostUnescape();
$popupWindow = new CJSPopup('', '');
$popupWindow->ShowTitlebar(GetMessage("IMPROVED_REIVEW_ADD_FORM_TITLE"));
$popupWindow->StartContent();
?>
<?$APPLICATION->IncludeComponent("improved:review.add", "popup", $arParams);?>
<?
//if($arResult["ERROR_MESSAGE"] <> "")
//	$popupWindow->ShowValidationError($arResult["ERROR_MESSAGE"]);
    

$popupWindow->StartButtons();?>
<input type="submit" name="savebtn" class="adm-btn-save" id="savebtn" value="<?=GetMessage('IMPROVED_REVIEW_POPUP_SUBMIT')?>" onclick="window.oReviewDialog.PostParameters();"/>
<?
$popupWindow->ShowStandardButtons(array('close'));
$popupWindow->EndButtons();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");?>
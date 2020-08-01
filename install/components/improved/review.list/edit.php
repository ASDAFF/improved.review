<?
/**
 * Copyright (c) 1/8/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$APPLICATION->RestartBuffer();
$arParams["NOT_HIDE_FORM"]="Y";
$arParams["USE_CAPTCHA"] = "N";
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/interface/admin_lib.php');
//CUtil::JSPostUnescape();
$popupWindow = new CJSPopup('', '');
$popupWindow->ShowTitlebar(GetMessage("MAIN_EDIT"));
$popupWindow->StartContent();
?>
<?$APPLICATION->IncludeComponent("improved:review.add", "edit", $arParams);?>
<?
if($strWarning <> "")
	$popupWindow->ShowValidationError($arResult["ERROR_MESSAGE"]);
    
$popupWindow->ShowStandardButtons();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");die();?>
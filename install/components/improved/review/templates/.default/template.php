<?
/**
 * Copyright (c) 1/8/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$templateAdd = '';
if($arParams['SHOW_POPUP']=='Y')
    $templateAdd = 'popup';
    
$APPLICATION->IncludeComponent("improved:review.add", $templateAdd, $arParams,$component);?>

<?$APPLICATION->IncludeComponent("improved:review.list", ".default", $arParams,$component);?>
<?
/**
 * Copyright (c) 1/8/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */


require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php"); 
IncludeModuleLangFile(__FILE__);
CModule::IncludeModule("improved.review");
// Get rights for the module
$alx_commentsModulePermissions = $APPLICATION->GetGroupRight("improved.review");
if ($alx_commentsModulePermissions == "D")
		$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$sTableID = "tbl_improved_review_rating_list";
$oSort = new CAdminSorting($sTableID, "ID", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);
$obEnum = new CUserTypeEntity;
$rsData = $obEnum->GetList(array(), array("USER_TYPE_ID"=>"IMPROVED_REVIEW_RATING","LANG"=>LANG));
$rsData = new CAdminResult($rsData, $sTableID);

$arHeaders = array(
	array("id"=>"LIST_COLUMN_LABEL",   "content"=>GetMessage("IMPROVED_REVIEW_RATING_LIST_HEADER"),				"sort"=>"ID",				"default"=>true),
);
$lAdmin->AddHeaders($arHeaders);
$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();
while($arRes = $rsData->GetNext())
{
    $row =& $lAdmin->AddRow($arRes["ID"], $arRes,"/bitrix/admin/userfield_edit.php?ID=".$arRes["ID"]."&lang=".LANG.'&back_url='.$APPLICATION->GetCurPageParam());
    $row->AddViewField("LIST_COLUMN_LABEL", $arRes["LIST_COLUMN_LABEL"]);
}
$lAdmin->AddFooter(
	array(
		array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()),
		array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"),
	)
);
        
$lAdmin->CheckListMode();
$APPLICATION->SetTitle(GetMessage("IMPROVED_REVIEW_RATING_LIST_TITLE"));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php"); // Second prolog

$aContext = array( Array(
	"TEXT" => GetMessage("MAIN_ADD"),
	"TITLE" => GetMessage("IMPROVED_REVIEW_ADD_RATING"),
	"ICON"=>"btn_new",
	"LINK"=>'/bitrix/admin/userfield_edit.php?lang='.LANG.'&USER_TYPE_ID=IMPROVED_REVIEW_RATING&ENTITY_ID=IMPROVED_REVIEW&FIELD_NAME=UF_'.strtoupper(RandString(4)).'&back_url='.$APPLICATION->GetCurPageParam(),
));
$lAdmin->AddAdminContextMenu($aContext);
$lAdmin->DisplayList();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
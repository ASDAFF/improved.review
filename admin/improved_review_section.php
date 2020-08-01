<?
/**
 * Copyright (c) 1/8/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/improved.review/include.php");
IncludeModuleLangFile(__FILE__);

// Get rights for the module
$sModulePermissions = $APPLICATION->GetGroupRight("improved.review");
if ($sModulePermissions == "D")
		$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$sTableID = "tbl_improved_review_section";
$oSort = new CAdminSorting($sTableID, "ELEMENT_ID", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);

if ($arID = $lAdmin->GroupAction())		
{
		if($_REQUEST['action_target']=='selected')
		{
				$rsUniqData = aReview::GetList(Array($by=>$order), $arFilter);
				while($arRes = $rsUniqData->Fetch())
						$arID[] = $arRes["ELEMENT_ID"];
		}
		foreach($arID as $ID)
		{
				if(strlen($ID)<=0)
						continue;

				switch($_REQUEST['action'])
				{
						case "deleteG":
								IMPROVED\Review\ReviewTable::deleteByElement($ID);
								break;
				}
		}
}
$arFilter = array();
$rsData = aReview::GetList(array($by=>$order), array(),array("ELEMENT_ID","CNT"),Array("ELEMENT_ID"));
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("IMPROVED_REVIEW_ADMIN_ELEMENT_NAV")));

$arHeaders = array(
		array("id"=>"ID",				"content"=>"ID",													"sort"=>"id",			"default"=>false),
		array("id"=>"ELEMENT_ID",		"content"=>GetMessage("IMPROVED_REVIEW_HEAD_ELEMENT_ID"),			"sort"=>"ELEMENT_ID",	"default"=>true),
		array("id"=>"ELEMENT_PAGE",		"content"=>GetMessage("IMPROVED_REVIEW_HEAD_ELEMENT_PAGE"),								"default"=>true),
		array("id"=>"CNT",				"content"=>GetMessage("IMPROVED_REVIEW_HEAD_REVIEW_CNT"),			"sort"=>"CNT",			"default"=>true),
);
$lAdmin->AddHeaders($arHeaders);

$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();
$arIBlockElementCache = Array();
while($arRes = $rsData->NavNext(true, "f_"))
{
	$row =& $lAdmin->AddRow($arRes["ELEMENT_ID"], $arRes);
	if(in_array("ELEMENT_ID", $arVisibleColumns) && CModule::IncludeModule("iblock"))
	{
			$obIBlockElement = CIBlockElement::GetList(Array(), Array("ID" => intval($arRes["ELEMENT_ID"])), false, false, Array("DETAIL_PAGE_URL", "ID", "IBLOCK_ID", "NAME"));
			if($arIBlockElement = $obIBlockElement->GetNext())
			{
				$showField = '<a href="/bitrix/admin/improved_review_list.php?lang='.LANG.'&ELEMENT_ID='.$arIBlockElement["ID"].'">'.($arIBlockElement["NAME"]).'</a>';
				$pageField = '<a href="'.$arIBlockElement["DETAIL_PAGE_URL"].'" target="_blank">'.GetMessage("IMPROVED_REVIEW_ELEMENT_PAGE").'</a>';
			}
			else
			{
				$showField = '';
				$pageField = '';
			}
	}
	$row->AddField("ELEMENT_ID", $showField);
	$row->AddField("ELEMENT_PAGE", $pageField);

	$arActions = Array();
	$arActions[] = array("ICON"=>"delete", "TEXT"=>GetMessage("MAIN_ADMIN_MENU_DELETE"), "ACTION"=>"if(confirm('".GetMessage("IMPROVED_REVIEW_DEL_CONF_SECT")."')) ".$lAdmin->ActionDoGroup($f_ELEMENT_ID, "deleteG"));
	$row->AddActions($arActions);
}

$lAdmin->AddFooter(
		array(
				array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()),
				array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"),
		)
);

$lAdmin->AddGroupActionTable(Array(
	"deleteG"=>GetMessage("MAIN_ADMIN_LIST_DELETE"),
	));

$lAdmin->CheckListMode();
$APPLICATION->SetTitle(GetMessage("IMPROVED_REVIEW_ADMIN_TITLE"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
$lAdmin->DisplayList();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
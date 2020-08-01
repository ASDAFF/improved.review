<?
/**
 * Copyright (c) 1/8/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
                return;

$arIBlockType = array();
$rsIBlockType = CIBlockType::GetList(array("sort"=>"asc"), array("ACTIVE"=>"Y"));
while ($arr=$rsIBlockType->Fetch())
{
                if($ar=CIBlockType::GetByIDLang($arr["ID"], LANGUAGE_ID))
                {
                                $arIBlockType[$arr["ID"]] = "[".$arr["ID"]."] ".$ar["NAME"];
                }
}

$arIBlock=array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
{
                $arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
                $arIBlockToUrl[$arr["ID"]] = $arr;
}
$arCurrentValues["SEF_BASE_URL"] = $arIBlockToUrl[$arCurrentValues["IBLOCK_ID"]]["LIST_PAGE_URL"];
$arCurrentValues["SECTION_PAGE_URL"] = str_replace($arCurrentValues["SEF_BASE_URL"],"",$arIBlockToUrl[$arCurrentValues["IBLOCK_ID"]]["SECTION_PAGE_URL"]);
$arCurrentValues["DETAIL_PAGE_URL"] = str_replace($arCurrentValues["SEF_BASE_URL"],"",$arIBlockToUrl[$arCurrentValues["IBLOCK_ID"]]["DETAIL_PAGE_URL"]);

$obGroups = CGroup::GetList(($by="id"), ($order="desc"));
while($arGroup = $obGroups->Fetch())
{
	$arGroups[$arGroup["ID"]] = $arGroup["NAME"];
}

$arComponentParameters = array(
                "GROUPS" => array(
                ),
                "PARAMETERS" => array(
                                //"AJAX_MODE" => Array(),
                                "IBLOCK_TYPE" => array(
                                                "PARENT" => "BASE",
                                                "NAME" => GetMessage("IMPROVED_PM_IBLOCK_TYPE"),
                                                "TYPE" => "LIST",
                                                "VALUES" => $arIBlockType,
                                                "REFRESH" => "Y",
                                ),
                                "IBLOCK_ID" => array(
                                                "PARENT" => "BASE",
                                                "NAME" => GetMessage("IMPROVED_PM_IBLOCK_IBLOCK"),
                                                "TYPE" => "LIST",
                                                "ADDITIONAL_VALUES" => "Y",
                                                "VALUES" => $arIBlock,
                                                "REFRESH" => "Y",
                                ),
                                "IS_SEF" => array(
                                                "PARENT" => "BASE",
                                                "NAME" => GetMessage("IMPROVED_PM_IS_SEF"),
                                                "TYPE" => "CHECKBOX",
                                                "DEFAULT" => "N",
                                                "REFRESH" => "Y",
                                ),
                                "SEF_BASE_URL" => array(
                                                "PARENT" => "BASE",
                                                "NAME"=>GetMessage("IMPROVED_PM_SEF_BASE_URL"),
                                                "TYPE"=>"STRING",
                                                "DEFAULT"=>$arCurrentValues["SEF_BASE_URL"],
                                ),
                                "SECTION_PAGE_URL" => CIBlockParameters::GetPathTemplateParam(
                                                "SECTION",
                                                "SECTION_PAGE_URL",
                                                GetMessage("IMPROVED_PM_SECTION_PAGE_URL"),
                                                $arCurrentValues["SECTION_PAGE_URL"],
                                                "BASE"
                                ),
                                "DETAIL_PAGE_URL" => CIBlockParameters::GetPathTemplateParam(
                                                "DETAIL",
                                                "DETAIL_PAGE_URL",
                                                GetMessage("IMPROVED_PM_DETAIL_PAGE_URL"),
                                                $arCurrentValues["DETAIL_PAGE_URL"],
                                                "BASE"
                                ),
                                "ELEMENT_ID" => array(
                                                "PARENT" => "BASE",
                                                "NAME" => GetMessage("IMPROVED_PM_ELEMENT_ID"),
                                                "TYPE" => "STRING",
                                                "DEFAULT" => '={$_REQUEST["ELEMENT_ID"]}',
                                ),
                                "ELEMENT_CODE" => array(
                                                "PARENT" => "BASE",
                                                "NAME" => GetMessage("IMPROVED_PM_ELEMENT_CODE"),
                                                "TYPE" => "STRING",
                                                "DEFAULT" => '={$_REQUEST["ELEMENT_CODE"]}',
                                ),
                                "CACHE_TIME"  =>  Array("DEFAULT"=>86400),
                                "SHOW_TITLE" => array(
                                                "PARENT" => "BASE",
                                                "NAME" => GetMessage("IMPROVED_PM_SHOW_TITLE"),
                                                "TYPE" => "CHECKBOX",
                                                "DEFAULT" => "Y",
                                ),                                
                                "ALLOW_SET" => array(
                                                "PARENT" => "BASE",
                                                "NAME" => GetMessage("IMPROVED_PM_ALLOW_SET"),
                                                "TYPE" => "CHECKBOX",
                                                "DEFAULT" => "Y",
                                ),                                
                ),
);
if($arCurrentValues["IS_SEF"] === "Y")
{
                unset($arComponentParameters["PARAMETERS"]["ELEMENT_ID"]);
                unset($arComponentParameters["PARAMETERS"]["ELEMENT_CODE"]);
                unset($arComponentParameters["PARAMETERS"]["SECTION_URL"]);
}
else
{
                unset($arComponentParameters["PARAMETERS"]["SEF_BASE_URL"]);
                unset($arComponentParameters["PARAMETERS"]["DETAIL_PAGE_URL"]);
                unset($arComponentParameters["PARAMETERS"]["SECTION_PAGE_URL"]);
}
if($arCurrentValues["SHOW_TITLE"] == "Y" || !isset($arCurrentValues["SHOW_TITLE"]))
{
				$arComponentParameters["PARAMETERS"]["TITLE_TEXT"] = Array(
								"PARENT" => "BASE",
								"NAME" => GetMessage("IMPROVED_PM_SHOW_TITLE_TEXT"),
                                "TYPE" => "STRING",
                                "DEFAULT" => GetMessage("IMPROVED_PM_TITLE"),
	);				
}
?>
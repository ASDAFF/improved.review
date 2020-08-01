<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock") ||
   !CModule::IncludeModule("improved.review"))
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
$arIBlockToUrl=array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
{
    $arIBlockToUrl[$arr["ID"]] = $arr;
    $arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}
$arCurrentValues["SEF_BASE_URL"] = $arIBlockToUrl[$arCurrentValues["IBLOCK_ID"]]["LIST_PAGE_URL"];
$arCurrentValues["SECTION_PAGE_URL"] = str_replace($arCurrentValues["SEF_BASE_URL"],"",$arIBlockToUrl[$arCurrentValues["IBLOCK_ID"]]["SECTION_PAGE_URL"]);
$arCurrentValues["DETAIL_PAGE_URL"] = str_replace($arCurrentValues["SEF_BASE_URL"],"",$arIBlockToUrl[$arCurrentValues["IBLOCK_ID"]]["DETAIL_PAGE_URL"]);

$obGroups = CGroup::GetList(($by="id"), ($order="desc"));
while($arGroup = $obGroups->Fetch())
{
	$arGroups[$arGroup["ID"]] = $arGroup["NAME"];
}
$arUF = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields("IMPROVED_REVIEW",0,LANGUAGE_ID);
$arUFF = array();
$arUFVot = Array();
foreach($arUF as $k=>$arUF)
{
    if($arUF['USER_TYPE_ID']=='IMPROVED_REVIEW_RATING')
    {
        $arUFVot[$k] = $arUF["EDIT_FORM_LABEL"];
        unset($arUF[$k]);
    }
    else
    {
        $arUFF[$k] = $arUF["EDIT_FORM_LABEL"];
        unset($arUF[$k]);
    }
}
$timestamp = time();
$arComponentParameters = array(
    "GROUPS" => array(
	    "PAGE_SETTINGS"=> Array("NAME"=>GetMessage("IMPROVED_REVIEW_CMP_PM_PAGE_SETTINGS")),
    ),
    "PARAMETERS" => array(
//DATA                
        "IBLOCK_TYPE" => array(
                        "PARENT" => "DATA_SOURCE",
                        "NAME" => GetMessage("IMPROVED_PM_IBLOCK_TYPE"),
                        "TYPE" => "LIST",
                        "VALUES" => $arIBlockType,
                        "REFRESH" => "Y",
        ),
        "IBLOCK_ID" => array(
                        "PARENT" => "DATA_SOURCE",
                        "NAME" => GetMessage("IMPROVED_PM_IBLOCK_IBLOCK"),
                        "TYPE" => "LIST",
                        "ADDITIONAL_VALUES" => "Y",
                        "VALUES" => $arIBlock,
                        "REFRESH" => "Y",
        ),
        "IS_SEF" => array(
                        "PARENT" => "DATA_SOURCE",
                        "NAME" => GetMessage("IMPROVED_PM_IS_SEF"),
                        "TYPE" => "CHECKBOX",
                        "DEFAULT" => "N",
                        "REFRESH" => "Y",
        ),
        "SEF_BASE_URL" => array(
                        "PARENT" => "DATA_SOURCE",
                        "NAME"=>GetMessage("IMPROVED_PM_SEF_BASE_URL"),
                        "TYPE"=>"STRING",
                        "DEFAULT"=>$arCurrentValues["SEF_BASE_URL"]
        ),
        "SECTION_PAGE_URL" => CIBlockParameters::GetPathTemplateParam(
                        "SECTION",
                        "SECTION_PAGE_URL",
                        GetMessage("IMPROVED_PM_SECTION_PAGE_URL"),
                        $arCurrentValues["SECTION_PAGE_URL"],
                        "DATA_SOURCE"                                                
        ),
        "DETAIL_PAGE_URL" => CIBlockParameters::GetPathTemplateParam(
                        "DETAIL",
                        "DETAIL_PAGE_URL",
                        GetMessage("IMPROVED_PM_DETAIL_PAGE_URL"),
                        $arCurrentValues["DETAIL_PAGE_URL"],
                        "DATA_SOURCE"
        ),
        "ELEMENT_ID" => array(
                        "PARENT" => "DATA_SOURCE",
                        "NAME" => GetMessage("IMPROVED_PM_ELEMENT_ID"),
                        "TYPE" => "STRING",
                        "DEFAULT" => '={$_REQUEST["ELEMENT_ID"]}',
        ),
        "ELEMENT_CODE" => array(
                        "PARENT" => "DATA_SOURCE",
                        "NAME" => GetMessage("IMPROVED_PM_ELEMENT_CODE"),
                        "TYPE" => "STRING",
                        "DEFAULT" => '={$_REQUEST["ELEMENT_CODE"]}',
        ),
        
//BASE                                
        "ALLOW_VOTE" => array(
                        "PARENT" => "BASE",
                        "NAME" => GetMessage("IMPROVED_PM_ALLOW_VOTE"),
                        "TYPE" => "CHECKBOX",
                        "DEFAULT" => "Y",
        ),
        "ONLY_AUTH_COMPLAINT" => array(
                        "PARENT" => "BASE",
                        "NAME" => GetMessage("IMPROVED_PM_ONLY_AUTH_COMPLAINT"),
                        "TYPE" => "CHECKBOX",
                        "DEFAULT" => "Y",
        ),
        "MOD_GOUPS" => array(
                        "PARENT" => "BASE",
                        "NAME" => GetMessage("IMPROVED_PM_MODERATING_GROUPS"),
                        "TYPE" => "LIST",
                        "VALUES" => $arGroups,
						"MULTIPLE" => "Y",
        ),								
        "COMMENTS_MODE" => array(
                        "PARENT" => "BASE",
                        "NAME" => GetMessage("IMPROVED_PM_COMMENTS_MODE"),
                        "TYPE" => "CHECKBOX",
                        "DEFAULT" => "N",
						"REFRESH" => "Y",
        ),
        "USER_PATH" => array(
                        "PARENT" => "BASE",
                        "NAME" => GetMessage("IMPROVED_PM_USER_PATH"),
                        "TYPE" => "STRING",
                        "DEFAULT" => '',
        ),        
//VISUAL           
        "UF" => array(
                        "PARENT" => "VISUAL",
                        "NAME" => GetMessage("IMPROVED_PM_NAME_UF"),
                        "TYPE" => "LIST",
                        "MULTIPLE" => "Y",
                        "VALUES" => $arUFF,
        ),    
        "UF_VOTE" => array(
                        "PARENT" => "VISUAL",
                        "NAME" => GetMessage("IMPROVED_PM_NAME_UF_VOTE"),
                        "TYPE" => "LIST",
                        "MULTIPLE" => "Y",
                        "VALUES" => $arUFVot,
        ),                 
        "POST_DATE_FORMAT" => array(
                        "PARENT" => "VISUAL",
                        "NAME" => GetMessage("IMPROVED_PM_POST_DATE_FORMAT"),
                        "TYPE" => "LIST",
                        "ADDITIONAL_VALUES" => "N",
                        "VALUES" => Array(
                                        "j M Y" => aReview::FormatDate("j M Y", $timestamp),//"22 Feb 2007",
                                        "M j, Y" => aReview::FormatDate("M j, Y", $timestamp),//"Feb 22, 2007",
                                        "j F Y" => aReview::FormatDate("j F Y", $timestamp),//"22 February 2007",
                                        "j F Y, H:i" => aReview::FormatDate("j F Y, H:i", $timestamp),//"22 February 2007, 03:00",
                                        "Tj F Y, H:i" => aReview::FormatDate("Tj F Y, H:i", $timestamp),//"today, 03:00",
                                        "F j, Y" => aReview::FormatDate("F j, Y", $timestamp),//"February 22, 2007",
                                        "d-m-Y" => aReview::FormatDate("d-m-Y", $timestamp),//"22-02-2007",
                                        "d.m.Y" => aReview::FormatDate("d.m.Y", $timestamp),//"22.02.2007",
                        ),
        ),

        "LIST_TITLE" => array(
                        "PARENT" => "VISUAL",
                        "NAME" => GetMessage("IMPROVED_REVIEW_CMP_PM_LIST_TITLE"),
                        "TYPE" => "STRING",
                        "DEFAULT" => "",
        ),								
        "EMPTY_MESSAGE" => array(
                        "PARENT" => "VISUAL",
                        "NAME" => GetMessage("IMPROVED_REVIEW_CMP_PM_EMPTY_MESSAGE"),
                        "TYPE" => "STRING",
                        "DEFAULT" => GetMessage("IMPROVED_REVIEW_CMP_PM_EMPTY_MESSAGE_1"),
        ),

        "NAME_SHOW_TYPE" => array(
                        "PARENT" => "VISUAL",
                        "NAME" => GetMessage("IMPROVED_PM_NAME_SHOW_TYPE"),
                        "TYPE" => "LIST",
                        "ADDITIONAL_VALUES" => "N",
                        "VALUES" => Array(
                                "LOGIN"=>GetMessage("IMPROVED_PM_NAME_SHOW_TYPE_LOGIN"),
                                "NAME"=>GetMessage("IMPROVED_PM_NAME_SHOW_TYPE_NAME"),
                                "NAME_LAST_NAME"=>GetMessage("IMPROVED_PM_NAME_SHOW_TYPE_NAME_LAST_NAME"),
                                "LOGIN_NAME"=>GetMessage("IMPROVED_PM_NAME_SHOW_TYPE_LOGIN_NAME"),
                                "LOGIN_NAME_LAST_NAME"=>GetMessage("IMPROVED_PM_NAME_SHOW_TYPE_LOGIN_NAME_LAST_NAME")
                        ),
        ),
        "SHOW_UPLOAD_FILE" => array(
                        "PARENT" => "VISUAL",
                        "NAME" => GetMessage("IMPROVED_PM_ALLOW_UPLOAD_FILE"),
                        "TYPE" => "CHECKBOX",
                        "DEFAULT" => "N",
        ),
        "SHOW_AVATAR_TYPE" => array(
                        "PARENT" => "VISUAL",
                        "NAME" => GetMessage("IMPROVED_PM_SHOW_AVATAR"),
                        "TYPE" => "LIST",
                        "ADDITIONAL_VALUES" => "N",
                        "VALUES" => Array(
                                "ns"=>GetMessage("IMPROVED_PM_AVATAR_TYPE_0"),
                                "user"=>GetMessage("IMPROVED_PM_AVATAR_TYPE_1"),
                                "forum"=>GetMessage("IMPROVED_PM_AVATAR_TYPE_2"),
                                "blog"=>GetMessage("IMPROVED_PM_AVATAR_TYPE_3")
                        ),
        ),
        
        "AVATAR_WIDTH" => array(
                        "PARENT" => "VISUAL",
                        "NAME" => GetMessage("IMPROVED_PM_AVATAR_WIDTH"),
                        "TYPE" => "STRING",
                        "DEFAULT" => "80",
        ),
        "AVATAR_HEIGHT" => array(
                        "PARENT" => "VISUAL",
                        "NAME" => GetMessage("IMPROVED_PM_AVATAR_HEIGHT"),
                        "TYPE" => "STRING",
                        "DEFAULT" => "80",
        ),                                   
//PAGER                                
		"REVIEWS_ON_PAGE" => array(
			"PARENT" => "PAGE_SETTINGS",
			"NAME" => GetMessage("IMPROVED_REVIEW_CMP_PM_REVIEWS_SHOW"),
			"TYPE" => "STRING",
			"DEFAULT" => '15',
		),
        "CACHE_TIME"  =>  Array("DEFAULT"=>3600),                                                             
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
?>
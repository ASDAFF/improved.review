<?
/**
 * Copyright (c) 1/8/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
{
	ShowError(GetMessage("IMPROVED_CP_REVIEW_IBLOCK_MODULE_NOT_INSTALLED"));
	return;
}
if(!CModule::IncludeModule("improved.review"))
{
	ShowError(GetMessage("IMPROVED_CP_REVIEW_MODULE_NOT_INSTALLED"));
	return;	
}

$arParams['ALLOW_SET'] = ($arParams['ALLOW_SET']=='Y');

if($arParams['ALLOW_SET'] && $_SERVER["REQUEST_METHOD"]=="POST" && check_bitrix_sessid() && $_POST["ACTION"]=="SET_RATING" && (int)$_POST["RATING"]>0)
{
    $ELEMENT_ID = (int)$_POST["ELEMENT_ID"];
    $APPLICATION->RestartBuffer();
    if($ELEMENT_ID>0 && aReview::AllowSetRating($ELEMENT_ID))
    {
        $review = new aReview;
        $review->Add(Array("ELEMENT_ID"=>$ELEMENT_ID,"RATING"=>(int)$_POST["RATING"],"ONLY_RATING"=>"Y","IS_SEND"=>"Y",'USER_ID'=>$USER->GetID()),false);
        BXClearCache(true,"/".SITE_ID."/improved/review.rating/".$ELEMENT_ID."/");
        echo "ajRatingAction = true;";
    }
    else
    {
        echo "ajRatingAction = false;";
    }
    die();
}

$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);
$arParams["ELEMENT_ID"] = intval($arParams["ELEMENT_ID"]);
$arParams["ELEMENT_CODE"] = trim($arParams["ELEMENT_CODE"]);
if($arParams["SHOW_TITLE"]=="")
    $arParams["SHOW_TITLE"] = "Y";
    
if($arParams["SHOW_TITLE"]!="Y")
    $arParams["SHOW_TITLE"] = "N";

$arParams["SHOW_TITLE"] = $arParams["SHOW_TITLE"]=="Y" ? true : false;
$arParams["TITLE_TEXT"] = trim($arParams["TITLE_TEXT"]); 


$arComponentVariables = array("SECTION_ID", "ELEMENT_ID","ID", "SECTION_CODE", "ELEMENT_CODE", "CODE");
$arVariableAliases = array(
				"section" => array("SECTION_ID" => "SECTION_ID","SECTION_CODE" => "SECTION_CODE"),
				"detail" => array("SECTION_ID" => "SECTION_ID", "ELEMENT_ID" => "ELEMENT_ID","ELEMENT_ID" => "ID", 
					"SECTION_CODE" => "SECTION_CODE", "ELEMENT_CODE" => "ELEMENT_CODE", "ELEMENT_CODE" => "CODE"),
			);

if($arParams["IS_SEF"] === "Y")
{
	$arVariables = array();

	$engine = new CComponentEngine($this);
	if (CModule::IncludeModule('iblock'))
	{
		$engine->addGreedyPart("#SECTION_CODE_PATH#");
		$engine->setResolveCallback(array("CIBlockFindTools", "resolveComponentEngine"));
	}

	$componentPage = $engine->guessComponentPath(
		$arParams["SEF_BASE_URL"],
		array(
			"section" => $arParams["SECTION_PAGE_URL"],
			"detail" => $arParams["DETAIL_PAGE_URL"],
		),
		$arVariables
	);
	if($componentPage === "detail")
	{
		CComponentEngine::InitComponentVariables(
			$componentPage,
			$arComponentVariables,
			$arVariableAliases,
			$arVariables
		);
	}
}
else
{
	CComponentEngine::InitComponentVariables(false, $arComponentVariables, array("SECTION_ID" => "SECTION_ID", "ELEMENT_ID" => "ELEMENT_ID", "ELEMENT_ID" => "ID",
					"SECTION_CODE" => "SECTION_CODE", "ELEMENT_CODE" => "ELEMENT_CODE", "ELEMENT_CODE" => "CODE"), $arVariables);
}

$arParams["ELEMENT_ID"] = intval($arVariables["ELEMENT_ID"]);
$arParams["ELEMENT_CODE"] = $arVariables["ELEMENT_CODE"];

if($arParams["ELEMENT_ID"]==0 && intval($arParams["~ELEMENT_ID"])>0)
	$arParams["ELEMENT_ID"] = intval($arParams["~ELEMENT_ID"]);

if(strlen($arParams["ELEMENT_CODE"])==0 && strlen($arParams["~ELEMENT_CODE"])>0)
	$arParams["ELEMENT_CODE"] = trim($arParams["~ELEMENT_CODE"]);
	
$arFilter = Array(
	"IBLOCK_ID" => $arParams["IBLOCK_ID"],
);

if($arParams["ELEMENT_ID"] <= 0)
{
	$arParams["ELEMENT_ID"] = CIBlockFindTools::GetElementID(
		$arParams["ELEMENT_ID"],
		$arParams["ELEMENT_CODE"],
		false,
		false,
		array(
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"IBLOCK_LID" => SITE_ID,
			"IBLOCK_ACTIVE" => "Y",
			"ACTIVE" => "Y",
		)
	);
}
if($arParams["ELEMENT_ID"]>0)
{
    if($arParams['ALLOW_SET'])
        $arParams["ALLOW_VOTE"] = aReview::AllowSetRating($arParams["ELEMENT_ID"]);
}
if ($this->StartResultCache($arParams["CACHE_TIME"],$arParams["ALLOW_VOTE"],"/".SITE_ID."/improved/review.rating/".$arParams["ELEMENT_ID"]."/"))
{
		if($arParams["ELEMENT_ID"]>0)
		{
			if(defined("BX_COMP_MANAGED_CACHE"))
				$GLOBALS["CACHE_MANAGER"]->RegisterTag('review_element_'.$arParams["ELEMENT_ID"]);
			
			$arFilter = Array(
				"IBLOCK_ID"	 =>	  $arParams["IBLOCK_ID"],
				"ID" =>$arParams["ELEMENT_ID"],
			);
		
			$rsElement = CIBlockElement::GetList(Array(), $arFilter, false, false, Array("ID", "IBLOCK_ID"));
			if($arElement = $rsElement->GetNext())
			{
				$arResult = aReview::CalculateRating($arElement["ID"]);
			}
		}
		else
			$this->AbortResultCache();

		$this->IncludeComponentTemplate();
}
?>
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

$arParams["POST_DATE_FORMAT"] = trim($arParams["POST_DATE_FORMAT"]);
if(strlen($arParams["POST_DATE_FORMAT"]) <= 0)
        $arParams["POST_DATE_FORMAT"] = "j F Y, H:i";

$arParams["NAME_SHOW_TYPE"] = trim($arParams["NAME_SHOW_TYPE"]);
if(!in_array($arParams["NAME_SHOW_TYPE"],Array("LOGIN","NAME","LOGIN_NAME","NAME_LAST_NAME","LOGIN_NAME_LAST_NAME")))
    $arParams["NAME_SHOW_TYPE"] = "NAME_LAST_NAME";

$arParams["SHOW_AVATAR_TYPE"] = trim($arParams["SHOW_AVATAR_TYPE"]);
if(!in_array($arParams["SHOW_AVATAR_TYPE"],Array("user","forum","blog")))
    $arParams["SHOW_AVATAR_TYPE"] = false;

$arParams["AVATAR_WIDTH"] = (int)$arParams["AVATAR_WIDTH"];
if($arParams["AVATAR_WIDTH"]==0)
    $arParams["AVATAR_WIDTH"] = 80;
$arParams["AVATAR_HEIGHT"] = (int)$arParams["AVATAR_HEIGHT"];
if($arParams["AVATAR_HEIGHT"]==0)
    $arParams["AVATAR_HEIGHT"] = 80;

$arParams["COMMENTS_MODE"] = ($arParams["COMMENTS_MODE"] == "Y");
$arParams["CACHE_TIME"] = (int)$arParams["CACHE_TIME"];
if($arParams["CACHE_TIME"]==0)
	$arParams["CACHE_TIME"] = 86400000;
//nav
$arParams["REVIEWS_ON_PAGE"] = (int)$arParams["REVIEWS_ON_PAGE"];
if(empty($arParams['NAV_TEMPLATE']))
    $arParams['NAV_TEMPLATE'] = 'arrows';

$arNavParams = array(
	"nPageSize" => $arParams["REVIEWS_ON_PAGE"],
	"bDescPageNumbering" => $arParams["PAGER_DESC_NUMBERING"],
	"bShowAll" => false,
);
$arNavigation = CDBResult::GetNavParams($arNavParams);
use IMPROVED\Review;
use Bitrix\Main;

if($this->StartResultCache(false, array($arNavigation)))
{

$arResult = array();
$filter = array('ONLY_RATING'=>'N','APPROVED'=>'Y','SITE_ID'=>SITE_ID);
$data = Review\ReviewTable::getList(array(
    'select' => array('ID','ELEMENT_ID','USER_ID','SITE_ID','AUTHOR_NAME','DATE','POST_DATE','MESSAGE_PLUS_HTML','MESSAGE_MINUS_HTML','MESSAGE_HTML',
                'TITLE','VOTE_MINUS','VOTE_PLUS','USER.LOGIN','USER.NAME','USER.LAST_NAME','REPLY_HTML','VOTE','RATING'),
    'filter' => $filter,
    'order' => array('ID'=>'DESC'),
    'limit' => $arParams["REVIEWS_ON_PAGE"],
    'offset' => ($arNavigation['PAGEN']-1) * $arParams["REVIEWS_ON_PAGE"]    
));
$result = new CDBResult($data);
$result->NavStart($arParams["REVIEWS_ON_PAGE"]);

while($Review = $result->fetch())
{
    $Review['POST_DATE'] = $Review['POST_DATE']->toString();
    
    $Review["SHOW_USER_PATH"] = '';
    if(strlen($arParams['USER_PATH'])>0 && $Review["USER_ID"])
    {
        $Review["SHOW_USER_PATH"] = CComponentEngine::MakePathFromTemplate($arParams['USER_PATH'],Array("USER_ID"=>$Review["USER_ID"]));
    }    
    
    if($Review["USER_ID"]>0)
    {
       switch($arParams["NAME_SHOW_TYPE"])
       {
            case "LOGIN":
                $Review["AUTHOR_NAME"] = $Review["IMPROVED_REVIEW_REVIEW_USER_LOGIN"]; 
            break;
        
            case "NAME":
                $Review["AUTHOR_NAME"] = $Review["IMPROVED_REVIEW_REVIEW_USER_NAME"]; 
            break;
            case "NAME_LAST_NAME":
                $Review["AUTHOR_NAME"] = $Review["IMPROVED_REVIEW_REVIEW_USER_NAME"]." ".$Review["IMPROVED_REVIEW_REVIEW_USER_LAST_NAME"];                                
            break;
            case "LOGIN_NAME":
                $Review["AUTHOR_NAME"] = "(".$Review["IMPROVED_REVIEW_REVIEW_USER_LOGIN"].") ".$Review["IMPROVED_REVIEW_REVIEW_USER_NAME"];
            break;                                                                                    
            case "LOGIN_NAME_LAST_NAME":
                $Review["AUTHOR_NAME"] = "(".$Review["IMPROVED_REVIEW_REVIEW_USER_LOGIN"].") ".$Review["IMPROVED_REVIEW_REVIEW_USER_NAME"]." ".$Review["IMPROVED_REVIEW_REVIEW_USER_LAST_NAME"];
            break;                                                                
       } 
    }    
    $ts = MakeTimeStamp($Review["DATE"], CSite::GetDateFormat());			 
	$Review["POST_DATE_FORMAT"] = aReview::FormatDate($arParams["POST_DATE_FORMAT"],$ts);
    
    if($arParams["SHOW_AVATAR_TYPE"]!=false && $Review["USER_ID"]>0)
    {
        $Review["USER_AVATAR"] = aReview::GetAvatar($Review["USER_ID"],$arParams["SHOW_AVATAR_TYPE"],Array("width"=>$arParams["AVATAR_WIDTH"],"height"=>$arParams["AVATAR_HEIGHT"]));
    }  
    
    $Review["USER_FIELDS"] = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields("IMPROVED_REVIEW",$Review["ID"],LANGUAGE_ID);
    $Review["USER_FIELDS_RATING"] = Array();
    foreach($Review["USER_FIELDS"] as $k=>$arUF)
    {
            
        if($arUF['USER_TYPE_ID']=='IMPROVED_REVIEW_RATING')
        {
            if(empty($arParams["UF_VOTE"]) || (count($arParams["UF_VOTE"])>0 && !in_array($k,$arParams["UF_VOTE"])))
                continue;
                                        
            $Review["USER_FIELDS_RATING"][$k] = $arUF;
            unset($Review["USER_FIELDS"][$k]);
        }
        else
        {
            if(count($arParams["UF"])>0 && !in_array($k,$arParams["UF"]))
                continue;                        
        }
    }    
    
    $Review['ELEMENT'] = aReview::GetElementInfo($Review['ELEMENT_ID']);
    $arResult['ITEMS'][] = $Review;      
}

$countQuery = new Bitrix\Main\Entity\Query(Review\ReviewTable::getEntity());
$countQuery->registerRuntimeField("CNT", array(
                "data_type" => "integer",
                "expression" => array("COUNT(1)")
                )
            )
            ->setSelect(array("CNT"))
            ->setFilter($filter);   
$totalCount = $countQuery->setLimit(null)->setOffset(null)->exec()->fetch();        
$totalCount = intval($totalCount['CNT_A']);
$totalPage = ceil($totalCount/$arParams["REVIEWS_ON_PAGE"]);
$result->NavRecordCount = $totalCount;
$result->NavPageCount = $totalPage;
$result->NavPageNomer = $arNavigation['PAGEN'];
$arResult["NAV_STRING"] = $result->GetPageNavString('', $arParams['NAV_TEMPLATE']);
$arResult["NAV_PARAMS"] = $result->GetNavParams();
$arResult["NAV_NUM"] = $result->NavNum;
$this->IncludeComponentTemplate();
}
?>
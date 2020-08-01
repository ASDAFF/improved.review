<?
/**
 * Copyright (c) 1/8/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!CModule::IncludeModule("improved.review"))
{
	ShowError(GetMessage("IMPROVED_CP_REVIEW_MODULE_NOT_INSTALLED"));
	return;	
}

$arParams["ELEMENT_ID"] = (int)$arParams["ELEMENT_ID"];
if($arParams["ELEMENT_ID"]==0)
{
    return;
}
$arParams["ALLOW_SUBS"] = ($arParams["ALLOW_SUBS"] == "Y"); 
$arParams["ALLOW_SUBS_AUTH_ONLY"] = ($arParams["ALLOW_SUBS_AUTH_ONLY"] == "Y");

if(!$arParams["ALLOW_SUBS"])
    return;

if($arParams["ALLOW_SUBS_AUTH_ONLY"] && !$USER->IsAuthorized())
    return;

if($_SERVER["REQUEST_METHOD"] == "POST" && check_bitrix_sessid() && $_POST["SUBS"]=="Y" && in_array($_POST["ACTION"],Array("SUBS","UNSUBS","CHECK")))
{
    $sub = new aReviewSubs(); 
    $result = false;           
    if($_POST["ACTION"]==="CHECK")
    {
        $APPLICATION->RestartBuffer();

        if($USER->IsAuthorized())
            $Email = $USER->GetEmail();
        else
            $Email = $_POST["EMAIL"];
        
		if (!$USER->IsAuthorized() && !$APPLICATION->CaptchaCheckCode($_POST["captcha_word"], $_POST["captcha_sid"]))
		{
    		$arSubResult = Array("TYPE"=>false,"ERR"=>GetMessage("IMPROVED_CP_WRONG_CAPTCHA")."<br>");
    		echo "subRes = " .CUtil::PhpToJSObject($arSubResult);
            die();            
		}
        
        $res = $sub->CheckFields($arParams["ELEMENT_ID"],$Email);
        if($res)
        {
    		$arSubResult = Array("TYPE"=>true);
    		echo "subRes = " .CUtil::PhpToJSObject($arSubResult);            
        }
        else
        {
    		$arSubResult = Array("TYPE"=>false,"ERR"=>$sub->LAST_ERROR);
    		echo "subRes = " .CUtil::PhpToJSObject($arSubResult);            
        }
        die();
    }
    if($_POST["ACTION"]==="SUBS")
    {
        if($USER->IsAuthorized())
            $Email = $USER->GetEmail();
        else
            $Email = $_POST["EMAIL"];
                               
        $res = $sub->Add($arParams["ELEMENT_ID"],$Email);
        if($res)
        {
            $APPLICATION->set_cookie("REVIEW_AUTHOR_EMAIL", $Email, false, "/", false, false, true, "IMPROVED_REVIEW");
            $result = true;    
        }
        else
        {
            ShowError($sub->LAST_ERROR);
        }
    }
    
    if($_POST["ACTION"]==="UNSUBS")
    {
        if($USER->IsAuthorized())
            $Email = $USER->GetEmail();
        else
            $Email = $APPLICATION->get_cookie("REVIEW_AUTHOR_EMAIL", "IMPROVED_REVIEW");
            
        if(strlen($Email)>0 && check_email($Email))
        {
            aReviewSubs::Delete($arParams["ELEMENT_ID"],$Email);
        }    
        $result = true;
    }   
     
    if($result)
        LocalRedirect($APPLICATION->GetCurPageParam());
}
if($USER->IsAuthorized())
    $Email = $USER->GetEmail();
else
    $Email = $APPLICATION->get_cookie("REVIEW_AUTHOR_EMAIL", "IMPROVED_REVIEW");

if(strlen($Email)>0)
    $arResult["IS_SUB"] = aReviewSubs::IsSubs($arParams["ELEMENT_ID"],$Email);
      
$this->IncludeComponentTemplate();
CUtil::InitJSCore(array('window','ajax'));
?>
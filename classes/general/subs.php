<?
/**
 * Copyright (c) 1/8/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

IncludeModuleLangFile(__FILE__);
class aReviewSubsMain
{
    Function CheckFields($ELEMENT_ID,$EMAIL)
    {
        $this->LAST_ERROR = "";
        
        if(strlen($EMAIL)==0 || !check_email($EMAIL))
            $this->LAST_ERROR .= GetMessage("IMPROVED_REVIEW_SUBS_ERR_EMAIL")."<br />";
            
        if($ELEMENT_ID==0)
            $this->LAST_ERROR .= GetMessage("IMPROVED_REVIEW_SUBS_ERR_ELEMENT")."<br />";
        
        if(strlen($this->LAST_ERROR)==0 && aReviewSubs::IsSubs($ELEMENT_ID,$EMAIL))
            $this->LAST_ERROR .= GetMessage("IMPROVED_REVIEW_SUBS_EMAIL_EX")."<br />";
            
        if(strlen($this->LAST_ERROR)>0)
            return false;
    
    return true;
    }
}
?>
<?
/**
 * Copyright (c) 1/8/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

IncludeModuleLangFile(__FILE__);
class aReviewMain
{
	Function GetByID($ID,$select = array('*'))
	{
            return IMPROVED\Review\ReviewTable::getList(array(
                    'select' => $select,
                    'filter' => Array("ID"=>$ID),
            ));
	}
    /* lib tools*/
	Function ClearCacheFull($ID)
	{		
		$obReview = self::GetByID($ID,Array("ELEMENT_ID"));
		if($arReview = $obReview->Fetch())
			self::ClearCache($arReview["ELEMENT_ID"]);
        else
            self::ClearCache(0,SITE_ID);
	}
	Function ClearCache($ELEMENT_ID,$SITE_ID = SITE_ID)
	{
			if($ELEMENT_ID>0)
			{
				BXClearCache(true, "/".$SITE_ID."/improved/review.list/".$ELEMENT_ID."/");
				BXClearCache(true, "/".$SITE_ID."/improved/review.rating/".$ELEMENT_ID."/");
			}	
			else
			{
				BXClearCache(true, "/".$SITE_ID."/improved/review.list/");
				BXClearCache(true, "/".$SITE_ID."/improved/review.rating/");
			}
	return true;
	}    
    /* */
	Function GetAllowTags()
	{
            return array("HTML" => "N", 
                        "ANCHOR" => COption::GetOptionString("improved.review", "FORM_ALLOW_ANCHOR", "Y"),
                        "BIU" => COption::GetOptionString("improved.review", "FORM_ALLOW_BIU", "Y"),
                        "IMG" => COption::GetOptionString("improved.review", "FORM_ALLOW_IMG", "Y"),
                        "QUOTE" => COption::GetOptionString("improved.review", "FORM_ALLOW_QUOTE", "Y"),
                        "CODE" => COption::GetOptionString("improved.review", "FORM_ALLOW_CODE", "N"),
                        "FONT" => COption::GetOptionString("improved.review", "FORM_ALLOW_FONT", "N"),
                        "LIST" => COption::GetOptionString("improved.review", "FORM_ALLOW_LIST", "Y"),
                        "SMILES" => COption::GetOptionString("improved.review", "FORM_ALLOW_SMILE", "Y"),
                        "NL2BR" => COption::GetOptionString("improved.review", "FORM_ALLOW_NL2BR", "Y"),
                        "VIDEO" => COption::GetOptionString("improved.review", "FORM_ALLOW_VIDEO", "N"),
                        "TABLE" => COption::GetOptionString("improved.review", "FORM_ALLOW_TABLE", "N"),
                        "CUT_ANCHOR" => "N", 
                        "ALIGN" => COption::GetOptionString("improved.review", "FORM_ALLOW_ALIGN", "Y")
            );        
	}
        Function ShowLHE($ID,$CONTENT,$INPUT,$height = 100,$jsObjName = "oLHErw")
        {
    		$arEditorFeatures = array(
                "ALIGN" => array('JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyFull'),
    			"BIU" => array('Bold', 'Italic', 'Underline', 'Strike'),
    			"FONT" => array('ForeColor','FontList', 'FontSizeList'),
    			"QUOTE" => array('Quote'),
    			"CODE" => array('Code'),
    			'ANCHOR' => array('CreateLink', 'DeleteLink'),
    			"IMG" => array('Image'),
    			"TABLE" => array('Table'),
    			"LIST" => array('InsertOrderedList', 'InsertUnorderedList'),
    			"NL2BR" => array(''),
                "VIDEO" => array('Video'),
                "SMILES" => array('SmileList'),
    		);
            $toolbarConfig = Array();
            $arAllowTags = self::GetAllowTags();
            foreach ($arEditorFeatures as $featureName => $toolbarIcons)
            {
                if($arAllowTags[$featureName]=="Y")
                    $toolbarConfig = array_merge($toolbarConfig, $toolbarIcons);
            }
    		$toolbarConfig = array_merge($toolbarConfig, array('RemoveFormat', 'Translit', 'Source'));
            
            $arSmiles = array();
            $arrSmiles = aReviewTextParser::GetSmilesBase();
            foreach($arrSmiles as $name=>$arSmile)
            {
                    $arSmiles[] = array(
                            'name' => $arSmile["DESCR"],
                            'path' => $arSmile["URL"],
                            'code' => $arSmile["CODE"],
                            'width'=> $arSmile["IMAGE_WIDTH"],
                            'height'=> $arSmile["IMAGE_HEIGHT"],
                    );
            }
			CModule::IncludeModule("fileman");
			$LHE = new CLightHTMLEditor();
			$arEditorParams = array(
				'id' => $ID,
				'content' => htmlspecialcharsback($CONTENT),
				'inputName' => $INPUT,
				'inputId' => $INPUT,
				'width' => "100%",
				'height' => $height."px",
				'minHeight' => $height."px",
				'bUseFileDialogs' => false,
				'bUseMedialib' => false,
				'BBCode' => true,
				'bBBParseImageSize' => true,
				'jsObjName' => $jsObjName,
                'arSmiles' => $arSmiles,
                //'smileCountInToolbar' => 3,
				'bQuoteFromSelection' => true,
				'bSetDefaultCodeView' => false,
				'bResizable' => true,
				'bAutoResize' => true,
                'autoResizeOffset' => 40,
                'toolbarConfig' =>$toolbarConfig,
                'bHandleOnPaste' => false,
			);  
            $LHE->Show($arEditorParams);              
        }    
        
		Function Index($ID,$arFields,$bOverWrite = false)
		{
			$ID = (int)$ID;
			if($ID==0)
				return;
			
			if(CModule::IncludeModule("search") && COption::GetOptionString("improved.review","indexing","Y")=="Y")
			{
					if(!$arRevew = self::GetByID($ID,Array("ID","ELEMENT_ID","SITE_ID"))->fetch())
						return;
					
					if(!isset($arFields["ELEMENT_ID"]) || (int)$arFields["ELEMENT_ID"]==0)
						$arFields["ELEMENT_ID"] = $arRevew["ELEMENT_ID"];
						
					$arIblockElement = aReview::GetElementInfo($arFields["ELEMENT_ID"]);
					
					$arGroups = Array();

					if(isset($arFields["APPROVED"]) && $arFields["APPROVED"]!="Y")
						$arGroups = aReview::GetModGroups();
						
					if(count($arGroups)==0 && $arFields["APPROVED"]=="Y")
						$arGroups = Array(2);
						
					$arIndex = Array(
						"DATE_CHANGE"=>$arFields["POST_DATE"],
						"LAST_MODIFIED"=>$arFields["POST_DATE"],
						"TITLE"=>GetMessage("IMPROVED_REVIEW_INDEX_MESS",Array("#ELEMENT_NAME#"=>$arIblockElement["~NAME"])),
						"SITE_ID"=>$arRevew["SITE_ID"],
						"PARAM1"=>"",
						"PARAM2"=>"",
						"BODY"=> $arFields["MESSAGE_PLUS"]."\n".$arFields["MESSAGE_MINUS"]."\n".$arFields["MESSAGE"],
						"PERMISSIONS"=>$arGroups,
						"URL"=>$arIblockElement["~DETAIL_PAGE_URL"]."#review".$ID
					);

					CSearch::Index("improved.review",$ID,$arIndex,$bOverWrite);
			}
		}
		
		Function ReIndex($ID)
		{
            if($arRevew = self::GetByID($ID,Array("POST_DATE","ELEMENT_ID","MESSAGE_PLUS","MESSAGE_MINUS","MESSAGE","APPROVED"))->fetch())
            {
                aReview::Index($ID,$arRevew,true);
            }
		}        
        
        Function GetElementIdById($ID)
        {
            if($arRevew = self::GetByID($ID,Array("ELEMENT_ID"))->fetch())
                return $arRevew["ELEMENT_ID"];
            else  
                return 0;
        }
    ///////////////////old    
	Function CheckFields(&$arFields)
	{
		$this->LAST_ERROR = "";
	
		if(is_set($arFields, "USER_ID") && (int)$arFields["USER_ID"]==0)
		{
			if(strlen($arFields["AUTHOR_NAME"])==0)
				$this->LAST_ERROR .= GetMessage("IMPROVED_REVIEW_EMPTY_AUTHOR_NAME_PUB")."<br>";

			if(strlen($arFields["AUTHOR_EMAIL"])==0)
			{
				$this->LAST_ERROR .= GetMessage("IMPROVED_REVIEW_EMPTY_AUTHOR_EMAIL")."<br>";
			}
			elseif (!check_email($arFields["AUTHOR_EMAIL"]))
			{
				$this->LAST_ERROR .= GetMessage("IMPROVED_REVIEW_EMPTY_AUTHOR_BAD_EMAIL")."<br>";
			}
			else
			{
				if(CUser::GetList(($by="personal_country"), ($order="desc"), Array("EMAIL"=>$arFields["AUTHOR_EMAIL"]))->Fetch())
					$this->LAST_ERROR .= GetMessage("IMPROVED_REVIEW_CF_MAIL_BUSY")."<br>";
			}
		}
		
		$arBadWords = explode("\n",COption::GetOptionString("improved.review", "npcw", ""));
		
		if(is_set($arFields, "MESSAGE_MINUS") && strlen($arFields["MESSAGE_MINUS"])>0)
		{
			
			if(count($arBadWords)>0)
			{
				foreach($arBadWords as $BadWord)
				{
					$BadWord = trim($BadWord);
					if(stristr($arFields["MESSAGE_MINUS"],$BadWord))
					{                                                
						$this->LAST_ERROR .= GetMessage("IMPROVED_REVIEW_COMMENTS_MESSAGE_MINUS_BAD_WORD",Array("#B_WORD#"=>$BadWord))."<br>";
					}
				}
			}			
		}
		
		if(is_set($arFields, "MESSAGE_PLUS") && strlen($arFields["MESSAGE_PLUS"])<=0)
			$this->LAST_ERROR .= GetMessage("IMPROVED_REVIEW_EMPTY_MESSAGE_PLUS")."<br>";
		elseif(strlen($arFields["MESSAGE_PLUS"])>0)
		{
			if(count($arBadWords)>0)
			{
				foreach($arBadWords as $BadWord)
				{
					$BadWord = trim($BadWord);
					if(stristr($arFields["MESSAGE_PLUS"],$BadWord))
					{                                                
						$this->LAST_ERROR .= GetMessage("IMPROVED_REVIEW_COMMENTS_MESSAGE_PLUS_BAD_WORD",Array("#B_WORD#"=>$BadWord))."<br>";
					}
				}
			}
		}

		if(is_set($arFields, "MESSAGE") && strlen($arFields["MESSAGE"])<=0)
			$this->LAST_ERROR .= GetMessage("IMPROVED_REVIEW_EMPTY_MESSAGE")."<br>";
		elseif(strlen($arFields["MESSAGE"])>0)
		{
			if(count($arBadWords)>0)
			{
				foreach($arBadWords as $BadWord)
				{
					$BadWord = trim($BadWord);
					if(stristr($arFields["MESSAGE"],$BadWord))
					{                                                
						$this->LAST_ERROR .= GetMessage("IMPROVED_REVIEW_COMMENTS_MESSAGE_BAD_WORD",Array("#B_WORD#"=>$BadWord))."<br>";
					}
				}
			}
		}
	
		if(intval($arFields["ELEMENT_ID"])==0)
			$this->LAST_ERROR .= GetMessage("IMPROVED_REVIEW_IBLOCK_ELEMENT_NOT_FOUND")."<br>";
	
	if(strlen($this->LAST_ERROR)>0)
		return false;
	return true;
	}

	Function FormatDate($format, $timestamp)
	{
		if(LANG=="en")
			return date($format, $timestamp);
		elseif(preg_match_all("/[FMlD]/", $format, $matches))
		{
			$ar = preg_split("/[FMlD]/", $format);
			$result = "";

		if(preg_match("/(j )(F)/", $format, $match))
		{
			if(preg_match("/^T/", $format))
			{
				if(ConvertTimeStamp($timestamp) == ConvertTimeStamp())
					$result = GetMessage("IMPROVED_REVIEW_TODAY");
				elseif(ConvertTimeStamp($timestamp) == ConvertTimeStamp(time()-86400))
					$result = GetMessage("IMPROVED_REVIEW_YESTERDAY");
			}

			if($result !="")
			{
				$ar[count($ar)-1] = preg_replace("/ ?[TYy]/", "", $ar[count($ar)-1]);
			}
			else
				$result = date("j", $timestamp)." ".GetMessage("IMPROVED_REVIEW_PMONTH_".date("n", $timestamp));
		}
		else
		{
			foreach($matches[0] as $i=>$match)
			{
				switch($match)
				{
								case "F":$match=GetMessage("IMPROVED_REVIEW_MONTH_".date("n", $timestamp));break;
								case "M":$match=GetMessage("IMPROVED_REVIEW_MON_".date("n", $timestamp));break;
								case "l":$match=GetMessage("IMPROVED_REVIEW_DAY_OF_WEEK_".date("w", $timestamp));break;
								case "D":$match=GetMessage("IMPROVED_REVIEW_DOW_".date("w", $timestamp));break;
				}
				$result .= date($ar[$i], $timestamp).$match;
			}
		}
		$result .= date($ar[count($ar)-1], $timestamp);
		return $result;
		}
		else
			return date($format, $timestamp);
	}



	Function GetByElementID($ELEMENT_ID)
	{
		return  aReview::GetList(Array(), Array("ELEMENT_ID" => IntVal($ELEMENT_ID)),Array(),$arSelect);
	}

    Function OnAfterIBlockElementDelete($arElement)
    {
            return IMPROVED\Review\ReviewTable::deleteByElement($arElement["ID"]);
    }
		
		
		Function GetElementInfo($ELEMENT_ID)
		{
			if(!CModule::IncludeModule("iblock"))
				return false;
				
			$ELEMENT_ID = (int)$ELEMENT_ID;
			if($ELEMENT_ID ==0)
				return false;
				
			$obIBlockElement = CIBlockElement::GetList(Array(), Array("ID" => $ELEMENT_ID), false, false, Array("DETAIL_PAGE_URL", "ID", "IBLOCK_ID", "NAME"));
			if($arIBlockElement = $obIBlockElement->GetNext())
				return $arIBlockElement;
				
		return false;
		}
		
		///new

		
		Function GetAuthorName($ID)
		{
                $ID = (int)$ID;
                if($ID==0)
                        return false;
				
				$ob = aReview::GetByID($ID,Array("AUTHOR_NAME"));
				$ar = $ob->Fetch();
			return $ar["AUTHOR_NAME"];
		}
		Function GetEmail($ID)
		{
                $ID = (int)$ID;
                if($ID==0)
                        return false;
				
				$ob = aReview::GetByID($ID,Array("AUTHOR_EMAIL"));
				$ar = $ob->Fetch();
			return $ar["AUTHOR_EMAIL"];
		}
		Function GetPostDate($ID)
		{
                $ID = (int)$ID;
                if($ID==0)
                        return false;
				
				$ob = aReview::GetByID($ID,Array("POST_DATE"));
				$ar = $ob->Fetch();
			return $ar["POST_DATE"];
		}
		
		Function GetSendEmails($arGroups)
		{
			global $APPLICATION;
			
				$arEmails = Array();
				//moderators
				$arGroups = array_merge($arGroups,aReview::GetModGroups());
				
				if(count($arGroups)>0)
				{
					$obMUsers = CUser::GetList(($by="id"), ($order="desc"), Array("GROUPS_ID"=>$arGroups));
					while($arMUser = $obMUsers->Fetch())
						$arEmails[] = $arMUser["EMAIL"];
				}
								
				//admin
				$email_admin = COption::GetOptionString("improved.review", "email_admin");
				if(strlen($email_admin)>0)
					$arEmails[] = $email_admin;
		
                array_unique($arEmails);
		return $arEmails;
		}
		
		Function GetModGroups()
		{
			global $APPLICATION;
			$arGroups = Array();
			
			$obR = $APPLICATION->GetGroupRightList(Array("MODULE_ID"=>"improved.review"));
			while($arR = $obR->Fetch())
				if($arR["G_ACCESS"]>="M")
					$arGroups[] = $arR["GROUP_ID"];
		return $arGroups;
		}
		

		
		Function SaveRatingToIB($ELEMENT_ID,$IBLOCK_ID,$PROPERTY)
		{
            $ELEMENT_ID = (int)$ELEMENT_ID;
			$IBLOCK_ID = (int)$IBLOCK_ID;
			$PROPERTY = trim($PROPERTY);
            if($ELEMENT_ID==0 || $PROPERTY=="" || $IBLOCK_ID==0)
                    return false;
			
			CModule::IncludeModule("iblock");
			if(!CIBlockProperty::GetList(Array(), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$IBLOCK_ID,"CODE"=>$PROPERTY))->Fetch())
			{
				$arFields = Array("NAME" =>$PROPERTY,"ACTIVE" =>"Y","CODE" => $PROPERTY,"PROPERTY_TYPE" => "S","IBLOCK_ID" => $IBLOCK_ID);
				$ibp = new CIBlockProperty;
				$PropID = $ibp->Add($arFields);
				if(!$PropID)
					return false;
			}
				CIBlockElement::SetPropertyValues($ELEMENT_ID, $IBLOCK_ID, aReview::CalculateRating($ELEMENT_ID), $PROPERTY);			
		}
        
        Function SendSubsEmail($ID,$arEventFields = Array())
        {
            $ID = (int)$ID;
            if($ID==0)
                return false;
            
            $siteID = SITE_ID;
            if(count($arEventFields)==0)
            {
                $arEventFields = Array();
                if(!$arReview = aReview::GetByID($ID,Array('*','USER'))->Fetch())
                    return false;
                else
                {
                    $arEventFields = $arReview;
                    $siteID = $arReview["SITE_ID"];                                         
                    $arEventFields["REVIEW_ID"] = $ID;
					$arElementInfo = aReview::GetElementInfo($arReview["ELEMENT_ID"]);
					$arEventFields["IBLOCK_ELEMENT_ID"] = $arReview["ELEMENT_ID"];
					if($arElementInfo)
					{
						$arEventFields["IBLOCK_ELEMENT_NAME"] = $arElementInfo["NAME"];
						$arEventFields["IBLOCK_ELEMENT_IBLOCK_ID"] = $arElementInfo["IBLOCK_ID"];
						$arEventFields["IBLOCK_ELEMENT_DETAIL_PAGE_URL"] = $arElementInfo["DETAIL_PAGE_URL"];
					}    
                    $arEventFields["POST_URL"] = $arEventFields["IBLOCK_ELEMENT_DETAIL_PAGE_URL"]."#review".$ID;
                                       
                    if($arReview["IMPROVED_REVIEW_REVIEW_USER_ID"]>0)
                    {
                        $arEventFields["AUTHOR"] = $arReview["IMPROVED_REVIEW_REVIEW_USER_NAME"];
                        $arEventFields["AUTHOR_EMAIL"] = $arReview["IMPROVED_REVIEW_REVIEW_USER_EMAIL"];
                    }
                    else
                    {
                        $arEventFields["AUTHOR"] = $arReview["AUTHOR_NAME"];
                        $arEventFields["AUTHOR_EMAIL"] = $arReview["AUTHOR_EMAIL"];
                    }   
                    
                    $arEventFields["MESSAGE_BODY"] = "";
                    if(strlen($arReview['MESSAGE_PLUS'])>0)
                    {
                        $arEventFields["MESSAGE_BODY"] .= GetMessage('IMPROVED_CP_EV_PLUS').CEvent::GetMailEOL().$arReview['MESSAGE_PLUS'].CEvent::GetMailEOL();
                    }
                    
                    if(strlen($arReview['MESSAGE_MINUS'])>0)
                    {
                        $arEventFields["MESSAGE_BODY"] .= GetMessage('IMPROVED_CP_EV_MINUS').CEvent::GetMailEOL().$arReview['MESSAGE_MINUS'].CEvent::GetMailEOL();
                    }

                    if(strlen($arReview['MESSAGE'])>0)
                    {
                        $arEventFields["MESSAGE_BODY"] .= GetMessage('IMPROVED_CP_EV_COMMENT').CEvent::GetMailEOL().$arReview['MESSAGE'];
                    }
                                                             
                }
            }
            
            if(strlen($arEventFields["EMAIL_FROM"])==0)    
                $arEventFields["EMAIL_FROM"] = COption::GetOptionString("improved.review", "email_from");

            $db_events = GetModuleEvents("improved.review", "OnBeforeSendSubMail");
            while($arEvent = $db_events->Fetch())
            if(ExecuteModuleEventEx($arEvent, array(&$arEventFields))===false)
                return false;

            $obS = aReviewSubs::GetList(Array("ELEMENT_ID"=>$arEventFields["IBLOCK_ELEMENT_ID"]));
            while($arS = $obS->Fetch())
            {
                $arEventFields["EMAIL"] = $arS["EMAIL"];
                CEvent::Send("IMPROVED_REVIEW_ADD", $siteID, $arEventFields,"N");
            }
            aReview::SetSend($ID);
        }
}
?>
<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use IMPROVED\Review;

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
$arParams["ONLY_AUTH_SEND"] = $arParams["ONLY_AUTH_SEND"]=="Y";
if($arParams["ONLY_AUTH_SEND"] && !$USER->IsAuthorized())
{
    $back_url = $APPLICATION->GetCurPageParam();
    if(strlen($arParams['REG_URL'])==0)
        $arParams['REG_URL'] = '/auth/?register=yes';
        
	echo GetMessage("IMPROVED_REVIEW_ADD_CP_ONLY_AUTH_SEND",array("#REG_URL#"=>$arParams['REG_URL'].(strstr($arParams['REG_URL'],"?") ? "&" : "?")."backurl=".$back_url));
	return;
}
$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);
$arParams["ELEMENT_ID"] = intval($arParams["ELEMENT_ID"]);
$arParams["ELEMENT_CODE"] = trim($arParams["ELEMENT_CODE"]);
if(!$arParams["USE_CAPTCHA"]) $arParams["USE_CAPTCHA"] = "Y";
$arParams["USE_CAPTCHA"] = ($arParams["USE_CAPTCHA"] == "Y" && !$USER->IsAuthorized()) ? "Y" : "N";
$arParams["ADD_TITLE"] = trim($arParams["ADD_TITLE"]);
$arParams["SAVE_RATING"] = ($arParams["SAVE_RATING"] == "Y");
$arParams["SAVE_RATING_IB_PROPERTY"] = trim($arParams["SAVE_RATING_IB_PROPERTY"]);

$arParams["SAVE_COUNT"] = ($arParams["SAVE_COUNT"] == "Y");
$arParams["SAVE_COUNT_IB_PROPERTY"] = trim($arParams["SAVE_COUNT_IB_PROPERTY"]);

$arParams["ALLOW_TITLE"] = $arParams["ALLOW_TITLE"]=="Y";
if(!isset($arParams["SHOW_MAIN_RATING"]))
    $arParams["SHOW_MAIN_RATING"] = 'Y';
    
$arParams["SHOW_MAIN_RATING"] = $arParams["SHOW_MAIN_RATING"]=="Y";
$arParams["REQUIRED_RATING"] = $arParams["REQUIRED_RATING"]=="Y";
$arParams["COMMENTS_MODE"] = ($arParams["COMMENTS_MODE"] == "Y");
$arParams["NOT_HIDE_FORM"] = ($arParams["NOT_HIDE_FORM"] == "Y");
$arParams["MODERATE"] = ($arParams["MODERATE"]=="Y");
$arParams["SHOW_CNT"] = ($arParams["SHOW_CNT"]=="Y");
$arParams['ALLOW_UPLOAD_FILE'] = ($arParams['ALLOW_UPLOAD_FILE']=='Y');
if(!isset($arParams['TITLE_MIN_LENGTH']))
    $arParams['TITLE_MIN_LENGTH'] = 5;
if(!isset($arParams["TITLE_MAX_LENGTH"]))
    $arParams["TITLE_MAX_LENGTH"] = 255;
    
$arParams['SEND_NOTIFY'] = ($arParams['SEND_NOTIFY']=='Y');
$arParams['TITLE_MIN_LENGTH'] = (int)$arParams['TITLE_MIN_LENGTH'];
$arParams['TITLE_MAX_LENGTH'] = (int)$arParams['TITLE_MAX_LENGTH'];
$arParams["ALLOW_EDIT"] = ($APPLICATION->GetGroupRight("improved.review") >= "M")? true : false;    
if(!$arParams["ALLOW_EDIT"])
{
	if(!is_array($arParams["MOD_GOUPS"]))
		$arParams["MOD_GOUPS"] = array();

	$arGroups = $USER->GetUserGroupArray();

	if(count($arParams["MOD_GOUPS"])>0)
	{
		$arParams["ALLOW_EDIT"] = (count(array_intersect($arGroups, $arParams["MOD_GOUPS"])) > 0);
	}
}

$arUFBase = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields("IMPROVED_REVIEW",0,LANGUAGE_ID);
$arParams["USER_FIELDS"] = array();
$arParams["USER_FIELDS_RATING"] = Array();
foreach($arUFBase as $k=>$arUF)
{
    if($arUF['USER_TYPE_ID']=='IMPROVED_REVIEW_RATING')
    {
        if(in_array($k,$arParams["UF_VOTE"]))
            $arParams["USER_FIELDS_RATING"][$k] = $arUF;
    }
    else
    {
        if(in_array($k,$arParams["UF"]))
            $arParams["USER_FIELDS"][$k] = $arUF;
    }
}
$arParams['SHOW_VOTE_BLOCK'] = true;
if(!$arParams['SHOW_MAIN_RATING'] && count($arParams["USER_FIELDS_RATING"])==0)
    $arParams['SHOW_VOTE_BLOCK'] = false;
    
if($arParams["ELEMENT_ID"]==0 && strlen($arParams["ELEMENT_CODE"])==0)
{
    $arComponentVariables = array("SECTION_ID", "ELEMENT_ID","ID", "SECTION_CODE", "ELEMENT_CODE", "CODE");
    $arVariableAliases = array(
    				"section" => array("SECTION_ID" => "SECTION_ID","SECTION_CODE" => "SECTION_CODE"),
    				"detail" => array("SECTION_ID" => "SECTION_ID", "ELEMENT_ID" => "ELEMENT_ID","ELEMENT_ID" => "ID", 
    					"SECTION_CODE" => "SECTION_CODE", "ELEMENT_CODE" => "ELEMENT_CODE", "CODE" => "ELEMENT_CODE"),
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
}
$arFilter = Array(
	"IBLOCK_ID" => $arParams["IBLOCK_ID"],
);
if($arParams["ELEMENT_ID"] <= 0 && strlen($arParams["ELEMENT_CODE"])>0)
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

if($arParams["ELEMENT_ID"]==0)
    return;

if(!CIBlockElement::GetList(Array(), array("IBLOCK_ID"=> $arParams["IBLOCK_ID"],"ID" =>$arParams["ELEMENT_ID"],"ACTIVE" => 'Y'), false, false, Array("ID", "IBLOCK_ID"))->Fetch())
    return;

CJSCore::Init(array('window'));
if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["mfi_mode"] && ($_POST['controlID']=='reviewFile' || $_POST['controlID']=='reviewFileEdit' || $_POST['controlID']=='reviewFileAdd'))
{
    $APPLICATION->IncludeComponent("bitrix:main.file.input","",Array(
        "ALLOW_UPLOAD"=>"F",
        "ALLOW_UPLOAD_EXT" => $arParams["UPLOAD_FILE_TYPE"],
        "MAX_FILE_SIZE" => $arParams["UPLOAD_FILE_SIZE"]*1024,
        "MULTIPLE"=>"Y",
        "MODULE_ID"=>"improved.review",
        'CONTROL_ID' => $_POST['controlID']
        )
    );
}

if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["SHOW_REVIEW_FORM"]=="Y")
{
    unset($_POST["SHOW_REVIEW_FORM"]);
    require_once('popup.php');
    die();
}
    
if($_SERVER["REQUEST_METHOD"] == "POST" && check_bitrix_sessid() && (strlen($_POST["review_add_btn"])>0 || $_POST["ACTION"]=="EDIT"))
{
        $Edit = ($_POST["RID"]>0 && $_POST["ACTION"]=="EDIT" && $arParams["ALLOW_EDIT"]);
        $FILES = $_POST["FILES"];
        if($Edit || $_POST['POPUP']=='Y')
            CUtil::JSPostUnescape();
        
        if($Edit)
        {
            $EditID = (int)$_POST["RID"];
            $FILES = $_POST["FILES_edit"];
            $arFilesCheck = Review\FileTable::getFiles($EditID);
            foreach($arFilesCheck as $arFile)
            {
                $keyDelFile = array_search($arFile['ID'],$FILES);
                if($keyDelFile !== false)
                    unset($FILES[$keyDelFile]);
            }        
        }           
        
		if ($arParams["USE_CAPTCHA"] == "Y")
		{
			if (!$APPLICATION->CaptchaCheckCode($_POST["captcha_word"], $_POST["captcha_sid"]))
			{
				$arResult["ERROR_MESSAGE"] .= GetMessage("IMPROVED_REVIEW_ADD_CP_WRONG_CAPTCHA")."<br>";
			}
		}    
        $TITLE;
        if($arParams["ALLOW_TITLE"]/* && !$arParams["COMMENTS_MODE"]*/)
        {
            $TITLE = trim($_POST["TITLE"]);
			if($arParams['TITLE_MIN_LENGTH']>0 && strlen($TITLE)<$arParams['TITLE_MIN_LENGTH'])
				$arResult["ERROR_MESSAGE"] .= GetMessage("IMPROVED_CP_WRONG_TITLE_MIN_LENGTH",Array("#CNT#"=>$arParams["TITLE_MIN_LENGTH"]))."<br>";
            
            if(strlen($TITLE)>255)                
                $arResult["ERROR_MESSAGE"] .= GetMessage("IMPROVED_CP_WRONG_TITLE_MAX_LENGTH",Array("#CNT#"=>$arParams["TITLE_MAX_LENGTH"]))."<br>";
        }    
        $MESSAGE_PLUS_TEXT;
        $MESSAGE_MINUS_TEXT;
        if(!$arParams["COMMENTS_MODE"])
        {
			$MESSAGE_PLUS_TEXT = trim(htmlspecialcharsEx($_POST["MESSAGE_PLUS"]));
			$MESSAGE_MINUS_TEXT = trim(htmlspecialcharsEx($_POST["MESSAGE_MINUS"]));
			
            if(strlen($MESSAGE_PLUS_TEXT)==0)
            {
                $arResult["ERROR_MESSAGE"] .= GetMessage("IMPROVED_CP_WRONG_MESSAGE_PLUS")."<br>";
            }
			elseif($arParams["PLUS_TEXT_MIN_LENGTH"]>0 && strlen($MESSAGE_PLUS_TEXT)<$arParams["PLUS_TEXT_MIN_LENGTH"])
				$arResult["ERROR_MESSAGE"] .= GetMessage("IMPROVED_CP_WRONG_PLUS_TEXT_MIN_LENGTH",Array("#CNT#"=>$arParams["PLUS_TEXT_MIN_LENGTH"]))."<br>";				
			elseif($arParams["PLUS_TEXT_MAX_LENGTH"]>0 && strlen($MESSAGE_PLUS_TEXT)>$arParams["PLUS_TEXT_MAX_LENGTH"])
				$arResult["ERROR_MESSAGE"] .= GetMessage("IMPROVED_CP_WRONG_PLUS_TEXT_MAX_LENGTH",Array("#CNT#"=>$arParams["PLUS_TEXT_MAX_LENGTH"]))."<br>";
            
			if($arParams["MINUS_TEXT_MIN_LENGTH"]>0 && strlen($MESSAGE_MINUS_TEXT)<$arParams["MINUS_TEXT_MIN_LENGTH"])
				$arResult["ERROR_MESSAGE"] .= GetMessage("IMPROVED_CP_WRONG_MINUS_TEXT_MIN_LENGTH",Array("#CNT#"=>$arParams["MINUS_TEXT_MIN_LENGTH"]))."<br>";				
			elseif($arParams["MINUS_TEXT_MAX_LENGTH"]>0 && strlen($MESSAGE_MINUS_TEXT)>$arParams["MINUS_TEXT_MAX_LENGTH"])
				$arResult["ERROR_MESSAGE"] .= GetMessage("IMPROVED_CP_WRONG_MINUS_TEXT_MAX_LENGTH",Array("#CNT#"=>$arParams["MINUS_TEXT_MAX_LENGTH"]))."<br>";
		}
		$MESSAGE_TEXT = trim(htmlspecialcharsEx($_POST["MESSAGE"]));
        if(strlen($MESSAGE_TEXT)==0)
        {
            $arResult["ERROR_MESSAGE"] .= GetMessage("IMPROVED_CP_WRONG_MESSAGE")."<br>";
        }
		elseif($arParams["MIN_LENGTH"]>0 && strlen($MESSAGE_TEXT)<$arParams["MIN_LENGTH"])
			$arResult["ERROR_MESSAGE"] .= GetMessage("IMPROVED_CP_WRONG_MIN_LENGTH",Array("#CNT#"=>$arParams["MIN_LENGTH"]))."<br>";				
		elseif($arParams["MAX_LENGTH"]>0 && strlen($MESSAGE_TEXT)>$arParams["MAX_LENGTH"])
			$arResult["ERROR_MESSAGE"] .= GetMessage("IMPROVED_CP_WRONG_MAX_LENGTH",Array("#CNT#"=>$arParams["MAX_LENGTH"]))."<br>";

        $RATING = (int)$_POST["RATING"];
        if($arParams["REQUIRED_RATING"] && $RATING==0)
        {
            $arResult["ERROR_MESSAGE"] .= GetMessage("IMPROVED_CP_WRONG_RATING")."<br>";
        }
        
        $arResult["arMessage"] = $arReview = array(
			"ELEMENT_ID" => $arParams["ELEMENT_ID"],
            "TITLE"=>$TITLE,
			"MESSAGE_PLUS"=>$MESSAGE_PLUS_TEXT,
			"MESSAGE_MINUS"=>$MESSAGE_MINUS_TEXT,
			"MESSAGE"=>$MESSAGE_TEXT,
			"RATING" => $RATING,
            "FILES" => $FILES, 
            "SITE_ID" => SITE_ID,
            "SUBSCRIBE" => $_POST["SUBSCRIBE"]
        );
   
        if($Edit && $arParams["ALLOW_EDIT"])
        {
            $arResult["arMessage"]['REPLY'] = trim($_POST['REPLY']);
            $arReview['REPLY'] = trim($_POST['REPLY']);
        }
        
        if(!$USER->IsAuthorized())
        {
            $arResult["arMessage"]["AUTHOR_NAME"] = $arReview["AUTHOR_NAME"] = trim(htmlspecialcharsEx($_POST["AUTHOR_NAME"]));
            
            if(strlen($arReview["AUTHOR_NAME"])==0)
                $arResult["ERROR_MESSAGE"] .= GetMessage("IMPROVED_CP_WRONG_NAME")."<br>";
   
            $arResult["arMessage"]["AUTHOR_EMAIL"] = $arReview["AUTHOR_EMAIL"] = $_POST["AUTHOR_EMAIL"];
            if(!check_email($arReview["AUTHOR_EMAIL"]))
                $arResult["ERROR_MESSAGE"] .= GetMessage("IMPROVED_CP_WRONG_EMAIL")."<br>";
        }
        else
            $arReview["USER_ID"] = $USER->GetID();
        
        if($arParams["COMMENTS_MODE"])
        {
            unset($arReview["MESSAGE_PLUS"]);
            unset($arReview["MESSAGE_MINUS"]);
        }

        if(!$arParams["MODERATE"])
        {
    		$not_moderate = COption::GetOptionString("improved.review", "not_moderate", "N");
    		if($not_moderate == "N")
    		{
    				$arReview["APPROVED"]="Y";
    		}
            else
            {
                if($USER->GetID()>0)
                {
                    $obUser = aReview::GetUserByID($USER->GetID());
                    if($arUser = $obUser->fetch())
                    {
                        if($arUser["ALLOW_POST"]!="Y")
                        {
                            $arResult["ERROR_MESSAGE"] = GetMessage("IMPROVED_CP_WRONG_CLOSE");
                        }
                        else
                        {
                            if($arUser["MODERATE_POST"]=="Y")
                            {
                                $arReview["APPROVED"] = "N";
                            }
                        }
                    }
                    else
                        $arReview["APPROVED"] = "N";
                }
                else
                    $arReview["APPROVED"] = "N";
            }
        }
        else
        {
            if($USER->GetID()>0)
            {
                $obUser = aReview::GetUserByID($USER->GetID());
                if($arUser = $obUser->fetch())
                {
                    if($arUser["ALLOW_POST"]!="Y")
                    {
                        $arResult["ERROR_MESSAGE"] = GetMessage("IMPROVED_CP_WRONG_CLOSE");
                    }
                }
            }            
            $arReview["APPROVED"] = "N";
        }    
    	if($arParams["MODERATE_LINK"])
    	{
    		preg_match_all("#((http|https|ftp):\/\/[a-z:@,.'/\#\%=~\\&?*+\[\]_0-9\x01-\x08-]+)#ies",$MESSAGE_PLUS_TEXT,$arTextLinks);
    		if(isset($arTextLinks[0]) && count($arTextLinks[0])>0)
    		{
    			$arReview["APPROVED"] = "N";
    		}
    		preg_match_all("#((http|https|ftp):\/\/[a-z:@,.'/\#\%=~\\&?*+\[\]_0-9\x01-\x08-]+)#ies",$MESSAGE_MINUS_TEXT,$arTextLinks);
    		if(isset($arTextLinks[0]) && count($arTextLinks[0])>0)
    		{
    			$arReview["APPROVED"] = "N";
    		}						
    		preg_match_all("#((http|https|ftp):\/\/[a-z:@,.'/\#\%=~\\&?*+\[\]_0-9\x01-\x08-]+)#ies",$MESSAGE_TEXT,$arTextLinks);
    		if(isset($arTextLinks[0]) && count($arTextLinks[0])>0)
    		{
    			$arReview["APPROVED"] = "N";
    		}
    	}

        if($arParams["ALLOW_EDIT"])
            $arReview["APPROVED"]="Y";        

//UF
		$arUserFields = Array();
        foreach ($arUFBase as $FIELD_NAME => $arPostField)
        {
            if($Edit && array_key_exists($FIELD_NAME,$arParams["USER_FIELDS_RATING"]))
            {
                if((int)$_POST["EDIT"][$arPostField["FIELD_NAME"]]>0)
                {
                    $arUserFields[$arPostField["FIELD_NAME"]] = $_POST["EDIT"][$arPostField["FIELD_NAME"]];
                    $arResult[$arPostField["FIELD_NAME"]] = $arUserFields[$arPostField["FIELD_NAME"]];
                }
            }
            else
            {    
                if($arPostField["EDIT_IN_LIST"]=="Y")
                {
                    if($arPostField["USER_TYPE"]["BASE_TYPE"]=="file")
                    {
                        $old_id = $_POST[$arPostField["FIELD_NAME"]."_old_id"];
                        if(is_array($old_id))
                        {
                            $arUserFields[$arPostField["FIELD_NAME"]] = array();
                            foreach($old_id as $key=>$value)
                            {
                                $arUserFields[$arPostField["FIELD_NAME"]][$key] = array(
                                        "name" => $_FILES[$arPostField["FIELD_NAME"]]["name"][$key],
                                        "type" => $_FILES[$arPostField["FIELD_NAME"]]["type"][$key],
                                        "tmp_name" => $_FILES[$arPostField["FIELD_NAME"]]["tmp_name"][$key],
                                        "error" => $_FILES[$arPostField["FIELD_NAME"]]["error"][$key],
                                        "size" => $_FILES[$arPostField["FIELD_NAME"]]["size"][$key],
                                        "del" => is_array($_POST[$arPostField["FIELD_NAME"]."_del"]) && in_array($value, $_POST[$arPostField["FIELD_NAME"]."_del"]),
                                        "old_id" => $value,
                                );
                            }
                        }
                        else
                        {
                            if($Edit)
                            {
                                $arUserFields[$arPostField["FIELD_NAME"]] = CFile::MakeFileArray(intval($_POST[$arPostField["FIELD_NAME"]]));
                            }
                            else
                            {
                                $arUserFields[$arPostField["FIELD_NAME"]] = $_FILES[$arPostField["FIELD_NAME"]];
                                $arUserFields[$arPostField["FIELD_NAME"]]["del"] = $_POST[$arPostField["FIELD_NAME"]."_del"];
                                $arUserFields[$arPostField["FIELD_NAME"]]["old_id"] = $old_id;
                            }
                        }
                    }
                    else
                    {
                        $arUserFields[$arPostField["FIELD_NAME"]] = $_POST[$arPostField["FIELD_NAME"]];
                        $arResult[$arPostField["FIELD_NAME"]] = $arUserFields[$arPostField["FIELD_NAME"]];
                    }
                }
            }
        }     
       
        $arReview = array_merge($arReview,$arUserFields);        

        if(strlen($arResult["ERROR_MESSAGE"])==0)
        {
				if(!$USER->IsAuthorized())
				{
					$APPLICATION->set_cookie("REVIEW_AUTHOR_NAME", $arReview["AUTHOR_NAME"], false, "/", false, false, true, "IMPROVED_REVIEW");
					$APPLICATION->set_cookie("REVIEW_AUTHOR_EMAIL", $arReview["AUTHOR_EMAIL"], false, "/", false, false, true, "IMPROVED_REVIEW");
				}
                
                $Review = new aReview();
                $urlAddString = '';
                
                if($Edit)
				    $urlAddString = $_REQUEST["IMPROVED_AJAX_CALL"] == "Y" ? "" : "#review".$EditID;
                    
				if($arParams["IS_SEF"] === "Y")
					$URL_VIEW = $APPLICATION->GetCurPage().$urlAddString;
				else
					$URL_VIEW = $APPLICATION->GetCurPageParam("",array("login","logout","forgot_password","change_password")).$urlAddString;
                
                if($Edit)
                {
                    unset($arReview["AUTHOR_EMAIL"]);
                    unset($arReview["AUTHOR_NAME"]);
                    unset($arReview["USER_ID"]);
                    unset($arReview["SITE_ID"]);      
             
                    $APPLICATION->RestartBuffer();
                    if(!$Review->Update($EditID,$arReview))
                    {
                        $arResult["ERROR_MESSAGE"] .= $Review->LAST_ERROR;
                    }
                    else
                    {
        				if($arParams["SAVE_RATING"])
        				{
        					aReview::SaveRatingToIB($arParams["ELEMENT_ID"],$arParams["IBLOCK_ID"],$arParams["SAVE_RATING_IB_PROPERTY"]);
        				}
                        
                        ?>
                        <script bxrunfirst="true">
                        top.BX.WindowManager.Get().Close();
                        top.BX.showWait();
                        top.BX.reload('<?=CUtil::JSEscape($URL_VIEW);?>', true);
                        </script>
                        <?   
                        die();                     
                    }
                }
                else
                {
    				if(!$REVIEW_ID = $Review->Add($arReview,false))
    				{
    					$arResult["ERROR_MESSAGE"] .= $Review->LAST_ERROR;
                        unset($_POST['POPUP']);
                        if($_POST['POPUP']=='Y')
                        {
                            require_once('popup.php');
                            die();
                        }
                            
    				}
                    else
                    {            
                        $URL_VIEW .= $_REQUEST["IMPROVED_AJAX_CALL"] == "Y" ? "" : "#review".$REVIEW_ID;
        				//rating save
        				if($arParams["SAVE_RATING"] && $arReview["APPROVED"]=='Y')
        				{
        					aReview::SaveRatingToIB($arParams["ELEMENT_ID"],$arParams["IBLOCK_ID"],$arParams["SAVE_RATING_IB_PROPERTY"]);
        				}
                        
                        if($arParams["SAVE_COUNT"] && $arReview["APPROVED"]=='Y')
                        {
                            Review\Tools::saveCountToProperty($arParams["ELEMENT_ID"],$arParams["IBLOCK_ID"],$arParams["SAVE_COUNT_IB_PROPERTY"]);
                        }      
                    
						$arEventFields = Array(
                            "TITLE"=>$TITLE,
							"MESSAGE_PLUS" => $MESSAGE_PLUS_TEXT,
							"MESSAGE_MINUS" => $MESSAGE_MINUS_TEXT,
							"MESSAGE" => $MESSAGE_TEXT,
							"POST_URL" => $URL_VIEW,
							"REVIEW_ID" => $REVIEW_ID,
                            'RATING' => $RATING,
						);     
                        $arEventFields["MESSAGE_BODY"] = "";
                        if(strlen($MESSAGE_PLUS_TEXT)>0)
                        {
                            $arEventFields["MESSAGE_BODY"] .= GetMessage('IMPROVED_CP_EV_PLUS').CEvent::GetMailEOL().CTextParser::convert4mail($MESSAGE_PLUS_TEXT).CEvent::GetMailEOL();
                        }
                        
                        if(strlen($MESSAGE_MINUS_TEXT)>0)
                        {
                            $arEventFields["MESSAGE_BODY"] .= GetMessage('IMPROVED_CP_EV_MINUS').CEvent::GetMailEOL().CTextParser::convert4mail($MESSAGE_MINUS_TEXT).CEvent::GetMailEOL();
                        }

                        if(strlen($MESSAGE_TEXT)>0)
                        {
                            $arEventFields["MESSAGE_BODY"] .= GetMessage('IMPROVED_CP_EV_COMMENT').CEvent::GetMailEOL().CTextParser::convert4mail($MESSAGE_TEXT);
                        }
                        
                        if($USER->GetID()>0)
                        {
                            $arEventFields["AUTHOR"] = "(".$USER->GetLogin().") ".$USER->GetFirstName();
                            $arEventFields["AUTHOR_EMAIL"] = $USER->GetEmail();
                        }
                        else
                        {
                            $arEventFields["AUTHOR"] = $arReview["AUTHOR_NAME"];
                            $arEventFields["AUTHOR_EMAIL"] = $arReview["AUTHOR_EMAIL"];
                        }
                        
                        $arElement = aReview::GetElementInfo($arParams["ELEMENT_ID"]);
                        $arEventFields["IBLOCK_ELEMENT_ID"] = $arParams["ELEMENT_ID"];
                    	$arEventFields["IBLOCK_ELEMENT_NAME"] = $arElement["NAME"];
                    	$arEventFields["IBLOCK_ELEMENT_IBLOCK_ID"] = $arElement["IBLOCK_ID"];
                    	$arEventFields["IBLOCK_ELEMENT_DETAIL_PAGE_URL"] = $arElement["DETAIL_PAGE_URL"];
                        $arEventFields["POST_URL"] = $arEventFields["IBLOCK_ELEMENT_DETAIL_PAGE_URL"]."#review".$REVIEW_ID;
						$arEventFields["POST_DATE"] = aReview::GetPostDate($REVIEW_ID);
						$arEventFields["EMAIL_FROM"] = COption::GetOptionString("improved.review", "email_from");
						$arEventFields["EMAIL_ADMIN"] = COption::GetOptionString("improved.review", "email_admin");
						
                        $db_events = GetModuleEvents("improved.review", "OnBeforeAddSendMail");
                        while($arEvent = $db_events->Fetch())
                                if(ExecuteModuleEventEx($arEvent, array(&$arEventFields))===false)
                                    return false;
						
						$arSendEmails = aReview::GetSendEmails($arParams["MOD_GOUPS"]);
                        array_unique($arSendEmails);
						foreach($arSendEmails as $Email)
						{
							$arEventFields["EMAIL"] = $Email;
							CEvent::SendImmediate("IMPROVED_REVIEW_ADD", SITE_ID, $arEventFields);
						}
                        
                        if($arParams['SEND_NOTIFY'])
                        {
							$arEventFields["EMAIL"] = $arEventFields["AUTHOR_EMAIL"];
							CEvent::SendImmediate("IMPROVED_REVIEW_ADD_NOTIFY", SITE_ID, $arEventFields);                            
                        }
                                            
                    //mail
    				$_SESSION["REVIEW_ADD_OK"] = true;
    
    				//redirect
                    if($_POST['POPUP']=='Y')
                    {
                    ?>
                        <script bxrunfirst="true">
                        top.BX.WindowManager.Get().Close();
                        top.BX.showWait();
                        top.BX.reload('<?=CUtil::JSEscape($URL_VIEW);?>', true);
                        </script>
                        <?   
                        die();
                    }
                    else
                    {
    				LocalRedirect($URL_VIEW);
                    exit();
                    }
                    }
                }
        }
        else
        {
            if($_POST['POPUP']=='Y')
            {
                unset($_POST['POPUP']);
                require_once('popup.php');
                die();
            }            
        }   
}

if($_POST["RID"]>0 && $_POST["ACTION"]=="EDIT" && $arParams["ALLOW_EDIT"] && empty($arResult["ERROR_MESSAGE"]))
{
    $REVIEW_ID_EDIT = $_POST["RID"];
    $arResult["arMessage"] = aReview::GetByID($REVIEW_ID_EDIT)->fetch();

    $arUFBase = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields("IMPROVED_REVIEW",$REVIEW_ID_EDIT,LANGUAGE_ID);
    $arParams["USER_FIELDS"] = array();
    $arParams["USER_FIELDS_RATING"] = Array();
    foreach($arUFBase as $k=>$arUF)
    {
        if($arUF['USER_TYPE_ID']=='IMPROVED_REVIEW_RATING')
        {
            if(in_array($k,$arParams["UF_VOTE"]))
                $arParams["USER_FIELDS_RATING"][$k] = $arUF;
        }
        else
        {
            if(in_array($k,$arParams["UF"]))
                $arParams["USER_FIELDS"][$k] = $arUF;
        }
    }
    $arFiles = Review\FileTable::getFiles($REVIEW_ID_EDIT);
    foreach($arFiles as $arFile)
        $arResult['FILES_EDIT_VALUE'][] = $arFile['ID'];
}

if ($arParams["USE_CAPTCHA"] == "Y")
	$arResult["CAPTCHA_CODE"] = htmlspecialcharsEx($APPLICATION->CaptchaGetCode());

$this->IncludeComponentTemplate();
?>
<script type="text/javascript">
<!--
    var review_ml = <?=intval($arParams["MAX_LENGTH"]);?>;
    var review_show_cnt = <?if($arParams["SHOW_CNT"]):?>true<?else:?>false<?endif;?>;
BX.ready(
    BX.message({
    	ERROR_FILE_UPLOAD: '<?=GetMessageJS('IMPROVED_REVIEW_ADD_ERROR_UPLOAD_FILE')?>'
    })
);    
//-->
</script>
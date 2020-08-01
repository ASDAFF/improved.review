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
CPageOption::SetOptionString("main", "nav_page_in_session", "N");

use IMPROVED\Review as R;

$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);
$arParams["ELEMENT_ID"] = intval($arParams["ELEMENT_ID"]);
$arParams["ELEMENT_CODE"] = trim($arParams["ELEMENT_CODE"]);

$arParams["LIST_TITLE"] = trim($arParams["LIST_TITLE"]);
$arParams["POST_DATE_FORMAT"] = trim($arParams["POST_DATE_FORMAT"]);
if(strlen($arParams["POST_DATE_FORMAT"]) <= 0)
        $arParams["POST_DATE_FORMAT"] = "j F Y, H:i";

$arParams["SHOW_UPLOAD_FILE"] = ($arParams["SHOW_UPLOAD_FILE"]=="Y");
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

$arParams["ALLOW_VOTE"] = ($arParams["ALLOW_VOTE"] == "Y");
$arParams["COMMENTS_MODE"] = ($arParams["COMMENTS_MODE"] == "Y");
$arParams["CACHE_TIME"] = (int)$arParams["CACHE_TIME"];
if($arParams["CACHE_TIME"]==0)
	$arParams["CACHE_TIME"] = 86400000;
//nav
$arParams["REVIEWS_ON_PAGE"] = (int)$arParams["REVIEWS_ON_PAGE"];
$arParams["SHOW_ALL"] = false;
if(isset($_REQUEST["showAll"]))
    $arParams["SHOW_ALL"] = ($_REQUEST["showAll"]=="Y");

$arParams['USER_PATH'] = trim($arParams['USER_PATH']); 
$arParams["SHOW_MAIN_RATING"] = $arParams["SHOW_MAIN_RATING"]=="Y";

if($arParams['CACHE_TYPE'] == 'N')
    $arParams["CACHE_TIME"] = 0;

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
/* check element */
if($arParams["ELEMENT_ID"]==0 && strlen($arParams["ELEMENT_CODE"])==0)
{
    $arComponentVariables = array("SECTION_ID", "ELEMENT_ID","ID", "SECTION_CODE", "ELEMENT_CODE", "CODE");
    $arVariableAliases = array(
    				"section" => array("SECTION_ID" => "SECTION_ID","SECTION_CODE" => "SECTION_CODE"),
    				"detail" => array("SECTION_ID" => "SECTION_ID", "ELEMENT_ID" => "ELEMENT_ID","ELEMENT_ID" => "ID", 
    					"SECTION_CODE" => "SECTION_CODE","ELEMENT_CODE" => "ELEMENT_CODE", "CODE" => "ELEMENT_CODE"),
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
/*end check element*/

$arParams["ONLY_AUTH_COMPLAINT"] = ($arParams["ONLY_AUTH_COMPLAINT"]=="Y");

$arParams["ALLOW_COMPLAINT"] = false;
if(!$arParams["ALLOW_EDIT"])
{
    if(!$arParams["ONLY_AUTH_COMPLAINT"] || ($arParams["ONLY_AUTH_COMPLAINT"] && $USER->IsAuthorized()))
        $arParams["ALLOW_COMPLAINT"] = true;
}     

if($_REQUEST["IMPROVED_AJAX_CALL"]=="Y" && $arParams["SHOW_ALL"])
    $APPLICATION->RestartBuffer();

if($_POST["IMPROVED_AJAX_CALL"]=="Y")
{
	$APPLICATION->RestartBuffer();
	$RID = (int)$_POST["RID"];
    require_once("ajax.php");
}
if($_POST["RID"]>0 && $_POST["ACTION"]=="EDIT")
{
    require_once('edit.php');
    die();
}
if($_POST["RID"]>0 && $_POST["ACTION"]=="COMPLAINT")
{
    require_once('complaint.php');
    die();
}    

if($_SERVER["REQUEST_METHOD"] == "POST" && check_bitrix_sessid() && $_POST["RID"]>0 && $_POST["ACTION"]=="COMPLAINT_ADD")
{
    $APPLICATION->RestartBuffer();
    CUtil::JSPostUnescape();
    
	if(!$USER->IsAuthorized() && $arParams["ONLY_AUTH_COMPLAINT"] || !$arParams["ALLOW_COMPLAINT"])
	{
		$strWarning = GetMessage("IMPROVED_REVIEW_CP_LIST_ABUSE_ERROR_AUTH");
        require_once('complaint.php');
        die();
	}

	if(!$USER->IsAuthorized())
	{
			$SENDER_NAME = htmlspecialcharsEx($_POST["SENDER_NAME"]);
			if(strlen($SENDER_NAME)==0)
					$strWarning .= GetMessage("IMPROVED_REVIEW_CP_LIST_ABUSE_ERROR_SENDER_NAME")."<br />";

			$SENDER_EMAIL = htmlspecialcharsEx($_POST["SENDER_EMAIL"]);
			if(!check_email($SENDER_EMAIL))
			{
					$strWarning .= GetMessage("IMPROVED_REVIEW_CP_LIST_ABUSE_ERROR_SENDER_EMAIL")."<br />";
			}
	}    
	$MESSAGE = htmlspecialcharsEx($_POST["comment"]);
	if(strlen($MESSAGE)==0)
		$strWarning .= GetMessage("IMPROVED_REVIEW_CP_LIST_ABUSE_ERROR_MESSAGE")."<br />";

	if(strlen($strWarning)>0)
	{
			require_once('complaint.php');
			die();
	}
        
	$arAbuseAdd = Array("REVIEW_ID"=>intval($_POST["RID"]),"MESSAGE"=>$MESSAGE,
			"USER_ID"=>$USER->GetID(),
			"SENDER_NAME"=>$SENDER_NAME,
			"SENDER_EMAIL"=>$SENDER_EMAIL,
	);

	if(!defined("BX_UTF"))
			$arAbuseAdd["MESSAGE"] = mb_convert_encoding($arAbuseAdd["MESSAGE"], 'windows-1251', 'auto');

	if(aReviewComplaint::Add($arAbuseAdd))
	{
		if($arParams["IS_SEF"] === "Y")
			$URL_VIEW = $APPLICATION->GetCurPage();
		else
			$URL_VIEW = $APPLICATION->GetCurPageParam("",array("login","logout","forgot_password","change_password"));
?>
        <script bxrunfirst="true">
        alert('<?=GetMessage("IMPROVED_REVIEW_T_REVIEW_LIST_REPORT_OK")?>');
        top.BX.WindowManager.Get().Close();
        top.BX.showWait();
        top.BX.reload('<?=CUtil::JSEscape($URL_VIEW);?>', true);
        </script>
<?
	}
    else
    {
        $strWarning = "unknown error";
        require_once('complaint.php');
    }    
    die();
}

//get
$cache = new CPHPCache;
$cache_id = $arParams["ELEMENT_ID"].'|'.($arParams["ALLOW_EDIT"] ? 1 : 0).'|'.($arParams["SHOW_ALL"] ? 1 : 0);
$cache_path = "/".SITE_ID."/improved/review.list/".$arParams["ELEMENT_ID"]."/";
if ($arParams["CACHE_TIME"] > 0 && $cache->InitCache($arParams["CACHE_TIME"], $cache_id, $cache_path))
{
    $Vars = $cache->GetVars();
    foreach($Vars["arResult"] as $k=>$v)
            $arResult[$k] = $v;
    CBitrixComponentTemplate::ApplyCachedData($Vars["templateCachedData"]);
    $cache->Output();
}
else
{
    if ($arParams["CACHE_TIME"] > 0)
            $cache->StartDataCache($arParams["CACHE_TIME"], $cache_id, $cache_path);
    
		$arFilter = Array(
			"IBLOCK_ID"	 =>	  $arParams["IBLOCK_ID"],
			"ID" =>$arParams["ELEMENT_ID"],
            "ACTIVE" => 'Y',
		);
		$rsElement = CIBlockElement::GetList(Array(), $arFilter, false, false, Array("ID", "IBLOCK_ID"));
		if($arElement = $rsElement->GetNext())
		{
            $arRFilter = Array("ELEMENT_ID"=>$arParams["ELEMENT_ID"],"ONLY_RATING"=>"N");
            $arResult["ALL_CNT"] = R\ReviewTable::count(array_merge($arRFilter,array('APPROVED'=>'Y')));
            $limit = !$arParams["SHOW_ALL"] ? $arParams["REVIEWS_ON_PAGE"] : null;
            $obReview = R\ReviewTable::getList(array(
                'order'=>array('IS_BEST'=>'DESC',"ID"=>"DESC"),
                'filter'=>$arRFilter,
                'limit'=>$limit,
                'select'=>array('*','VOTE','USER_LOGIN'=>'USER.LOGIN','USER_NAME'=>'USER.NAME','USER_LAST_NAME'=>'USER.LAST_NAME')
            ));
			while($arReview = $obReview->fetch())
			{
                $arReview['POST_DATE'] = $arReview['POST_DATE']->toString();
                $arReview["SHOW_USER_PATH"] = '';
                if(strlen($arParams['USER_PATH'])>0 && $arReview["USER_ID"])
                {
                    $arReview["SHOW_USER_PATH"] = CComponentEngine::MakePathFromTemplate($arParams['USER_PATH'],Array("USER_ID"=>$arReview["USER_ID"]));
                }
                
                if($arReview["USER_ID"]>0)
                {
                   switch($arParams["NAME_SHOW_TYPE"])
                   {
                        case "LOGIN":
                            $arReview["AUTHOR_NAME"] = $arReview["USER_LOGIN"]; 
                        break;
                    
                        case "NAME":
                            $arReview["AUTHOR_NAME"] = $arReview["USER_NAME"]; 
                        break;
                        case "NAME_LAST_NAME":
                            $arReview["AUTHOR_NAME"] = $arReview["USER_NAME"]." ".$arReview["USER_LAST_NAME"];                                
                        break;
                        case "LOGIN_NAME":
                            $arReview["AUTHOR_NAME"] = "(".$arReview["USER_LOGIN"].") ".$arReview["USER_NAME"];
                        break;                                                                                    
                        case "LOGIN_NAME_LAST_NAME":
                            $arReview["AUTHOR_NAME"] = "(".$arReview["USER_LOGIN"].") ".$arReview["USER_NAME"]." ".$arReview["USER_LAST_NAME"];
                        break;                                                                
                   } 
                }
                
                $ts = MakeTimeStamp($arReview["POST_DATE"], CSite::GetDateFormat());			 
				$arReview["POST_DATE_FORMAT"] = aReview::FormatDate($arParams["POST_DATE_FORMAT"],$ts);
                $arReview["FILES"] = R\FileTable::getFiles($arReview["ID"]);

                if($arParams["SHOW_AVATAR_TYPE"]!=false && $arReview["USER_ID"]>0)
                {
                    $arReview["USER_AVATAR"] = aReview::GetAvatar($arReview["USER_ID"],$arParams["SHOW_AVATAR_TYPE"],Array("width"=>$arParams["AVATAR_WIDTH"],"height"=>$arParams["AVATAR_HEIGHT"]));
                }
                $arReview["USER_FIELDS"] = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields("IMPROVED_REVIEW",$arReview["ID"],LANGUAGE_ID);
                $arReview["USER_FIELDS_RATING"] = Array();
                foreach($arReview["USER_FIELDS"] as $k=>$arUF)
                {
                        
                    if($arUF['USER_TYPE_ID']=='IMPROVED_REVIEW_RATING')
                    {
                        if(empty($arParams["UF_VOTE"]) || (count($arParams["UF_VOTE"])>0 && !in_array($k,$arParams["UF_VOTE"])))
                            continue;
                                                    
                        $arReview["USER_FIELDS_RATING"][$k] = $arUF;
                        unset($arReview["USER_FIELDS"][$k]);
                    }
                    else
                    {
                        if(count($arParams["UF"])>0 && !in_array($k,$arParams["UF"]))
                            continue;                        
                    }
                }
				$arResult["ITEMS"][$ts] = $arReview;  
            }
        }    
    if ($arParams["CACHE_TIME"] > 0)
            $cache->EndDataCache(array("templateCachedData"=>$this-> GetTemplateCachedData(), "arResult"=>$arResult));
}
if(!empty($arResult))
    $this->IncludeComponentTemplate();
        
if($_REQUEST["IMPROVED_AJAX_CALL"]=="Y" && $arParams["SHOW_ALL"])
    die();
    
$APPLICATION->AddHeadString('<script type="text/javascript">
var bsmsessid = \''.bitrix_sessid().'\';
new_href = window.location.href;
var hashpos = new_href.indexOf(\'#\'), hash = \'\';
if (hashpos != -1)
{
        hash = new_href.substr(hashpos);
        new_href = new_href.substr(0, hashpos);
}
var CURRENT_URL = new_href;
</script>');    

if($arParams["ALLOW_EDIT"])
{
$path = "";
if(isset($this->__template->__folder) && strlen($this->__template->__folder)>0)
				$path = $this->__template->__folder;
else
				$path = "/bitrix/components/improved/review.list/templates/".$this->__templateName;
$APPLICATION->AddHeadString("
<script>
var ReviewModMessages = {
	'REVIEW_LIST_MODER_APP': '".GetMessage("IMPROVED_REVIEW_T_REVIEW_LIST_MODER_APP")."',
	'REVIEW_LIST_MODER_HIDE': '".GetMessage("IMPROVED_REVIEW_T_REVIEW_LIST_MODER_HIDE")."',
    'REVIEW_LIST_MODER_DEL': '".GetMessage("MAIN_DELETE")."',
    'REVIEW_LIST_MODER_SET_BEST': '".GetMessage("IMPROVED_REVIEW_T_REVIEW_LIST_MODER_SET_BEST")."',
    'REVIEW_LIST_MODER_DEL_BEST': '".GetMessage("IMPROVED_REVIEW_T_REVIEW_LIST_MODER_DEL_BEST")."'
	};
</script>");
$APPLICATION->AddHeadScript($path."/admin_script.js");
}
?>
<?
/**
 * Copyright (c) 1/8/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

?>
<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php"); 
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/improved.review/include.php");
IncludeModuleLangFile(__FILE__);

$ModulePermissions = $APPLICATION->GetGroupRight("improved.review");
if ($ModulePermissions == "D")
		$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$sTableID = "tbl_improved_review_list";
$oSort = new CAdminSorting($sTableID, "ID", "DESC");
$lAdmin = new CAdminList($sTableID, $oSort);

$arFilterFields = Array(
		"find_author_email",
		"find_approved",
		"find_element_id",
		"find_id",
		"find_date1",
		"find_date2",
		"find_author_id",
        "find_title",
		"find_author"        
		);

if($lAdmin->IsDefaultFilter())
{
		$find_date1_DAYS_TO_BACK=90;
		$find_date2 = ConvertTimeStamp(time()-86400, "SHORT");
		$find_approved = "N";
		$find_deleted = "N";
		$set_filter = "Y";
}
$lAdmin->InitFilter($arFilterFields);

if((int)$find_element_id ==0 && (int)$ELEMENT_ID>0)
    $find_element_id = (int)$ELEMENT_ID;
    
$arFilter = Array(
		"APPROVED"			=>	$find_approved,
		"DELETED"			=>	$find_deleted,
		"ID"				=>	$find_id,
		"ELEMENT_ID"		=>	$find_element_id,
        "TITLE"             => $find_title,
);

foreach($arFilter as $k=>$v)
    if(is_null($v) || strlen($v)==0)
        unset($arFilter[$k]);
        
if(strlen($find_author)>0)
{
    $arFilter[0] = Array(
        "LOGIC"=>"OR",
        Array("AUTHOR_EMAIL" => $find_author),
        Array("AUTHOR_NAME" => $find_author),
        Array("USER_NAME" => $find_author),
        Array("USER_LAST_NAME" => $find_author),
        Array("USER_LOGIN" => $find_author),
        Array("USER_EMAIL" => $find_author),
    );
    if(intval($find_author)>0)
        $arFilter[0][] = Array("USER_ID" => $find_author);
}
	
if(CModule::IncludeModule("statistic"))
{
		if(AdminListCheckDate($lAdmin, array("find_date1"=>$find_date1, "find_date2"=>$find_date2)))
		{
		      if(strlen($find_date1)>0)
				$arFilter[">=POST_DATE"] =  $find_date1;
              if(strlen($find_date2)>0)  
				$arFilter["<=POST_DATE"] =  $find_date2;
		}
}
else
{
		if(CheckDateTime($find_date1) && strlen($find_date1)>0)
				$arFilter[">=POST_DATE"] =  $find_date1;

		if(CheckDateTime($find_date2) && strlen($find_date2)>0)
				$arFilter["<=POST_DATE"] =  $find_date2;
}

if($lAdmin->EditAction())
{
		foreach($FIELDS as $ID=>$arFields)
		{
				if(!$lAdmin->IsUpdated($ID))
						continue;
				$ID = IntVal($ID);
				$Review = new aReview;
				if(!isset($arFields['ELEMENT_ID']))
					$arFields['ELEMENT_ID'] = aReview::GetElementIdById($ID);
                                    
				if(!$Review->Update($ID, $arFields))
				{
						$lAdmin->AddUpdateError("ID:".$ID." - ".$Review->LAST_ERROR, $ID);
				}
		}
}

if ($arID = $lAdmin->GroupAction())		
{
		if($_REQUEST['action_target']=='selected')
		{
				$rsUniqData = aReview::GetList(Array($by=>$order), $arFilter);
				while($arRes = $rsUniqData->Fetch())
						$arID[] = $arRes;
		}
		foreach($arID as $ID)
		{
				if(strlen($ID)<=0)
						continue;

				switch($_REQUEST['action'])
				{
						case "delete":
								aReview::Delete($ID);
								break;
						case "restore":
								aReview::Restore($ID);
								break;
						case "approve":
								aReview::SetApproved($ID,true);
								break;
						case "hide":
								aReview::SetApproved($ID,false);
								break;
				}
		}
}
$arSite = array();
$dataSites = \Bitrix\Main\SiteTable::getList();
while($dataSite = $dataSites->fetch())
{
    $arSite[$dataSite['LID']] = $dataSite;
}

//$rsData = aReview::GetList(Array($by=>$order), $arFilter,Array("*","USER"));
$rsData = IMPROVED\Review\ReviewTable::getList(array(
    'filter' => $arFilter,
    'order' => array($by=>$order),
    'select' => array('*','USER_LOGIN'=>'USER.LOGIN','USER_NAME'=>'USER.NAME','USER_EMAIL'=>'USER.EMAIL','USER_LAST_NAME'=>'USER.LAST_NAME')
));
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("IMPROVED_REVIEW_ADMIN_NAV")));

$arHeaders = array(
	array("id"=>"ID",              "content"=>"ID",                                            "sort"=>"ID",               "default"=>false),
	array("id"=>"ELEMENT_ID",      "content"=>GetMessage("IMPROVED_REVIEW_HEAD_ELEMENT_ID"),    "sort"=>"ELEMENT_ID",       "default"=>true),
	array("id"=>"APPROVED",        "content"=>GetMessage("IMPROVED_REVIEW_HEAD_APPROVED"),      "sort"=>"APPROVED",         "default"=>true, "align" => "center"),
	array("id"=>"AUTHOR_NAME",     "content"=>GetMessage("IMPROVED_REVIEW_HEAD_AUTHOR_NAME"),   "sort"=>"AUTHOR_NAME",      "default"=>true),
	array("id"=>"AUTHOR_EMAIL",    "content"=>GetMessage("IMPROVED_REVIEW_HEAD_AUTHOR_EMAIL"),  "sort"=>"AUTHOR_EMAIL",     "default"=>true),
	array("id"=>"POST_DATE",       "content"=>GetMessage("IMPROVED_REVIEW_HEAD_POST_DATE"),     "sort"=>"POST_DATE",        "default"=>true),
	array("id"=>"MESSAGE_PLUS",    "content"=>GetMessage("IMPROVED_REVIEW_HEAD_MESSAGE_PLUS"),                              "default"=>true),
	array("id"=>"MESSAGE_MINUS",   "content"=>GetMessage("IMPROVED_REVIEW_HEAD_MESSAGE_MINUS"),                             "default"=>true),
	array("id"=>"MESSAGE",         "content"=>GetMessage("IMPROVED_REVIEW_HEAD_MESSAGE"),                                   "default"=>true),
    array("id"=>"TITLE",           "content"=>GetMessage("IMPROVED_REVIEW_HEAD_TITLE"),                                     "default"=>false),
    array("id"=>"AUTHOR_IP",       "content"=>GetMessage("IMPROVED_REVIEW_HEAD_IP"),                                        "default"=>false),
);
$lAdmin->AddHeaders($arHeaders);
$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();
$arIBlockElementCache = Array();
	while($arRes = $rsData->NavNext(true, "f_"))
	{
		$row =& $lAdmin->AddRow($f_ID, $arRes);
		$row->AddViewField("APPROVED", GetMessage("IMPROVED_REVIEW_APPROVED_".$arRes["APPROVED"]));

		if($arRes["USER_ID"]>0)
		{
				$row->AddViewField("AUTHOR_NAME", "[<a href='/bitrix/admin/user_edit.php?lang=".LANG."&ID=".$arRes["USER_ID"]."' target='_blank'>".$arRes["USER_ID"]."</a>] (".$arRes["USER_LOGIN"].") ".$arRes["USER_NAME"]." ".$arRes["USER_LAST_NAME"]);
				$row->AddViewField("AUTHOR_EMAIL", $arRes["USER_EMAIL"]);
		}
		else
		{
				$row->AddEditField("AUTHOR_NAME", $arRes["AUTHOR_NAME"]);
				$row->AddEditField("AUTHOR_EMAIL", htmlspecialcharsEx($arRes["AUTHOR_EMAIL"]));
		}

		
		$row->AddCheckField("APPROVED");
		$row->AddCheckField("DELETED");

		$showField = "";
		if(in_array("MESSAGE_PLUS", $arVisibleColumns))
		{
				$showField = '<textarea rows="10" cols="50" name="FIELDS['.$f_ID.'][MESSAGE_PLUS]">'.htmlspecialcharsEx($arRes["MESSAGE_PLUS"]).'</textarea>';
		}
		$row->AddEditField("MESSAGE_PLUS", $showField);

		$showField = "";
		if(in_array("MESSAGE_MINUS", $arVisibleColumns))
		{
				$showField = '<textarea rows="10" cols="50" name="FIELDS['.$f_ID.'][MESSAGE_MINUS]">'.htmlspecialcharsEx($arRes["MESSAGE_MINUS"]).'</textarea>';
		}
		$row->AddEditField("MESSAGE_MINUS", $showField);

		$showField = "";
		if(in_array("MESSAGE", $arVisibleColumns))
		{
				$showField = '<textarea rows="10" cols="50" name="FIELDS['.$f_ID.'][MESSAGE]">'.htmlspecialcharsEx($arRes["MESSAGE"]).'</textarea>';
		}
		$row->AddEditField("MESSAGE", $showField);
        $row->AddViewField("TITLE", $arRes["TITLE"]);
        $row->AddEditField("TITLE", $arRes["TITLE"]);

		$showField = "";
		if(in_array("ELEMENT_ID", $arVisibleColumns) && CModule::IncludeModule("iblock"))
		{
				if(!array_key_exists($arRes["ELEMENT_ID"], $arIBlockElementCache))
				{
						$arIBlockElementCache[$arRes["ELEMENT_ID"]] = Array("NAME" => "", "DETAIL_PAGE_URL" => "");
						$obIBlockElement = CIBlockElement::GetList(Array(), Array("ID" => intval($arRes["ELEMENT_ID"])), false, false, Array("DETAIL_PAGE_URL", "ID", "IBLOCK_ID", "NAME"));
						if($arIBlockElement = $obIBlockElement->GetNext())
						{
								$arIBlockElementCache[$arRes["ELEMENT_ID"]] = $arIBlockElement;
						}
				}
				$arIBlockElement = $arIBlockElementCache[$arRes["ELEMENT_ID"]];
                
                $server_name = '';
                if(strlen($arSite[$arRes['SITE_ID']]['SERVER_NAME'])>0)
                {
                    $server_name = (CMain::IsHTTPS()) ? "https://" : "http://".$arSite[$arRes['SITE_ID']]['SERVER_NAME'];
                }
                
				$showField = '<a href="'.$server_name.$arIBlockElement["DETAIL_PAGE_URL"].'#review_'.$f_ID.'" target="_blank">'.htmlspecialcharsEx($arIBlockElement["NAME"]).'</a>';
		}
		$row->AddField("ELEMENT_ID", $showField);

		$arActions = Array();
		if($arRes["APPROVED"] <> "Y")
				$arActions[] = array("ICON"=>"list", "TEXT"=>GetMessage("IMPROVED_REVIEW_PUBLISH"), "ACTION"=>$lAdmin->ActionDoGroup($f_ID, "approve", 'ID='.$f_ID));
		else
				$arActions[] = array("ICON"=>"list", "TEXT"=>GetMessage("IMPROVED_REVIEW_HIDE"), "ACTION"=>$lAdmin->ActionDoGroup($f_ID, "hide", 'ID='.$f_ID));

		$arActions[] = array("SEPARATOR"=>true);
		$arActions[] = array("ICON"=>"delete", "TEXT"=>GetMessage("MAIN_DELETE"), "ACTION"=>"if(confirm('".GetMessage("IMPROVED_REVIEW_DEL_CONF")."')) ".$lAdmin->ActionDoGroup($f_ID, "delete", 'ID='.$f_ID));
		$row->AddActions($arActions);
	}

$lAdmin->AddFooter(
	array(
		array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()),
		array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"),
	)
);

$lAdmin->AddGroupActionTable(Array(
	"delete"=>GetMessage("MAIN_ADMIN_LIST_DELETE"),
	"approve"=>GetMessage("IMPROVED_REVIEW_PUBLISH"),
	"hide"=>GetMessage("IMPROVED_REVIEW_HIDE"),
));


$aContext = array( Array(
	"TEXT" => GetMessage("IMPROVED_REVIEW_UP_BUTTON"),
	"TITLE" => GetMessage("IMPROVED_REVIEW_UP_BUTTON"),
	"ICON"=>"btn_list",
	"LINK"=>'/bitrix/admin/improved_review_section.php?lang='.LANG,
));
$lAdmin->AddAdminContextMenu($aContext);
$lAdmin->CheckListMode();
$APPLICATION->SetTitle(GetMessage("IMPROVED_REVIEW_ADMIN_LIST_TITLE"));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php"); // Second prolog
?>
<form name="filter_form" method="GET" action="<?echo $APPLICATION->GetCurPageParam()?>">
<?
$oFilter = new CAdminFilter(
		$sTableID."_filter",
		array(
				GetMessage("IMPROVED_REVIEW_F_CREATE_PERIOD"),
				GetMessage("IMPROVED_REVIEW_F_APPROVED"),
				GetMessage("IMPROVED_REVIEW_F_ELEMENT_ID"),
                GetMessage("IMPROVED_REVIEW_F_TITLE"),
				"ID",
		)
);
$oFilter->Begin();
?>
		<tr>
				<td><b><?echo GetMessage("IMPROVED_REVIEW_F_AUTHOR")?>:</b></td>
				<td><input type="text" name="find_author" value="<?echo htmlspecialcharsEx($find_author)?>" size="40"></td>
		</tr>
		<tr>
				<td><?echo GetMessage("IMPROVED_REVIEW_F_CREATE_PERIOD")." (".FORMAT_DATE."):"?></td>
				<td><?echo CalendarPeriod("find_date1", $find_date1, "find_date2", $find_date2, "filter_form", "Y")?></td>
		</tr>
		<tr>
				<td><?echo GetMessage("IMPROVED_REVIEW_F_APPROVED");?>:</td>
				<td>
						<?
						$arr = array("reference"=>array(GetMessage("MAIN_YES"), GetMessage("MAIN_NO")), "reference_id"=>array("Y","N"));
						echo SelectBoxFromArray("find_approved", $arr, htmlspecialcharsEx($find_approved), GetMessage('MAIN_ALL'));
						?>
				</td>
		</tr>
	
		<tr>
				<td><?echo GetMessage("IMPROVED_REVIEW_F_ELEMENT_ID")?>:</td>
				<td><input type="text" name="find_element_id" value="<?echo htmlspecialcharsEx($find_element_id)?>" size="40"></td>
		</tr>		

		<tr>
				<td><?echo GetMessage("IMPROVED_REVIEW_F_TITLE")?>:</td>
				<td><input type="text" name="find_title" value="<?echo htmlspecialcharsEx($find_title)?>" size="40"></td>
		</tr>		
		
		<tr>
				<td>ID:</td>
				<td>
					<input type="text" name="find_id" value="<?echo htmlspecialcharsEx($find_id)?>" size="15">
				</td>
		</tr>
<?
$oFilter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPageParam("",Array("ELEMENT_ID")), "form"=>"filter_form"));
$oFilter->End();
?>
</form>
<?
$lAdmin->DisplayList();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
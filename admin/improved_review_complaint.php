<?
/**
 * Copyright (c) 1/8/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

?>
<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/improved.review/include.php");
IncludeModuleLangFile(__FILE__);

$sModulePermissions = $APPLICATION->GetGroupRight("improved.review");
if ($sModulePermissions != "W")
                $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$sTableID = "tbl_improved_review_complaint";
$oSort = new CAdminSorting($sTableID, "ID", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);
$arFilterFields = Array(
                "find_id",
                "find_active",
                "find_review_id",
                "find_sender",
);

$lAdmin->InitFilter($arFilterFields);

$arFilter = Array(
        "COMMENT_ID"    => $find_review_id,
        "SENDER"        => $find_sender,
        "ACTIVE"        => $find_active,
        "ID"            => $find_id
);

if($lAdmin->EditAction()) // Save button was pressed
{
        foreach($FIELDS as $ID=>$arFields)
        {
                if(!$lAdmin->IsUpdated($ID))
                                continue;

                $res = aReviewComplaint::Update($ID, $arFields);
        }
}
if(($arID = $lAdmin->GroupAction()))
{
                if($_REQUEST['action_target']=='selected')
                {
                                $rsData = aReviewComplaint ::GetList(Array($by=>$order), $arFilter);
                                while($arRes = $rsData->Fetch())
                                                $arID[] = $arRes['ID'];
                }

                foreach($arID as $ID)
                {
                                if(strlen($ID)<=0)
                                                continue;

                                switch($_REQUEST['action'])
                                {
                                                case "delete":
                                                                aReviewComplaint::Delete($ID);
                                                                break;
                                                case "delete_review":
                                                                if($arCC = aReviewComplaint::GetByID($ID)->Fetch())
                                                                {
                                                                        aReview::Delete($arCC["REVIEW_ID"]);
                                                                        aReviewComplaint::Update($ID, Array("ACTIVE"=>"N"));
                                                                }
                                                                break;
                                                case "active_status":
                                                                aReviewAuthor::SetStatus($ID, "A");
                                                                break;
                                                case "activate":
                                                case "deactivate":
                                                                aReviewComplaint::Update($ID, Array("ACTIVE"=>$_REQUEST['action']=="activate"?"Y":"N"));
                                                                break;

                                }
                }
}

// Fill list with data
$rsData = aReviewComplaint::GetList(Array($by=>$order), $arFilter);
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();

// Set page navigation
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("IMPROVED_REVIEW_AUTHORS_ADMIN_NAV")));

// List headers/columns
$arHeaders = array(
                array("id"=>"ID",               "content"=>"ID",                                                "sort"=>"id",                 "default"=>true),
                array("id"=>"ACTIVE",           "content"=>GetMessage("IMPROVED_REVIEW_HEAD_CC_ACTIVE"),       "sort"=>"active", "default"=>true),
                array("id"=>"DATE_CREATE",      "content"=>GetMessage("IMPROVED_REVIEW_HEAD_CC_DATE_CREATE"),  "sort"=>"date_create", "default"=>true),
                array("id"=>"ELEMENT",          "content"=>GetMessage("IMPROVED_REVIEW_HEAD_CC_ELEMENT"),      "sort"=>"ELEMENT", "default"=>true),
                array("id"=>"REVIEW",          "content"=>GetMessage("IMPROVED_REVIEW_HEAD_CC_COMMENT"),      "default"=>true),
                array("id"=>"MESSAGE",          "content"=>GetMessage("IMPROVED_REVIEW_HEAD_CC_MESSAGE"),      "default"=>true),
                array("id"=>"SENDER",           "content"=>GetMessage("IMPROVED_REVIEW_HEAD_CC_SENDER"),       "sort"=>"user_id", "align"=>"center", "default"=>true),
);
$lAdmin->AddHeaders($arHeaders);
$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();
while($arRes = $rsData->NavNext(true, "f_"))
{
                $row =& $lAdmin->AddRow($f_ID, $arRes);
                $row->AddCheckField("ACTIVE");

                $arReview = aReview::GetByID($f_REVIEW_ID,Array("MESSAGE_PLUS_HTML","MESSAGE_MINUS_HTML","MESSAGE_HTML","ELEMENT_ID","ID"))->Fetch();
                $REVIEW = "<strong>".GetMessage("IMPROVED_REVIEW_CC_LIST_PLUS").":</strong>".$arReview["MESSAGE_PLUS_HTML"]."<br>".
                        "<strong>".GetMessage("IMPROVED_REVIEW_CC_LIST_MINUS").":</strong>".$arReview["MESSAGE_MINUS_HTML"]."<br>".
                        "<strong>".GetMessage("IMPROVED_REVIEW_CC_LIST_COMMENT").":</strong>".$arReview["MESSAGE_HTML"];
                        
                $row->AddViewField("REVIEW", $REVIEW);

                if(in_array("ELEMENT", $arVisibleColumns) && CModule::IncludeModule("iblock"))
                {
                                if(!array_key_exists($arRes["ELEMENT_ID"], $arIBlockElementCache))
                                {
                                                $arIBlockElementCache[$arReview["ELEMENT_ID"]] = Array("NAME" => "", "DETAIL_PAGE_URL" => "");
                                                $obIBlockElement = CIBlockElement::GetList(Array(), Array("ID" => intval($arReview["ELEMENT_ID"])), false, false, Array("DETAIL_PAGE_URL", "ID", "IBLOCK_ID", "NAME"));
                                                if($arIBlockElement = $obIBlockElement->GetNext())
                                                {
                                                                $arIBlockElementCache[$arComment["ELEMENT_ID"]] = $arIBlockElement;
                                                }
                                }
                                $arIBlockElement = $arIBlockElementCache[$arComment["ELEMENT_ID"]];
                                $showField = '<a href="'.$arIBlockElement["DETAIL_PAGE_URL"].'#review_'.$f_REVIEW_ID.'" target="_blank">'.($arIBlockElement["NAME"]).'</a>';
                }
                $row->AddField("ELEMENT", $showField);

                if($f_USER_ID>0)
                {
                        if($arUser = CUser::GetByID($f_USER_ID)->Fetch())
                        {
                                $SENDER = '[<a href="/bitrix/admin/user_edit.php?ID='.$f_USER_ID.'&lang='.LANG.'" target="_blank">'.$arUser["LOGIN"].'</a>] '.$arUser["NAME"];
                        }
                }
                else
                {
                        $SENDER = $f_SENDER_NAME.' (<a href="mailto:'.$f_SENDER_EMAIL.'">'.$f_SENDER_EMAIL.'</a>)';
                }

                $row->AddViewField("SENDER", $SENDER);
}

// "footer" of the list
$lAdmin->AddFooter(Array(
        array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()),
        array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"),
));

// Add form with actions
$lAdmin->AddGroupActionTable(Array(
                "delete"=>GetMessage("MAIN_ADMIN_LIST_DELETE"),
                "delete_review"=>GetMessage("IMPROVED_REVIEW_CC_A_DELETE_COMMENT"),
                "activate"=>GetMessage("MAIN_ADMIN_LIST_ACTIVATE"),
                "deactivate"=>GetMessage("MAIN_ADMIN_LIST_DEACTIVATE"),
                ));

$aContext = array();
$lAdmin->AddAdminContextMenu($aContext);
$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("IMPROVED_REVIEW_CC_ADMIN_TITLE"));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>
<form name="filter_form" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?">
<?
$oFilter = new CAdminFilter(
                $sTableID."_filter",
                array(
                                GetMessage("IMPROVED_REVIEW_CC_F_ACTIVE"),
                                GetMessage("IMPROVED_REVIEW_CC_F_COMMENT_ID"),
                                GetMessage("IMPROVED_REVIEW_CC_F_SENDER"),
                )
);

$oFilter->Begin();
?>
        <tr>
                <td>ID:</td>
                <td><input type="text" name="find_id" value="<?echo htmlspecialcharsEx($find_id)?>" size="15"></td>
        </tr>

        <tr>
                <td><?echo GetMessage("IMPROVED_REVIEW_CC_F_ACTIVE");?>:</td>
                <td>
                        <?
                        $arr = array("reference" => array(GetMessage("MAIN_YES"),GetMessage("MAIN_NO"),),"reference_id" => array("Y","N"));
                        echo SelectBoxFromArray("find_active", $arr, $find_active, GetMessage("MAIN_ALL"), "");
                        ?>
                </td>
        </tr>
        <tr>
                <td><?echo GetMessage("IMPROVED_REVIEW_CC_F_COMMENT_ID")?>:</td>
                <td>
                        <input type="text" name="find_review_id" value="<?echo htmlspecialcharsEx($find_review_id)?>" size="15">
                </td>
        </tr>

        <tr>
                <td><?echo GetMessage("IMPROVED_REVIEW_CC_F_SENDER")?>:</td>
                <td>
                        <input type="text" name="find_sender" value="<?echo htmlspecialcharsEx($find_sender)?>" size="15">
                </td>
        </tr>
<?
$oFilter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage(), "form"=>"filter_form"));
$oFilter->End();
?>
</form>
<?
$lAdmin->DisplayList();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
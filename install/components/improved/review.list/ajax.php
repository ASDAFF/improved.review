<?
/**
 * Copyright (c) 1/8/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

use IMPROVED\Review as R;
$RID = (int)$_POST["RID"];
if(strlen($_POST["AJAX_ACTION"])>0 && $RID>0 && check_bitrix_sessid() && $arParams["ALLOW_EDIT"])
{
	switch($_POST["AJAX_ACTION"])
	{
		case "DELETE":
            R\ReviewTable::delete($RID);
			echo "ajAction = true";
			break;
		case "HIDE":
			echo "ajAction = " .CUtil::PhpToJSObject(aReview::SetApproved($RID,false));
			break;
		case "APPROVED":
			echo "ajAction = " .CUtil::PhpToJSObject(aReview::SetApproved($RID,true));
			break;
        case 'SET_BEST':
            R\ReviewTable::update($RID,array('IS_BEST'=>'Y'));
            echo 'ajAction = true';
        break;
        case 'DEL_BEST':
            R\ReviewTable::update($RID,array('IS_BEST'=>'N'));
            echo 'ajAction = true';
        break;
        
	}
	if($arParams["SAVE_RATING"])
	{
		aReview::SaveRatingToIB($arParams["ELEMENT_ID"],$arParams["IBLOCK_ID"],$arParams["SAVE_RATING_IB_PROPERTY"]);
	}
    
    if($arParams["SAVE_COUNT"])
    {
        R\Tools::saveCountToProperty($arParams["ELEMENT_ID"],$arParams["IBLOCK_ID"],$arParams["SAVE_COUNT_IB_PROPERTY"]);
    }    			
    aReview::ClearCacheFull($RID);
	die();
}

if($_POST["VOTE"]=="Y" && (int)$_POST["RID"]>0 && $arParams["ALLOW_VOTE"])
{
	$APPLICATION->RestartBuffer();
	$allowVote = aReview::AllowVote($RID);
	$plus = ($_POST["PLUS"] == "Y") ? true : false;
	if($allowVote)
	{
		if(aReview::Vote($RID,$plus))
		{
				$arVoteResult = aReview::GetByID($RID,Array("ID","VOTE_MINUS","VOTE","VOTE_PLUS"))->fetch();
				$arVoteResult["TYPE"] = true;
				$arVoteResult["PLUS"] = $arVoteResult["VOTE_PLUS"];
                $arVoteResult["MINUS"] = $arVoteResult["VOTE_MINUS"];
                $arVoteResult["VOTE"] = $arVoteResult["VOTE"];
				echo "voteRes = " .CUtil::PhpToJSObject($arVoteResult);
		}
	}
	else
	{
		$arVoteResult = Array("TYPE"=>false,"ERR"=>"-");
		echo "voteRes = " .CUtil::PhpToJSObject($arVoteResult);
	}
	die();
}
?>
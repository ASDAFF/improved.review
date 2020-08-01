<?
/**
 * Copyright (c) 1/8/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

$improved_reviewWarningTmp = "";

if (CModule::IncludeModule("improved.review") && check_bitrix_sessid()):
	$arAltasibReviewFields = Array(
	);

	if ($USER->IsAdmin())
    {
		$arAltasibReviewFields["ALLOW_POST"] = (($improved_review_ALLOW_POST=="Y") ? "Y" : "N");
        $arAltasibReviewFields["MODERATE_POST"] = (($improved_review_MODERATE_POST=="Y") ? "Y" : "N");
    }
    
	$ob_res = aReview::GetUserByID($ID, Array("ID"));
	if ($ar_res=$ob_res->fetch())
	{
		aReview::UpdateUser($ar_res["ID"], $arAltasibReviewFields);
	}
    else
	{
		$arAltasibReviewFields["USER_ID"] = $ID;
        aReview::AddUser($arAltasibReviewFields);
	}
    $improved_review_res = true;
endif;
?>
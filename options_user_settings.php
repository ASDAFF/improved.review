<?
/**
 * Copyright (c) 1/8/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

include(GetLangFileName($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altsib.review/lang/", "/options_user_settings.php"));

if (CModule::IncludeModule("improved.review")):
    ClearVars("str_improved_review_");
    $data = aReview::GetUserList(array(), array("USER_ID" => $ID));
	if (!$arReviewUser = $data->fetch())
	{
			$arReviewUser['ALLOW_POST'] = "Y";
	}    
	if (strlen($strError)>0)
	{
		$DB->InitTableVarsForEdit("improved_review_user", "improved_review_", "str_improved_review_");
		$DB->InitTableVarsForEdit("b_user", "improved_review_", "str_improved_review_");
        
        $arReviewUser['ALLOW_POST'] = $str_improved_review_ALLOW_POST;
        $arReviewUser['MODERATE_POST'] = $str_improved_review_MODERATE_POST;
	}
	?>
	<input type="hidden" name="profile_module_id[]" value="improved.review">
	<?if ($USER->IsAdmin()):?>
		<tr>
			<td width="40%"><?=GetMessage("improved_review_ALLOW_POST")?></td>
			<td width="60%"><input type="checkbox" name="improved_review_ALLOW_POST" value="Y" <?if ($arReviewUser['ALLOW_POST']=="Y") echo "checked";?>></td>
		</tr>
		<tr>
			<td width="40%"><?=GetMessage("improved_review_MODERATE_POST")?></td>
			<td width="60%"><input type="checkbox" name="improved_review_MODERATE_POST" value="Y" <?if ($arReviewUser['MODERATE_POST']=="Y") echo "checked";?>></td>
		</tr>        
	<?endif;?>
	<?
endif;
?>
<?
/**
 * Copyright (c) 1/8/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

IncludeModuleLangFile(__FILE__);

$GROUP_RIGHT = $APPLICATION->GetGroupRight("improved.review");

if($GROUP_RIGHT>"D")
{
	$aMenu = array(
			"parent_menu" => "global_menu_services",
			"section" => "improved_review",
			"sort" => 10,
			"text" => GetMessage("IMPROVED_REVIEW_MENU_TEXT"),
			"title" => GetMessage("IMPROVED_REVIEW_MENU_TITILE"),
			"url" => "improved_review_index.php?lang=".LANGUAGE_ID,
			"icon" => "menu_improved_review",
			"page_icon" => "improved_review_page_icon",
			"items_id" => "improved_review",
			"items" => array()

			);
			if($GROUP_RIGHT >= "M")
			{
				$aMenu["items"][] = array(
					"text" => GetMessage("IMPROVED_REVIEW_MENU_SECTION_TEXT"),
					"url" => "improved_review_section.php?lang=".LANGUAGE_ID,
					"title" => GetMessage("IMPROVED_REVIEW_MENU_SECTION_TITLE")
				);
				$aMenu["items"][] = array(
					"text" => GetMessage("IMPROVED_REVIEW_MENU_LIST_TEXT"),
					"url" => "improved_review_list.php?lang=".LANGUAGE_ID,
					"title" => GetMessage("IMPROVED_REVIEW_MENU_LIST_TITLE")
				);												
			}

			if($GROUP_RIGHT >= "M")
				$aMenu["items"][] = array(
					"text" => GetMessage("IMPROVED_REVIEW_MENU_ABUSE_TEXT"),
					"url" => "improved_review_complaint.php?lang=".LANGUAGE_ID,
					"title" => GetMessage("IMPROVED_REVIEW_MENU_ABUSE_TITLE")
				);
            if($GROUP_RIGHT == "W")
            {
				$aMenu["items"][] = array(
					"text" => GetMessage("IMPROVED_REVIEW_MENU_RATING_TEXT"),
					"url" => "improved_review_rating.php?lang=".LANGUAGE_ID,
					"title" => GetMessage("IMPROVED_REVIEW_MENU_RATING_TITLE")
				);                
            }
                return $aMenu;
}
return false;
?>
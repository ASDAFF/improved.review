<?
/**
 * Copyright (c) 1/8/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
                "NAME" => GetMessage("IMPROVED_DESC_REVIEW_NAME"),
                "DESCRIPTION" => GetMessage("IMPROVED_DESC_REVIEW_DESCRIPTION"),
                "ICON" => "/images/icon.gif",
                "CACHE_PATH" => "Y",
                "PATH" => array(
                                "ID" => "IS-MARKET.RU",
                                "NAME" => GetMessage("IMPROVED_DESC_SECTION_NAME"),
                                "CHILD" => array(
                                                "ID" => "improved_review_cmpx",
                                                "NAME" => GetMessage("IMPROVED_DESC_REVIEW_SECTION_NAME"),
                                ),
                ),
);

?>
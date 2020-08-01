<?
/**
 * Copyright (c) 1/8/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

global $MESS;
$PathInstall = str_replace("\\", "/", __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen("/index.php"));
IncludeModuleLangFile(__FILE__);

Class improved_review extends CModule
{
                var $MODULE_ID = "improved.review";
                var $MODULE_VERSION;
                var $MODULE_VERSION_DATE;
                var $MODULE_NAME;
                var $MODULE_DESCRIPTION;
                var $MODULE_CSS;

                function improved_review()
                {
                    $arModuleVersion = array();

                    $path = str_replace("\\", "/", __FILE__);
                    $path = substr($path, 0, strlen($path) - strlen("/index.php"));
                    include($path."/version.php");

                    if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
                    {
                                    $this->MODULE_VERSION = $arModuleVersion["VERSION"];
                                    $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
                    }
                    else
                    {
                                    $this->MODULE_VERSION = "2.0.7";
                                    $this->MODULE_VERSION_DATE = "2009-10-21 00:00:00";
                    }

                    $this->MODULE_NAME = GetMessage("IMPROVED_REVIEW_MODULE_NAME");
                    $this->MODULE_DESCRIPTION = GetMessage("IMPROVED_REVIEW_MODULE_DESCRIPTION");
                    $this->MODULE_CSS = "/bitrix/modules/improved.review/install/themes/.default/improved.review.css";

                    $this->PARTNER_NAME = "ASDAFF";
                    $this->PARTNER_URI = "https://asdaff.github.io/";
                }
                function GetModuleRightList()
                {
                    global $MESS;
                    $arr = array(
                                    "reference_id" => array("D","M","W"),
                                    "reference" => array(
                                                    "[D] ".GetMessage("IMPROVED_REVIEW_INSTALL_ACCESS_DENIED"),
                                                    "[M] ".GetMessage("IMPROVED_REVIEW_INSTALL_MODERATION"),
                                                    "[W] ".GetMessage("IMPROVED_REVIEW_INSTALL_FULL_ACCESS"))
                                    );
                    return $arr;
                }
                function DoInstall()
                {
                    global $DB, $APPLICATION, $step;
                    $this->InstallFiles();
                    $this->InstallDB();
                    $this->InstallEvents();
                    $GLOBALS["errors"] = $this->errors;

                    $APPLICATION->IncludeAdminFile(GetMessage("IMPROVED_REVIEW_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/improved.review/install/step.php");
                }
                function DoUninstall()
                {
                    global $DB, $APPLICATION, $step;
                    $step = IntVal($step);
                    if($step<2)
                    {
                                    $APPLICATION->IncludeAdminFile(GetMessage("IMPROVED_REVIEW_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/improved.review/install/unstep1.php");
                    }
                    elseif($step==2)
                    {

                                    $this->UnInstallFiles();
                                    if($_REQUEST["saveemails"] != "Y")
                                                    $this->UnInstallEvents();

                                    $this->UnInstallDB(array(
                                                    "savedata" => $_REQUEST["savedata"],
                                    ));

                                    $GLOBALS["errors"] = $this->errors;

                                    $APPLICATION->IncludeAdminFile(GetMessage("IMPROVED_REVIEW_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/improved.review/install/unstep2.php");
                    }
                }
                function InstallDB()
                {
                    global $DB, $DBType, $APPLICATION;
                    $this->errors = false;

                    if(!$DB->Query("SELECT 'x' FROM improved_review", true))
                    {
                                    $this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/improved.review/install/db/".strtolower($DBType)."/install.sql");
                    }
                    if($this->errors !== false)
                    {
                                    $APPLICATION->ThrowException(implode("", $this->errors));
                                    return false;
                    }
                    RegisterModule("improved.review");
                    CModule::IncludeModule("improved.review");
                    RegisterModuleDependences("iblock", "OnAfterIBlockElementDelete", "improved.review", "aReview", "OnAfterIBlockElementDelete");
                    RegisterModuleDependences("main", "OnUserTypeBuildList", "improved.review", "aReviewRatingUF", "GetUserTypeDescription");
                    $this->InstallUserFields();
                }
                
                function InstallUserFields()
                {
                    global $APPLICATION,$USER_FIELD_MANAGER;
                    $USER_FIELD_MANAGER->CleanCache();
                    $USER_FIELD_MANAGER->arUserTypes = '';
                    
                    CModule::IncludeModule("improved.review");
                    AddEventHandler("main", "OnUserTypeBuildList", array("aReviewRatingUF", "GetUserTypeDescription"));
                    
                    $typeData = CUserTypeEntity::GetList(array(), array("ENTITY_ID" => "IMPROVED_REVIEW","FIELD_NAME" => "UF_RATING_PRICE",));
                    if (!($typeData = $typeData->Fetch()))
                    {
                        $obUserField  = new CUserTypeEntity;                                
                        $arFields = array(
                            "ENTITY_ID" => "IMPROVED_REVIEW",
                            "FIELD_NAME" => "UF_RATING_PRICE",
                            "USER_TYPE_ID" => "IMPROVED_REVIEW_RATING",
                            "SORT" => 100,
                            "SHOW_FILTER" => "N",
                            "EDIT_FORM_LABEL" => Array("ru"=>GetMessage("IMPROVED_UF_RATING_PRICE"),"en"=>"Price"),
                            "LIST_COLUMN_LABEL" => Array("ru"=>GetMessage("IMPROVED_UF_RATING_PRICE"),"en"=>"Price"),
                        );
                        $UF_ID = $obUserField->Add($arFields);
                        /*if (false == $UF_ID)
                        {
                            if ($strEx = $APPLICATION->GetException())
                            {
                                    $this->errors[] = $strEx->GetString();
                            }
                        }*/
                    }                    
                }
                function UnInstallDB($arParams = array())
                {
                                global $DB, $DBType, $APPLICATION;
                                $this->errors = false;
                                if(array_key_exists("savedata", $arParams) && $arParams["savedata"] != "Y")
                                {
                                    $this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/improved.review/install/db/".$DBType."/uninstall.sql");

                                    if($this->errors !== false)
                                    {
                                                    $APPLICATION->ThrowException(implode("", $this->errors));
                                                    return false;
                                    }
                                }

                                COption::RemoveOption("improved.review");
                                UnRegisterModuleDependences("iblock", "OnAfterIBlockElementDelete", "improved.review", "aReview", "OnAfterIBlockElementDelete");
                                UnRegisterModuleDependences("main", "OnUserTypeBuildList", "improved.review", "aReviewRatingUF", "GetUserTypeDescription");
                                UnRegisterModule("improved.review");

                                return true;

                }
                function InstallFiles()
                {
                                CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/improved.review/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true, true);
                                CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/improved.review/install/themes/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/", true, true);
                                CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/improved.review/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
                                CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/improved.review/install/templates/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/templates/", true, true);
                                return true;
                }

                function UnInstallFiles()
                {

                                DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/improved.review/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
                                DeleteDirFilesEx("/bitrix/themes/.default/icons/improved.review");
                                DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/improved.review/install/themes/.default", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default");//css
                                DeleteDirFilesEx("/bitrix/components/improved/review");
                                DeleteDirFilesEx("/bitrix/components/improved/review.add");
                                DeleteDirFilesEx("/bitrix/components/improved/review.list");
                                DeleteDirFilesEx("/bitrix/components/improved/review.rating");
                                DeleteDirFilesEx("/bitrix/components/improved/review.subs");
                                return true;
                }
                function InstallEvents()
                {
                                global $DB;
                                include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/improved.review/install/events.php");
                                return true;
                }

                function UnInstallEvents()
                {
                                global $DB;
                                $statusMes = Array();
                                $eventType = new CEventType;
                                $eventType->Delete("IMPROVED_REVIEW_ADD");

                                $statusMes[] = "IMPROVED_REVIEW_ADD";

                                foreach($statusMes as $v)
                                {
                                                $eventM = new CEventMessage;
                                                $dbEvent = CEventMessage::GetList($b="ID", $order="ASC", Array("EVENT_NAME" => $v));
                                                while($arEvent = $dbEvent->Fetch())
                                                {
                                                                $eventM->Delete($arEvent["ID"]);
                                                }
                                }
                                return true;
                }
}
?>
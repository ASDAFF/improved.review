<?
/**
 * Copyright (c) 1/8/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

Class aReviewComplaint extends aReviewComplaintMain
{
        Function GetList($arOrder = Array("ID"=>"ASC"), $arFilter=Array())
        {
                global $DB;
                $arSqlSearch = Array();
                $strSqlSearch = "";

                if (is_array($arFilter))
                {
                        $filter_keys = array_keys($arFilter);

                        for ($i=0; $i<count($filter_keys); $i++)
                        {
                                $val = $arFilter[$filter_keys[$i]];

                                if (strlen($val)<=0 || $val."!"=="NOT_REF!") continue;

                                switch(strtoupper($filter_keys[$i]))
                                {
                                        case "ID":
                                                $arSqlSearch[] = GetFilterQuery("RC.ID",$val,"N");
                                        break;

                                        case "COMMENT_ID":
                                                $arSqlSearch[] = GetFilterQuery("RC.COMMENT_ID",$val,"N");
                                        break;

                                        case "USER_ID":
                                                $arSqlSearch[] = GetFilterQuery("RC.USER_ID",$val,"N");
                                        break;

                                        case "ACTIVE":
                                                if(in_array($val, Array("N", "Y")))
                                                        $arSqlSearch[] = GetFilterQuery("RC.ACTIVE", $val, "Y");
                                        break;

                                        case "SENDER":
                                                if(is_numeric($val))
                                                        $arSqlSearch[] = GetFilterQuery("RC.USER_ID",$val,"N");
                                                else
                                                {
                                                        $arSqlSearch[] = "( RC.SENDER_EMAIL LIKE '%".$DB->ForSql($val)."%' OR RC.SENDER_NAME LIKE '%".$DB->ForSql($val)."%' ) ";
                                                }
                                        break;
                                }
                        }
                }

               $arSqlOrder = Array();

                foreach($arOrder as $by=>$order)
                {
                        $by = strtoupper($by);
                        $order = strtoupper($order);

                        if ($order!="ASC")
                        $order = "DESC";

                        if ($by == "ID")                $arSqlOrder[] = " RC.ID ".$order." ";
                        elseif ($by == "ACTIVE")                $arSqlOrder[] = " RC.ACTIVE ".$order." ";
                        elseif ($by == "COMMENT_ID")                $arSqlOrder[] = " RC.COMMENT_ID ".$order." ";
                        elseif ($by == "USER_ID")                $arSqlOrder[] = " RC.USER_ID ".$order." ";
                        elseif ($by == "DATE_CREATE")                $arSqlOrder[] = " RC.DATE_CREATE ".$order." ";

                }

                $strSqlOrder = "";
                DelDuplicateSort($arSqlOrder);

                for ($i=0; $i<count($arSqlOrder); $i++)
                {
                        if($i==0)
                                $strSqlOrder = " ORDER BY ";
                        else
                                $strSqlOrder .= ",";

                        $strSqlOrder .= $arSqlOrder[$i];
                }

                $strSqlSearch = GetFilterSqlSearch($arSqlSearch);

                $strSql = "
                SELECT DISTINCT RC.*
                FROM improved_review_complaint RC
                WHERE
                "
                .$strSqlSearch
                .$strSqlOrder;

                $res = $DB->Query($strSql, false, $err_mess.__LINE__);
                $res->is_filtered = (IsFiltered($strSqlSearch));

        return $res;
        }

        Function Add($arFields)
        {
                global $DB;

                if(!is_set($arFields, "ACTIVE"))
                        $arFields["ACTIVE"]="Y";
                elseif($arFields["ACTIVE"]!="Y")
                        $arFields["ACTIVE"]="N";

                if(intval($arFields["REVIEW_ID"])==0 || strlen($arFields["MESSAGE"])==0):
                        return false;
                else:
                        if(!isset($arFields["DATE_CREATE"]))
                                $arFields["DATE_CREATE"] = date("d.m.Y H:i:s");

                        if(!isset($arFields["SENDER_IP"]))
                                $arFields["SENDER_IP"] = $_SERVER["REMOTE_ADDR"];

                    $arInsert = $DB->PrepareInsert("improved_review_complaint", $arFields, "improved.review");

                    $strSql =
                        "INSERT INTO improved_review_complaint (".$arInsert[0].") ".
                        "VALUES (".$arInsert[1].")";

                    $DB->Query($strSql);
                    $ID = $DB->LastID();
                endif;

        return $ID;
        }

        Function Update($ID, $arFields)
        {
                global $DB;

                if(is_set($arFields, "ACTIVE") && $arFields["ACTIVE"]!="Y")
                        $arFields["ACTIVE"]="N";

                $ID = (int)$ID;

                if((isset($arFields["COMMENT_ID"]) && intval($arFields["COMMENT_ID"])==0) || (isset($arFields["MESSAGE"]) && strlen($arFields["MESSAGE"])==0)):
                        return false;
                else:
                    $arInsert = $DB->PrepareUpdate("improved_review_complaint", $arFields, "improved.review");

                    $strSql = "UPDATE improved_review_complaint SET ".$arInsert." WHERE ID=".$ID;

                    $DB->Query($strSql);
                    $Result = true;
                endif;

        return $Result;
        }

        Function Delete($ID)
        {
                global $DB, $APPLICATION;

                $ID = (int)$ID;
                $APPLICATION->ResetException();

                $strSql = "DELETE FROM improved_review_complaint WHERE ID=".$ID;
                $res = $DB->Query($strSql, false, $err_mess.__LINE__);
        return true;
        }
}
?>
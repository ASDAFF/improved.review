<?
/**
 * Copyright (c) 1/8/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

Class aReviewFile
{
        Function GetFiles($REVIEW_ID)
        {
                global $DB;

                $REVIEW_ID = (int)$REVIEW_ID;
                if($REVIEW_ID==0)
                        return Array();

                $arResult = Array();
                $strSql = "SELECT FILE_ID FROM improved_review_file WHERE REVIEW_ID=".$REVIEW_ID;
                $res = $DB->Query($strSql);
                $upload_dir = COption::GetOptionString("main", "upload_dir", "upload");
                while($arFid = $res->Fetch())
                {
                        $arFile = CFile::GetByID($arFid["FILE_ID"])->Fetch();
                        $arFile["SRC"] = "/".$upload_dir."/".$arFile["SUBDIR"]."/".$arFile["FILE_NAME"];
                        
                        if(!stristr($arFile["CONTENT_TYPE"], "image/"))
                            array_unshift($arResult,$arFile);
                        else
                            $arResult[] = $arFile;
                }
        return $arResult;
        }

        Function Delete($REVIEW_ID)
        {
                global $DB;
                $REVIEW_ID = (int)$REVIEW_ID;
                if($REVIEW_ID==0)
                        false;

                $strSql = "SELECT FILE_ID FROM improved_review_file WHERE REVIEW_ID=".$REVIEW_ID;
                $obCF = $DB->Query($strSql);
                $fe = false;
                while($arCF = $obCF->Fetch())
                {
                        CFile::Delete($arCF["FILE_ID"]);
                        $fe = true;
                }
                if($fe)
                {
                        $strSql = "DELETE FROM improved_review_file WHERE REVIEW_ID=".$REVIEW_ID;
                        $DB->Query($strSql);
                }

        return true;
        }
        
        Function DeleteByFileID($FILE_ID)
        {
                global $DB;
                $FILE_ID = (int)$FILE_ID;
                if($FILE_ID==0)
                        false;

                $strSql = "SELECT FILE_ID,ELEMENT_ID FROM improved_review_file WHERE FILE_ID=".$FILE_ID;
                $obCF = $DB->Query($strSql);
                if($arFile = $obCF->Fetch())
                {
                        CFile::Delete($arFile["FILE_ID"]);
                        $strSql = "DELETE FROM improved_review_file WHERE FILE_ID=".$FILE_ID;
                        $DB->Query($strSql);
                        aReview::ClearCache($arFile["ELEMENT_ID"]);
                return true;
                }
        }
}
?>
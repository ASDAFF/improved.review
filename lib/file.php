<?
/**
 * Copyright (c) 1/8/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

namespace IMPROVED\Review;

use \Bitrix\Main\Entity;
class FileTable extends Entity\DataManager
{
    public static function getFilePath()
    {
            return __FILE__;
    }

    public static function getTableName()
    {
            return 'improved_review_file';
    }

    public static function getMap()
    {
        return array(
            'ID' => array(
                    'data_type' => 'integer',
                    'primary' => true,
                    'autocomplete' => true,
            ),
            'REVIEW_ID' => array(
                    'data_type' => 'integer'
            ),                        

            'FILE_ID' => array(
                    'data_type' => 'integer'
            ),                        

            'USER_ID' => array(
                    'data_type' => 'integer'
            ),                        

            'ELEMENT_ID' => array(
                    'data_type' => 'integer'
            ),                        
            
            'TIMESTAMP_X' => array(
                    'data_type' => 'datetime',                                
            ),
            
            'HITS' => array(
                    'data_type' => 'integer'
            ),                        
        );
    }
    
    public static function getFiles($REVIEW_ID)
    {
        $upload_dir = \COption::GetOptionString("main", "upload_dir", "upload");
        $arResult = Array();
        
        $data = self::getList(array(
            'select'=>array('FILE_ID'),
            'filter'=>array('REVIEW_ID'=>$REVIEW_ID),
        ));
        
        while($arFid = $data->Fetch())
        {
                $arFile = \CFile::GetByID($arFid["FILE_ID"])->Fetch();
                $arFile["SRC"] = "/".$upload_dir."/".$arFile["SUBDIR"]."/".$arFile["FILE_NAME"];
                
                if(!stristr($arFile["CONTENT_TYPE"], "image/"))
                    array_unshift($arResult,$arFile);
                else
                    $arResult[] = $arFile;
        }
    return $arResult;        
    }
    
    public static function delete($ID)
    {
        if($data = self::getRow(array(
                'filter'=>array('ID'=>$ID),
                'select'=>array('ID','FILE_ID')
            )))
        {
            \CFile::Delete($data["FILE_ID"]);
            parent::delete($ID);
        }
    }
    
    public static function deleteByReview($ID)
    {
        $ob = self::getList(array(
                'filter'=>array('REVIEW_ID'=>$ID),
                'select'=>array('ID','FILE_ID')
        ));
        while($data = $ob->fetch())
        {
            \CFile::Delete($data["FILE_ID"]);
            parent::delete($data['ID']);
        }        
    }
}
?>
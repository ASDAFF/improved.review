<?
/**
 * Copyright (c) 1/8/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

namespace IMPROVED\Review;

if (!\CModule::IncludeModule('improved.review'))
        return;

use \Bitrix\Main;
use \Bitrix\Main\Entity;
class ReviewTable extends Entity\DataManager
{
        public static function getFilePath()
        {
                return __FILE__;
        }

        public static function getTableName()
        {
                return 'improved_review';
        }

        public static function getMap()
        {
            global $DB;
                return array(
                        'ID' => array(
                                'data_type' => 'integer',
                                'primary' => true,
                                'autocomplete' => true,
                        ),
                        'ELEMENT_ID' => array(
                                'data_type' => 'integer'
                        ),                        
                        
                        'APPROVED' => array(
                                'data_type' => 'boolean',
                                'values' => array('N', 'Y')
                        ),                       
                        'USER_ID' => array(
                                'data_type' => 'integer'
                        ),                        
                        
                        'USER' => array(
                                'data_type' => 'Bitrix\Main\User',
                                'reference' => array('=this.USER_ID' => 'ref.ID')
                        ),                                               
                        'SITE_ID' => array(
                                'data_type' => 'string'
                        ),
                        'AUTHOR_ID' => array(
                                'data_type' => 'integer'
                        ),                        
                        'AUTHOR_NAME' => array(
                                'data_type' => 'string'
                        ),
                        'AUTHOR_EMAIL' => array(
                                'data_type' => 'string'
                        ),
                        'AUTHOR_IP' => array(
                                'data_type' => 'string'
                        ),                                                                                                
                        'POST_DATE' => array(
                                'data_type' => 'datetime',                                
                        ),
                        'DATE' => array(
                                'data_type' => 'datetime',                                
                                'expression' => array(
                                 str_replace('%%ss', '%s', str_replace('%','%%',$DB->DateToCharFunction('%ss','FULL'))), 'POST_DATE'
                                )
                        ),                                                        
                        'MESSAGE_PLUS' => array(
                                'data_type' => 'string'
                        ),
                        'MESSAGE_PLUS_HTML' => array(
                                'data_type' => 'string'
                        ),
                        'MESSAGE_MINUS' => array(
                                'data_type' => 'string'
                        ),
                        'MESSAGE_MINUS_HTML' => array(
                                'data_type' => 'string'
                        ),
                        'MESSAGE' => array(
                                'data_type' => 'string'
                        ),
                        'MESSAGE_HTML' => array(
                                'data_type' => 'string'
                        ),                                                                                                                                                
                        'TITLE' => array(
                                'data_type' => 'string'
                        ),                        
                        'REPLY' => array(
                                'data_type' => 'string'
                        ),
                        'REPLY_HTML' => array(
                                'data_type' => 'string'
                        ),                                                
                        'IS_SEND' => array(
                                'data_type' => 'boolean',
                                'values' => array('N', 'Y')
                        ),                        
                        'VOTE_MINUS' => array(
                                'data_type' => 'integer'
                        ),
                        'VOTE_PLUS' => array(
                                'data_type' => 'integer'
                        ),                                                
                        'RATING' => array(
                                'data_type' => 'integer'
                        ),                        
                        'ONLY_RATING' => array(
                                'data_type' => 'boolean',
                                'values' => array('N', 'Y')
                        ),
                        'IS_BEST' => array(
                                'data_type' => 'boolean',
                                'values' => array('N', 'Y')
                        ),
            			'UF' => array(
            				'data_type' => 'IMPROVED\Review\UF',
            				'reference' => array('=this.ID' => 'ref.VALUE_ID')
            			),
                        'CNT' => array('expression' => array('COUNT(*)'), 'data_type'=>'integer'),
                        'VOTE' => array('expression' => array('(%s) - (%s)', 'VOTE_PLUS', 'VOTE_MINUS'), 'data_type'=>'integer') 
                );
        }
        
        public static function count($filter)
        {
            $data = self::getList(array('filter'=>$filter,'select'=>array('CNT')));
            if($result = $data->fetch())
                return $result['CNT'];
        return 0;
        }
        
        public static function update($ID,$data)
        {
            $res = parent::update($ID,$data);
            if($res->IsSuccess())
            {
                Tools::clearCacheFull($ID);
            }
            return $res;
        }
        
        public static function delete($ID)
        {
            if($data = self::getRow(array(
                'filter'=>array('ID'=>$ID),
                'select'=>array('ID','ELEMENT_ID')
            )))
            {
                $connection = Main\Application::getConnection();
                $connection->query('DELETE FROM improved_review_vote_to_review WHERE REVIEW_ID = '.$data["ID"]);
                $connection->query('DELETE FROM improved_review_rating2element WHERE ELEMENT_ID = '.$data["ELEMENT_ID"]);
                
                $GLOBALS["USER_FIELD_MANAGER"]->Delete("IMPROVED_REVIEW", $ID);
				if (Main\Loader::includeModule("search") && \COption::GetOptionString("improved.review","indexing","Y")=="Y")
					\CSearch::DeleteIndex("improved.review", $ID);

                FileTable::deleteByReview($ID);
                parent::delete($ID);
                Tools::clearCache($data['ELEMENT_ID']);
            }
        }
        
        public static function deleteByElement($ID)
        {
            $ob = self::getList(array(
                    'filter'=>array('ELEMENT_ID'=>$ID),
                    'select'=>array('ID')
            ));
            while($data = $ob->fetch())
            {
                parent::delete($data['ID']);
            }            
        }
}
?>
<?
/**
 * Copyright (c) 1/8/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

namespace IMPROVED\Review;

if (!\CModule::IncludeModule('improved.review'))
        return;

use Bitrix\Main\Entity;

Class UFTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'b_uts_improved_review';
	}

	public static function getUfId()
	{
		return 'IMPROVED_REVIEW';
	}

	public static function isUts()
	{
		return true;
	}

	public static function getMap()
	{
		/** @global CUserTypeManager $USER_FIELD_MANAGER */
		global $USER_FIELD_MANAGER;

		// get ufields
		$fieldsMap = $USER_FIELD_MANAGER->getUserFields(static::getUfId());
		foreach ($fieldsMap as $k => $v)
		{
			if ($v['MULTIPLE'] == 'Y')
			{
				unset($fieldsMap[$k]);
			}
            else
            {
                $data_type = "string";
                if($v["USER_TYPE"]["BASE_TYPE"]=="int")
                    $data_type = "integer";
                    
                $fieldsMap[$k] = array(
        			'data_type' => $data_type,
        		);
            }
		}
		$fieldsMap['VALUE_ID'] = array(
			'data_type' => 'integer',
			'primary' => true
		);        
		return $fieldsMap;
	}    
}
?>
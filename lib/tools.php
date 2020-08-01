<?
/**
 * Copyright (c) 1/8/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

namespace IMPROVED\Review;

use \Bitrix\Main\Config;

class Tools
{
	Function clearCacheFull($ID)
	{		
		if($arReview = ReviewTable::getList(array('filter'=>array('ID'=>$ID),'select'=>array('ELEMENT_ID')))->fetch())
			self::ClearCache($arReview["ELEMENT_ID"]);
        else
            self::ClearCache(0,SITE_ID);
	}
	Function clearCache($ELEMENT_ID,$SITE_ID = SITE_ID)
	{
			if($ELEMENT_ID>0)
			{
				BXClearCache(true, "/".$SITE_ID."/improved/review.list/".$ELEMENT_ID."/");
				BXClearCache(true, "/".$SITE_ID."/improved/review.rating/".$ELEMENT_ID."/");
			}	
			else
			{
				BXClearCache(true, "/".$SITE_ID."/improved/review.list/");
				BXClearCache(true, "/".$SITE_ID."/improved/review.rating/");
			}
	return true;
	}
    
    public static function saveCountToProperty($ELEMENT_ID,$IBLOCK_ID,$PROPERTY)
    {
        if((int)$ELEMENT_ID==0)
            return false;
        
        if(self::checkPropertyExist($IBLOCK_ID,$PROPERTY))
        {
            \CIBlockElement::SetPropertyValues($ELEMENT_ID, $IBLOCK_ID, ReviewTable::count(array('ELEMENT_ID'=>$ELEMENT_ID,'APPROVED'=>'Y')), $PROPERTY);
        }
    }
    private static function checkPropertyExist($IBLOCK_ID,$PROPERTY)
    {
		$IBLOCK_ID = (int)$IBLOCK_ID;
		$PROPERTY = trim($PROPERTY);
        if($PROPERTY=="" || $IBLOCK_ID==0)
                return false;
			
        if (\Bitrix\Main\Loader::includeModule('iblock'))
        {
            if(!\CIBlockProperty::GetList(Array(), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$IBLOCK_ID,"CODE"=>$PROPERTY))->Fetch())
            {
            	$arFields = Array("NAME" =>$PROPERTY,"ACTIVE" =>"Y","CODE" => $PROPERTY,"PROPERTY_TYPE" => "S","IBLOCK_ID" => $IBLOCK_ID);
            	$ibp = new \CIBlockProperty;
            	$PropID = $ibp->Add($arFields);
            	if(!$PropID)
            		return false;
            }
            else
                return true;                
        }
    return false;        
    }
	public static function getAllowTags()
	{
		return array("HTML" => "N",
			"ANCHOR" => Config\Option::get("improved.review", "FORM_ALLOW_ANCHOR", "Y"),
			"BIU" => Config\Option::get("improved.review", "FORM_ALLOW_BIU", "Y"),
			"IMG" => Config\Option::get("improved.review", "FORM_ALLOW_IMG", "Y"),
			"QUOTE" => Config\Option::get("improved.review", "FORM_ALLOW_QUOTE", "Y"),
			"CODE" => Config\Option::get("improved.review", "FORM_ALLOW_CODE", "N"),
			"FONT" => Config\Option::get("improved.review", "FORM_ALLOW_FONT", "N"),
			"LIST" => Config\Option::get("improved.review", "FORM_ALLOW_LIST", "Y"),
			"SMILES" => Config\Option::get("improved.review", "FORM_ALLOW_SMILE", "Y"),
			"NL2BR" => Config\Option::get("improved.review", "FORM_ALLOW_NL2BR", "Y"),
			"VIDEO" => Config\Option::get("improved.review", "FORM_ALLOW_VIDEO", "N"),
			"TABLE" => Config\Option::get("improved.review", "FORM_ALLOW_TABLE", "N"),
			"CUT_ANCHOR" => "N",
			"ALIGN" => Config\Option::get("improved.review", "FORM_ALLOW_ALIGN", "Y")
		);

	}

	public static function showLHE($ID,$CONTENT,$INPUT,$height = 100,$jsObjName = "oLHErw")
	{
		\Bitrix\Main\Loader::includeModule("fileman");
		$Editor = new \CHTMLEditor;
		$controlsMap = array();
		$allowTags = self::getAllowTags();
		foreach($allowTags as $key=>$val)
		{
			if($key == 'BIU' && $val=='Y')
			{
				$controlsMap[] = array('id' => 'Bold',  'compact' => true, 'sort' => 80);
				$controlsMap[] = array('id' => 'Bold',  'compact' => true, 'sort' => 80);
				$controlsMap[] = array('id' => 'Italic',  'compact' => true, 'sort' => 90);
				$controlsMap[] = array('id' => 'Underline',  'compact' => true, 'sort' => 100);
				$controlsMap[] = array('id' => 'Strikeout',  'compact' => true, 'sort' => 110);
				$controlsMap[] = array('id' => 'RemoveFormat',  'compact' => true, 'sort' => 120);
				$controlsMap[] = array('id' => 'AlignList', 'compact' => false, 'sort' => 125);
			}

			if($key == 'FONT' && $val=='Y')
			{
				$controlsMap[] = array('id' => 'Color',  'compact' => true, 'sort' => 130);
				$controlsMap[] = array('id' => 'FontSelector',  'compact' => false, 'sort' => 135);
				$controlsMap[] = array('id' => 'FontSize',  'compact' => false, 'sort' => 140);
				$controlsMap[] = array('separator' => true, 'compact' => false, 'sort' => 145);
			}

			if($key == 'LIST' && $val=='Y')
			{
				$controlsMap[] = array('id' => 'OrderedList',  'compact' => true, 'sort' => 150);
				$controlsMap[] = array('id' => 'UnorderedList',  'compact' => true, 'sort' => 160);
				$controlsMap[] = array('separator' => true, 'compact' => false, 'sort' => 200);
			}

			if($key == 'ANCHOR' && $val=='Y')
				$controlsMap[] = array('id' => 'InsertLink',  'compact' => true, 'sort' => 210, 'wrap' => 'bx-b-link-'.$ID);
			if($key == 'IMG' && $val=='Y')
				$controlsMap[] = array('id' => 'InsertImage',  'compact' => false, 'sort' => 220);
			if($key == 'VIDEO' && $val=='Y')
				$controlsMap[] = array('id' => 'InsertVideo',  'compact' => true, 'sort' => 230, 'wrap' => 'bx-b-video-'.$ID);
			if($key == 'TABLE' && $val=='Y')
				$controlsMap[] = array('id' => 'InsertTable',  'compact' => false, 'sort' => 250);
			if($key == 'CODE' && $val=='Y')
				$controlsMap[] = array('id' => 'Code',  'compact' => true, 'sort' => 260);
			if($key == 'QUOTE' && $val=='Y')
				$controlsMap[] = array('id' => 'Quote',  'compact' => true, 'sort' => 270, 'wrap' => 'bx-b-quote-'.$ID);

			$controlsMap[] = array('id' => 'Fullscreen',  'compact' => false, 'sort' => 310);

			$controlsMap[] = array('id' => 'BbCode',  'compact' => true, 'sort' => 340);
		}
		$controlsMap[] = array('id' => 'quickAnswer', 'compact' => false, 'sort' => 340);

		$Editor->Show(array(
			'name' => $INPUT,
			'inputName' => $INPUT,
			'id' => $ID,
			'siteId' => SITE_ID,
			'width' => '100%',
			'height' => $height."px",
			'minHeight' => $height."px",
			'content' => htmlspecialcharsBack($CONTENT),
			'jsObjName' => $jsObjName,
			'bAllowPhp' => false,
			'limitPhpAccess' => false,
			'showTaskbars' => false,
			'showNodeNavi' => false,
			'askBeforeUnloadPage' => false,
			//'arSmiles' => $arParams["SMILES"]["VALUE"],
			'bbCode' => true,
			'autoResize' => true,
			'autoResizeOffset' => 40,
			'saveOnBlur' => true,
			'iframeCss' => 'body{font-family: "Helvetica Neue",Helvetica,Arial,sans-serif; font-size: 13px;}',
			'minBodyWidth' => 350,
			'normalBodyWidth' => 555,
			'controlsMap' => $controlsMap,
		));
	}
}
?>
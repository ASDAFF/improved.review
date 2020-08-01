<?
global $DBType;
IncludeModuleLangFile(__FILE__);

$arClassesList = array(
				"aReviewMain" => "classes/general/review.php",
                "aReviewSubsMain" => "classes/general/subs.php",
                
				"aReview" => "classes/".$DBType."/review.php",
                "aReviewComplaintMain" => "classes/general/complaint.php",
                "aReviewComplaint" => "classes/".$DBType."/complaint.php",
                "aReviewSubs" => "classes/".$DBType."/subs.php",

                "IMPROVED\Review\ReviewTable" => "lib/review.php",
                "IMPROVED\Review\UFTable" => "lib/uf.php",
                "IMPROVED\Review\UserTable" => "lib/user.php",
                
);

// fix strange update bug
if (method_exists(CModule, "AddAutoloadClasses"))
{

                CModule::AddAutoloadClasses(
                                "improved.review",
                                $arClassesList
                );
}
else
{
                foreach ($arClassesList as $sClassName => $sClassFile)
                {
                                require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/improved.review/".$sClassFile);
                }
}

class aReviewTextParser extends CTextParser
{
                var $LAST_ERROR  = "";
                var $author_name  = "";
                var $postDate  = "";
                var $quote_error = 0;
                var $quote_open = 0;
                var $quote_closed = 0;
                var $allow_img_ext = "gif|jpg|jpeg|png";

                function aReviewTextParser()
                {
                        $this->CTextParser();
                        $this->parser_nofollow = "N";
                }

                Function GetSmilesBase($additional = true,$arOrder = Array())
                {
						$SmiltePath = COption::GetOptionString("improved.review","smile_pack_path","main");

						$obCache = new CPHPCache;
						$life_time = 2678400;
						$cache_id = "ALXSmileReview".SITE_ID.implode(",",$arOrder).$SmiltePath;
						
						if(CModule::IncludeModule("blog") && $SmiltePath == "blog")
						{
								if($obCache->InitCache($life_time, $cache_id, "/improved/review/smile/")) :
									$vars = $obCache->GetVars();
									$arrSmiley = $vars["SMILE"];
								else :
									$rsSites = CSite::GetByID(SITE_ID);
									$arSite = $rsSites->Fetch();
							
									$arrSmiley = Array();
									$obBS = CBlogSmile::GetList($arOrder);
									while($arBS = $obBS->Fetch())
									{							
										if(strlen($arBS["TYPING"])==0 || $arBS["SMILE_TYPE"]!="S")
											continue;
											
										$arSmileLang = CBlogSmile::GetLangByID($arBS["ID"], $arSite["LANGUAGE_ID"]);									
										$arC = explode(" ",$arBS["TYPING"]);
										$arrSmiley[] = Array("ARR_CODE"=>$arC,"URL"=>"/bitrix/images/blog/smile/".$arBS["IMAGE"],"CODE"=>isset($arC[1])? $arC[1] :$arC[0],"DESCR"=>htmlspecialcharsEx($arSmileLang["NAME"]));
									}
									if($obCache->StartDataCache()):
											$obCache->EndDataCache(array(
												  "SMILE"    => $arrSmiley
												  ));
									endif;
								endif;
								
						return $arrSmiley;
						}

						if(CModule::IncludeModule("forum") && $SmiltePath == "forum")
						{
								if($obCache->InitCache($life_time, $cache_id, "/improved/review/smile/")) :
									$vars = $obCache->GetVars();
									$arrSmiley = $vars["SMILE"];
								else :
										$rsSites = CSite::GetByID(SITE_ID);
										$arSite = $rsSites->Fetch();
								
										$arrSmiley = Array();
										$obFS = CForumSmile::GetList($arOrder);
										while($arFS = $obFS->Fetch())
										{
											if(strlen($arFS["TYPING"])==0 || $arFS["TYPE"]!="S")
												continue;
												
											$arSmileLang = CForumSmile::GetLangByID($arFS["ID"], $arSite["LANGUAGE_ID"]);
											
											$arC = explode(" ",$arFS["TYPING"]);
											$arrSmiley[] = Array("ARR_CODE"=>$arC,"URL"=>"/bitrix/images/forum/smile/".$arFS["IMAGE"],"CODE"=>isset($arC[1])? $arC[1] :$arC[0],"DESCR"=>htmlspecialcharsEx($arSmileLang["NAME"]));
										}			
									if($obCache->StartDataCache()):
											$obCache->EndDataCache(array(
												  "SMILE"    => $arrSmiley
												  ));
									endif;
								endif;								
						return $arrSmiley;
						}

						//socialnetwork
						if(CModule::IncludeModule("socialnetwork") && $SmiltePath == "socialnetwork")
						{
								if($obCache->InitCache($life_time, $cache_id, "/improved/review/smile/")) :
									$vars = $obCache->GetVars();
									$arrSmiley = $vars["SMILE"];
								else :
										$rsSites = CSite::GetByID(SITE_ID);
										$arSite = $rsSites->Fetch();
								
										$arrSmiley = Array();
										$oSNS = CSocNetSmile::GetList($arOrder);
										while($arSNS = $oSNS->Fetch())
										{
											if(strlen($arSNS["TYPING"])==0 || $arSNS["SMILE_TYPE"]!="S")
												continue;
												
											$arSmileLang = CSocNetSmile::GetLangByID($arSNS["ID"], $arSite["LANGUAGE_ID"]);
											
											$arC = explode(" ",$arSNS["TYPING"]);
											$arrSmiley[] = Array("ARR_CODE"=>$arC,"URL"=>"/bitrix/images/socialnetwork/smile/".$arSNS["IMAGE"],"CODE"=>isset($arC[1])? $arC[1] :$arC[0],"DESCR"=>htmlspecialcharsEx($arSmileLang["NAME"]));
										}			
									if($obCache->StartDataCache()):
											$obCache->EndDataCache(array(
												  "SMILE"    => $arrSmiley
												  ));
									endif;
								endif;								
						return $arrSmiley;
						}				
						
					if($obCache->InitCache($life_time, $cache_id, "/improved/review/smile/")) :
						$vars = $obCache->GetVars();
						$arrSmiley = $vars["SMILE"];
					else :
                                static $arrSmiley = Array();
                                
                                $res = CSmile::getList(array('RETURN_RES' => 'Y',
                                    'FILTER'=>Array("TYPE"=>'S'),
                                    'ORDER'=>array('SORT'=>'ASC'),
                                    'SELECT'=>array('TYPING','IMAGE','NAME','SET_ID','IMAGE_WIDTH','IMAGE_HEIGHT')
                                    )
                                );
                                $uploadDirName = COption::GetOptionString("main", "upload_dir", "upload");
                                while($arSmile = $res->Fetch())
                                {
									$arC = explode(" ",$arSmile["TYPING"]);
                                    $smallSmile = '/'.$uploadDirName.'/resize_cache/'.CSmile::PATH_TO_SMILE.$arSmile['SET_ID'].'/'.$arSmile['IMAGE'];
                                    $smallSmileFP = $_SERVER["DOCUMENT_ROOT"].$smallSmile;
                                    if(!file_exists($smallSmileFP))
                                    { 
                                        CheckDirPath($smallSmile);
                                        $ResizedFile = CFile::ResizeImageFile($_SERVER["DOCUMENT_ROOT"].CSmile::PATH_TO_SMILE.$arSmile['SET_ID'].'/'.$arSmile['IMAGE'],$smallSmileFP,array('width'=>16,'height'=>16));
                                    }
									$arrSmiley[] = Array("ARR_CODE"=>$arC,
                                        "URL"=>$smallSmile ? $smallSmile : CSmile::PATH_TO_SMILE.$arSmile['SET_ID'].'/'.$arSmile['IMAGE'],
                                        "CODE"=>isset($arC[1])? $arC[1] :$arC[0],
                                        "DESCR"=>htmlspecialcharsEx($arSmile["NAME"]),
                                        'IMAGE_WIDTH'=>$arSmile['IMAGE_WIDTH'],
                                        'IMAGE_HEIGHT'=>$arSmile['IMAGE_HEIGHT'],
                                        
                                    );
                                }
                        if($obCache->StartDataCache()):
                            $obCache->EndDataCache(array(
                                  "SMILE"    => $arrSmiley
                                  ));
                        endif;
                endif;

                return $arrSmiley;
                }

                Function ParseSmile($text)
                {
                        $text = " ".$text." ";
                        $arrSmiley = aReviewTextParser::GetSmilesBase(true,Array("SORT"=>"DESC"));
                        foreach ($arrSmiley as $arrSmileyItem)
                        {
                                $text = str_ireplace($arrSmileyItem['ARR_CODE'], " <img src='".$arrSmileyItem['URL']."' alt='".$arrSmileyItem['DESCR']."' title='".$arrSmileyItem['DESCR']."' border='0' /> ", $text);
                        }

                return trim($text);
                }
                
				function convert($text, $allow, $type = "html")	//, "KEEP_AMP" => "N"
				{
				if(!is_array($allow))
					$allow = Array();

					if (!isset($this->image_params['width'])) $this->image_params['width'] = 100;
					if (!isset($this->image_params['height'])) $this->image_params['height'] = 100;
					if (!isset($this->image_params['template'])) $this->image_params['template'] = 'popup_image';
			
					$this->imageWidth = $this->image_params["width"];
					$this->imageHeight = $this->image_params["height"];
					$this->type = $type;
					if (is_array($allow) && sizeof($allow)>0)
					{
						if (!isset($allow['TABLE']))
							$allow['TABLE']=$allow['BIU'];
						$this->allow = $allow;
					}
					$text = $this->convertText($text);
					//$text = $this->ParseSmile($text);
					return $text;
				}                
}

Class aReviewRatingUF extends CUserTypeInteger
{
        Function GetUserTypeDescription()
        {
                return array(
                        "USER_TYPE_ID" => "IMPROVED_REVIEW_RATING",
                        "CLASS_NAME" => "aReviewRatingUF",
                        "DESCRIPTION" => GetMessage("IMPROVED_REVIEW_UF_RATING"),
                        "BASE_TYPE" => "int",
                );
        }
        function GetSettingsHTML($arUserField = false, $arHtmlControl, $bVarsFromForm)
        {}    
}
?>
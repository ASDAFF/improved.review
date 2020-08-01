<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
$isHidden = true;
if(strlen($arResult["ERROR_MESSAGE"])>0)
{
	ShowError($arResult["ERROR_MESSAGE"]);
$isHidden = false;
}
if($arParams["NOT_HIDE_FORM"] && $isHidden)
    $isHidden = false;

if(isset($_SESSION["REVIEW_ADD_OK"]) && $_SESSION["REVIEW_ADD_OK"])
{
	ShowNote($arParams["MESSAGE_OK"]);
	unset($_SESSION["REVIEW_ADD_OK"]);
    $isHidden = true;
}
?>
<div>
<?if($isHidden):?><div class="alx_add_reviews_a" id="review_show_form"><a href="javascript:void(0)" onclick="ShowReviewForm();"><?echo strlen($arParams["ADD_TITLE"]) ==0 ? GetMessage("IMPROVED_TP_ADD") : $arParams["ADD_TITLE"]?></a></div><?endif;?>
<div id="review_add_form" style="<?if($isHidden):?>display:none;<?endif;?>">
<form name="review_add" id="review_add" action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">
	<?=bitrix_sessid_post()?>
    <input type="hidden" value="<?=$arParams["ELEMENT_ID"];?>" name="ELEMENT_ID">
    <input type="hidden" value="<?=$arResult["arMessage"]["RATING"];?>" name="RATING" id="RATING">
	<div class="alx_reviews_block_border">&nbsp;</div>
<div class="alx_reviews_block">
	<div class="alx_reviews_form">
        <?if(!$USER->IsAuthorized()):?>
    		<div class="alx_reviews_form_item_pole alx_reviews_form_poles_small">
    			<div class="alx_reviews_form_pole_name"><span class="requred_txt">*</span> <?=GetMessage("IMPROVED_TP_NAME")?>:</div>
    			<div class="alx_reviews_form_inputtext_bg"><input type="text" name="AUTHOR_NAME" value="<?=$arResult["arMessage"]["AUTHOR_NAME"];?>" /></div>
				<div class="alx_clear_block">&nbsp;</div>
    		</div>
    		<div class="alx_reviews_form_item_pole alx_reviews_form_poles_small">
    			<div class="alx_reviews_form_pole_name"><span class="requred_txt">*</span> <?=GetMessage("IMPROVED_TP_EMAIL")?>:</div>
    			<div class="alx_reviews_form_inputtext_bg"><input type="text" name="AUTHOR_EMAIL" value="<?=$arResult["arMessage"]["AUTHOR_EMAIL"];?>" /></div>
				<div class="alx_clear_block">&nbsp;</div>
    		</div>
        <?endif;?>
        
        <?if($arParams["ALLOW_TITLE"]):?>
    		<div class="alx_reviews_form_item_pole alx_reviews_form_item_pole_last">
    			<div class="alx_reviews_form_pole_name"><span class="requred_txt">*</span> <?=GetMessage("IMPROVED_TP_TITLE")?>:</div>
    			<div class="alx_reviews_form_inputtext_bg"><div class="alx_reviews_form_inputtext_bg_arr"></div><input type="text" name="TITLE" value="<?=$arResult["arMessage"]["TITLE"];?>" /></div>
    		</div>
            <div class="alx_clear_block">&nbsp;</div>
		<?endif;?>
        <?if(!$arParams["COMMENTS_MODE"]):?>
        <div class="alx_reviews_form_item_pole_textarea">
			<div class="alx_reviews_form_pole_name"><span class="requred_txt">*</span> <?=GetMessage("IMPROVED_TP_PLUS")?>:</div>
			<div class="alx_reviews_form_textarea_bg">
                <?=\IMPROVED\Review\Tools::showLHE("MESSAGE_PLUS",$arResult["arMessage"]["MESSAGE_PLUS"],"MESSAGE_PLUS");?>
			</div>
            <?/*if($arParams["SHOW_CNT"]):?>
            <div class="alx_reviews_form_item_pole_textarea_dop_txt">
            <?=GetMessage("IMPROVED_TP_SCORE")?><span id="review_cnt_p" class="alx_reviews_red_txt">0</span>
            </div>
            <?endif;*/?>            
		</div>
		<div class="alx_reviews_form_item_pole_textarea">
			<div class="alx_reviews_form_pole_name"><span class="requred_txt">*</span> <?=GetMessage("IMPROVED_TP_MINUS")?>:</div>
			<div class="alx_reviews_form_textarea_bg">
                <?=\IMPROVED\Review\Tools::showLHE("MESSAGE_MINUS",$arResult["arMessage"]["MESSAGE_MINUS"],"MESSAGE_MINUS");?>
			</div>
            <?/*if($arParams["SHOW_CNT"]):?>
            <div class="alx_reviews_form_item_pole_textarea_dop_txt">
            <?=GetMessage("IMPROVED_TP_SCORE")?><span id="review_cnt_m" class="alx_reviews_red_txt">0</span>
            </div>
            <?endif;*/?>
		</div>
        <?endif;?>
		<div class="alx_reviews_form_item_pole_textarea">
			<div class="alx_reviews_form_pole_name"><span class="requred_txt">*</span> <?=GetMessage("IMPROVED_TP_COMMENT")?>:</div>
			<div class="alx_reviews_form_textarea_bg">
                <?=\IMPROVED\Review\Tools::showLHE("MESSAGE",$arResult["arMessage"]["MESSAGE"],"MESSAGE",200,"oLHErwc");?>
			</div>
			<div class="alx_reviews_form_item_pole_textarea_dop_txt">
				<?if($arParams["MIN_LENGTH"]>0):?><?=GetMessage("IMPROVED_TP_MIN_L")?> <span class="alx_reviews_red_txt"><?=$arParams["MIN_LENGTH"]?></span> <?=GetMessage("IMPROVED_TP_S")?>. <br /><?endif;?>
				<?if($arParams["MAX_LENGTH"]>0):?><?=GetMessage("IMPROVED_TP_MAX_L")?> <?=$arParams["MAX_LENGTH"]?> <?=GetMessage("IMPROVED_TP_S")?>. <?=GetMessage("IMPROVED_TP_R")?>: <span id="review_max_cnt" class="alx_reviews_red_txt"><?=$arParams["MAX_LENGTH"]?></span>.<?endif;?>
                <?if($arParams["SHOW_CNT"]):?><?=GetMessage("IMPROVED_TP_SCORE")?><span id="review_cnt_c" class="alx_reviews_red_txt">0</span><?endif;?>
			</div>

            <?if($arParams["ALLOW_UPLOAD_FILE"]):?>
                    <?
                    $APPLICATION->IncludeComponent("bitrix:main.file.input","",Array(
                        "ALLOW_UPLOAD"=>"F",
                        "ALLOW_UPLOAD_EXT" => $arParams["UPLOAD_FILE_TYPE"],
                        "MAX_FILE_SIZE" => $arParams["UPLOAD_FILE_SIZE"]*1024,
                        "INPUT_NAME"=>"FILES",
                        "INPUT_NAME_UNSAVED"=>"FILES_TMP",
                        "MULTIPLE"=>"Y",
                        "MODULE_ID"=>"improved.review",
                        'CONTROL_ID' => 'reviewFileAdd'
                        )
                    );
                    ?>
                    
            <div><?=GetMessage('IMPROVED_TP_FILE_EXT',Array('#FILE_EXT#'=>$arParams["UPLOAD_FILE_TYPE"]))?></div>                                    
            <div><?=GetMessage('IMPROVED_TP_FILE_SIZE',Array('#FILE_SIZE#'=>CFile::FormatSize(intval($arParams["UPLOAD_FILE_SIZE"]*1024), 0)))?></div>
            <?endif;?>
            
			<div class="alx_clear_block">&nbsp;</div>
		</div>
        <?if($arParams['SHOW_VOTE_BLOCK']):?>
        <div class="alx_reviews_form_vote" id="alx_reviews_form_vote">
            <div class="alx_reviews_form_vote_group_name"><?=GetMessage("IMPROVED_TP_VOTE")?>:</div>
            <?if($arParams['SHOW_MAIN_RATING']):?>
              	<div class="alx_reviews_form_pole_name"><?if($arParams["REQUIRED_RATING"]):?><span class="requred_txt">*</span> <?endif;?><?=GetMessage("IMPROVED_TP_RATING")?>:</div>
       		     	<div class="alx_reviews_form_vote_items" onmouseout="jsReviewVote.Restore();">
                        <?
                            for($i=1; $i<=5; $i++):
                                $class = "alx_reviews_form_vote_item";
                                
                                if($arResult["arMessage"]["RATING"]>0 && $i<=$arResult["arMessage"]["RATING"]):
                                    $class = "alx_reviews_form_vote_item alx_reviews_form_vote_item_sel";
                        ?>
                                    <script>
                                    jsReviewVote.Set(<?=$i?>,'RATING',0);
                                    </script>                        
                                <?endif;?>
        				    <div id="improved_item_vote_0_<?=$i?>" onmouseover="jsReviewVote.Curr(<?=$i?>,0)" onmouseout="jsReviewVote.Out(0)" onclick="jsReviewVote.Set(<?=$i?>,'RATING',0)" class="<?=$class?>"></div>
                        <?endfor;?>
                    </div>
    			<div class="alx_clear_block">&nbsp;</div>
            <?endif;?>
            <?if(count($arParams["USER_FIELDS_RATING"]) > 0):?>
                <?foreach($arParams["USER_FIELDS_RATING"] as $FIELD_NAME=>$arUserField):?>
                <div class="alx_reviews_form_pole_name"><?if ($arUserField["MANDATORY"]=="Y"):?><span class="requred_txt">*</span><?endif;?> <?=$arUserField["EDIT_FORM_LABEL"]?>:</div>
                <?$APPLICATION->IncludeComponent(
                       "bitrix:system.field.edit",
                       $arUserField["USER_TYPE"]["USER_TYPE_ID"],
                       array("bVarsFromForm" => $arResult, "arUserField" => $arUserField), null, array("HIDE_ICONS"=>"Y"));?>            
					   <div class="alx_clear_block">&nbsp;</div>
                <?endforeach;?>
				
            <?endif;?> 
			<div class="alx_clear_block">&nbsp;</div>			
		</div>
        <?endif;?>
        <?if(count($arParams["USER_FIELDS"]) > 0):?>
		<div class="alx_reviews_form_poles_group">
			<div class="alx_reviews_form_poles_group_border_top">&nbsp;</div>
            <?foreach($arParams["USER_FIELDS"] as $FIELD_NAME=>$arUserField):?>
            <div class="alx_reviews_form_item_pole_uf">
                <div class="alx_reviews_form_pole_name"><?if ($arUserField["MANDATORY"]=="Y"):?><span class="requred_txt">*</span><?endif;?> <?=$arUserField["EDIT_FORM_LABEL"]?>:</div>
               <?
                    if($arUserField["USER_TYPE"]["USER_TYPE_ID"]=="string_formatted")
                        $classUF = "alx_reviews_form_textarea_bg";
                    elseif($arUserField["USER_TYPE"]["USER_TYPE_ID"]=="IMPROVED_REVIEW_RATING")
                        $classUF = "alx_reviews_form_field_vote";
                    else
                        $classUF = "alx_reviews_form_field";
                ?>
                <div class="<?=$classUF?>">
					<?$APPLICATION->IncludeComponent(
                       "bitrix:system.field.edit",
                       $arUserField["USER_TYPE"]["USER_TYPE_ID"],
                       array("bVarsFromForm" => $arResult, "arUserField" => $arUserField), null, array("HIDE_ICONS"=>"Y"));
                    ?>
                </div>
				 <div class="alx_clear_block"></div>
            </div>
           
            <?endforeach;?>
			<div class="alx_reviews_form_poles_group_border_bottom">&nbsp;</div>
		</div>
        <?endif;?>
                
        <?if ($arParams["USE_CAPTCHA"] == "Y"):?>
            <div class="alx_reviews_form_captcha">
    				<div class="alx_reviews_form_pole_name"><span class="requred_txt">*</span> <?=GetMessage('IMPROVED_TP_CONFIRM')?><br />&nbsp; <?=GetMessage('IMPROVED_TP_CONFIRM_CODE')?>:</div>
					<div class="alx_reviews_form_captcha_pole">
						<div class="alx_reviews_form_inputtext_bg"><input type="text" name="captcha_word" /></div>
					</div>
    				<div class="alx_reviews_form_captcha_pic">
                        <input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
    					<div class="alx_reviews_form_captcha_pic_block"><img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="116" height="37" alt="CAPTCHA" id="alx_review_CAPTCHA" /></div>
                                	<a href="javascript:void(0);" onclick="BX('alx_review_CAPTCHA').src=BX('alx_review_CAPTCHA').src+'&rnd='+Math.random()"><?=GetMessage("IMPROVED_TP_RELOAD")?></a>
    				</div>
					<div class="alx_clear_block">&nbsp;</div>
    		</div>
        <?endif;?>
		<div class="alx_clear_block">&nbsp;</div>
        <?if(!aReviewSubs::IsSubs($arParams["ELEMENT_ID"],$USER->GetEmail())):?>
		<div class="alx_reviews_subscribe">
			<?/*<div class="alx_reviews_checkbox_block">
            <div class="alx_reviews_checkbox_block_cont">
            <div class="alx_reviews_checkbox">*/?>
            <input type="checkbox" name="SUBSCRIBE" value="Y" <?if($arResult["arMessage"]['SUBSCRIBE']=='Y'):?>checked="checked"<?endif;?>/>
            <?/*</div>*/?><?=GetMessage("IMPROVED_TP_SUBSCRIBE")?><?/*</div>*/?></div>
		</div>
        <?endif;?>
		<div class="alx_reviews_form_requred_block">
				<span class="requred_txt">*</span> <?=GetMessage("IMPROVED_TP_R_FIELDS")?>
			</div>
	</div>
	<div class="alx_reviews_block_border"></div>
	<div class="alx_reviews_form_submit_block">
			<div class="alx_reviews_form_item_submit"><input type="submit" name="review_add_btn" value="<?=GetMessage("IMPROVED_TP_SEND")?>" /></div>		
	</div>    
	</div>
</form>
</div>
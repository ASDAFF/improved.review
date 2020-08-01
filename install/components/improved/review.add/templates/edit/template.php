<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
if(strlen($arResult["ERROR_MESSAGE"])>0)
{
	ShowError($arResult["ERROR_MESSAGE"]);
?>
<script>
            delete JCLightHTMLEditor.items['MESSAGE_PLUS_e'];
            delete JCLightHTMLEditor.items['MESSAGE_MINUS_e'];
            delete JCLightHTMLEditor.items['MESSAGE_e'];
            delete JCLightHTMLEditor.items['REPLY'];
</script>
<?    
}

if(isset($_SESSION["REVIEW_ADD_OK"]) && $_SESSION["REVIEW_ADD_OK"])
{
	ShowNote($arParams["MESSAGE_OK"]);
	unset($_SESSION["REVIEW_ADD_OK"]);
}
?>
<br />
<div id="review_add_form_edit">
<form name="review_add" id="review_add" action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">
	<?=bitrix_sessid_post()?>
    <input type="hidden" value="<?=$arParams["ELEMENT_ID"];?>" name="ELEMENT_ID">
    <input type="hidden" value="<?=$arResult["arMessage"]["RATING"];?>" name="RATING" id="RATING_e">
    <input type="hidden" value="EDIT" name="ACTION">
    <input type="hidden" value="<?=$_POST["RID"]?>" name="RID">
	<div class="alx_reviews_block_border">&nbsp;</div>
<div class="alx_reviews_block_edit">
	<div class="alx_reviews_form">       
        <?if($arParams["ALLOW_TITLE"]):?>
    		<div class="alx_reviews_form_item_pole alx_reviews_form_item_pole_last">
    			<div class="alx_reviews_form_pole_name"><span class="requred_txt">*</span> <?=GetMessage("IMPROVED_TP_TITLE")?>:</div>
    			<div class="alx_reviews_form_inputtext_bg"><div class="alx_reviews_form_inputtext_bg_arr"></div><input type="text" name="TITLE" value="<?=$arResult["arMessage"]["TITLE"];?>" /></div>
    		</div>
            <div class="alx_clear_block">&nbsp;</div>
		<?endif;?>
        <?if(!$arParams['COMMENTS_MODE']):?>
        <div class="alx_reviews_form_item_pole_textarea">
			<div class="alx_reviews_form_pole_name"><span class="requred_txt">*</span> <?=GetMessage("IMPROVED_TP_PLUS")?>:</div>
			<div class="alx_reviews_form_textarea_bg">
                <?=\IMPROVED\Review\Tools::showLHE("MESSAGE_PLUS_e",$arResult["arMessage"]["MESSAGE_PLUS"],"MESSAGE_PLUS",100,"oLHErwe");?>
			</div>
		</div>
		<div class="alx_reviews_form_item_pole_textarea">
			<div class="alx_reviews_form_pole_name"><span class="requred_txt">*</span> <?=GetMessage("IMPROVED_TP_MINUS")?>:</div>
			<div class="alx_reviews_form_textarea_bg">
                <?=\IMPROVED\Review\Tools::showLHE("MESSAGE_MINUS_e",$arResult["arMessage"]["MESSAGE_MINUS"],"MESSAGE_MINUS",100,"oLHErwe");?>
			</div>
		</div>
        <?endif;?>
		<div class="alx_reviews_form_item_pole_textarea">
			<div class="alx_reviews_form_pole_name"><span class="requred_txt">*</span> <?=GetMessage("IMPROVED_TP_COMMENT")?>:</div>
			<div class="alx_reviews_form_textarea_bg">
                <?=\IMPROVED\Review\Tools::showLHE("MESSAGE_e",$arResult["arMessage"]["MESSAGE"],"MESSAGE",200,"oLHErwe");?>
			</div>
			<div class="alx_reviews_form_item_pole_textarea_dop_txt">
				<?if($arParams["MIN_LENGTH"]>0):?><?=GetMessage("IMPROVED_TP_MIN_L")?> <span class="alx_reviews_red_txt"><?=$arParams["MIN_LENGTH"]?></span> <?=GetMessage("IMPROVED_TP_S")?>. <br /><?endif;?>
				<?if($arParams["MAX_LENGTH"]>0):?><?=GetMessage("IMPROVED_TP_MAX_L")?> <?=$arParams["MAX_LENGTH"]?> <?=GetMessage("IMPROVED_TP_S")?>. <?=GetMessage("IMPROVED_TP_R")?>: <span id="review_max_cnt" class="alx_reviews_red_txt"><?=$arParams["MAX_LENGTH"]?></span>.<?endif;?>
			</div>

            <?if($arParams["ALLOW_UPLOAD_FILE"]):?>
                    <?
                    $APPLICATION->IncludeComponent("bitrix:main.file.input","",Array(
                        "ALLOW_UPLOAD"=>"F",
                        "ALLOW_UPLOAD_EXT" => $arParams["UPLOAD_FILE_TYPE"],
                        //"MAX_FILE_SIZE" => $arParams["UPLOAD_FILE_SIZE"],
                        "INPUT_NAME"=>"FILES_edit",
                        'INPUT_VALUE'=>$arResult['FILES_EDIT_VALUE'],
                        "INPUT_NAME_UNSAVED"=>"FILES_TMP_edit",
                        "MULTIPLE"=>"Y",
                        "MODULE_ID"=>"improved.review",
                        'CONTROL_ID' => 'reviewFileEdit'
                        )
                    );
                    ?>                
            <?endif;?>
            
			<div class="alx_clear_block">&nbsp;</div>
		</div>
        
		<div class="alx_reviews_form_item_pole_textarea">
			<div class="alx_reviews_form_pole_name"><span class="requred_txt">*</span> <?=GetMessage("IMPROVED_TP_REPLY")?>:</div>
			<div class="alx_reviews_form_textarea_bg">
                <?=\IMPROVED\Review\Tools::showLHE("REPLY",$arResult["arMessage"]["REPLY"],"REPLY",100,"oLHErwe");?>
			</div>
		</div>
                
        <div class="alx_reviews_form_vote" id="alx_reviews_form_vote">
			<div class="alx_reviews_form_vote_group_name"><span class="requred_txt">*</span> <?=GetMessage("IMPROVED_TP_VOTE")?>:</div>
              	<div class="alx_reviews_form_pole_name"><?=GetMessage("IMPROVED_TP_RATING")?>:</div>
       		     	<div class="alx_reviews_form_vote_items" onmouseout="jsReviewVoteEdit.Restore();">
                        <?
                            for($i=1; $i<=5; $i++):
                                $class = "alx_reviews_form_vote_item";
                                
                                if($arResult["arMessage"]["RATING"]>0 && $i<=$arResult["arMessage"]["RATING"]):
                                    $class = "alx_reviews_form_vote_item alx_reviews_form_vote_item_sel";
                                    ?>
                                    <script>
                                    jsReviewVoteEdit.Set(<?=$i?>,'RATING_e',0);
                                    </script>
                                <?endif;?>
        				    <div id="improved_item_vote_edit_0_<?=$i?>" onmouseover="jsReviewVoteEdit.Curr(<?=$i?>,0)" onmouseout="jsReviewVoteEdit.Out(0)" onclick="jsReviewVoteEdit.Set(<?=$i?>,'RATING_e',0)" class="<?=$class?>"></div>
                        <?endfor;?>
                    </div>
			<div class="alx_clear_block">&nbsp;</div>
            <?if(count($arParams["USER_FIELDS_RATING"]) > 0):?>
                <?foreach($arParams["USER_FIELDS_RATING"] as $FIELD_NAME=>$arUserField):?>
                <div class="alx_reviews_form_pole_name"><?if ($arUserField["MANDATORY"]=="Y"):?><span class="requred_txt">*</span><?endif;?> <?=$arUserField["EDIT_FORM_LABEL"]?>:</div>
                <?$APPLICATION->IncludeComponent(
                       "bitrix:system.field.edit",
                       $arUserField["USER_TYPE"]["USER_TYPE_ID"],
                       array("arUserField" => $arUserField,"EDIT"=>"Y"), null, array("HIDE_ICONS"=>"Y"));?>            
					   <div class="alx_clear_block">&nbsp;</div>
                <?endforeach;?>
				
            <?endif;?> 
			<div class="alx_clear_block">&nbsp;</div>			
		</div>
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
                <?if($arUserField["USER_TYPE"]["USER_TYPE_ID"]=="file"):?>
                    <?
                    $APPLICATION->IncludeComponent("bitrix:main.file.input","",Array(
                        "ALLOW_UPLOAD"=>"F",
                        //"ALLOW_UPLOAD_EXT" => $arParams["UPLOAD_FILE_TYPE"],
                        "INPUT_NAME"=>$arUserField['FIELD_NAME'],
                        'INPUT_VALUE'=>$arUserField['VALUE'],
                        "INPUT_NAME_UNSAVED"=>"FILES_TMP_edit",
                        "MULTIPLE"=>$arUserField['MULTIPLE'],
                        "MODULE_ID"=>"improved.review",
                        )
                    );
                    ?>                
                <?else:?>
					<?$APPLICATION->IncludeComponent(
                       "bitrix:system.field.edit",
                       $arUserField["USER_TYPE"]["USER_TYPE_ID"],
                       array("arUserField" => $arUserField), null, array("HIDE_ICONS"=>"Y"));
                    ?>
                <?endif;?>

                </div>
				 <div class="alx_clear_block"></div>
            </div>
            <?endforeach;?>
			<div class="alx_reviews_form_poles_group_border_bottom">&nbsp;</div>
		</div>
        <?endif;?>
		<div class="alx_clear_block">&nbsp;</div>
		<div class="alx_reviews_form_requred_block">
				<span class="requred_txt">*</span> <?=GetMessage("IMPROVED_TP_R_FIELDS")?>
			</div>
	</div>
	</div>
	<div class="alx_reviews_block_border"></div>
</form>
</div>
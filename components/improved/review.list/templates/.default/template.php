<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?/*p($arResult);*/?>
<div class="alx_reviews_list" id="alx_reviews_list">
	<div class="alx_reviews_title">
		<div class="alx_reviews_title_txt"><?echo strlen($arParams["LIST_TITLE"])>0 ? $arParams["LIST_TITLE"] : GetMessage("IMPROVED_REVIEW_T_REVIEW_LIST_REVIEW")?>:</div>
		<div class="alx_reviews_list_count"><div class="alx_reviews_count_bg"></div><span id="review_cnt"><?=$arResult["ALL_CNT"]?></span></div>
	</div>
	<?foreach($arResult["ITEMS"] as $arReview):
    if(!aReview::AllowVote($arReview["ID"]) || ($USER->GetID()>0 && $USER->GetID()==$arReview["USER_ID"]))
        $arReview['ALLOW_VOTE'] = false;
    else
        $arReview['ALLOW_VOTE'] = true;
    ?>
	<?if($arReview["APPROVED"]=="N" && (($USER->GetID()==$arReview["USER_ID"] && $USER->GetID()>0) 
		|| ($APPLICATION->get_cookie("REVIEW_AUTHOR_EMAIL", "IMPROVED_REVIEW") == $arReview["AUTHOR_EMAIL"]
		&& $APPLICATION->get_cookie("REVIEW_AUTHOR_NAME", "IMPROVED_REVIEW") == $arReview["AUTHOR_NAME"])
		))
			ShowNote(GetMessage("IMPROVED_REVIEW_T_REVIEW_LIST_MODER"));
		elseif($arReview["APPROVED"]=="N" && !$arParams["ALLOW_EDIT"])
			continue;?>
	<a name="review<?=$arReview["ID"];?>"></a>
	<div class="alx_reviews_item<?if($arReview["APPROVED"]!="Y"):?> hide<?endif;?>" id="review-list-review-<?=$arReview["ID"]?>">
		<div class="alx_reviews_item_line"></div>
        <?
        if($arReview["VOTE"]<0)
            $class = "no";
        else
            $class = "yes";
        ?>
		<div class="alx_reviews_item_vote_result alx_reviews_item_vote_result_<?=$class?>"><div class="alx_reviews_item_vote_result_arr"></div><span id="review_vote_r<?=$arReview["ID"]?>"><?=$arReview["VOTE"];?></span></div>
		<div class="alx_reviews_item_author_info">
			<div class="alx_reviews_ava"><?if($arParams["SHOW_AVATAR_TYPE"]!=false && strlen($arReview["USER_AVATAR"])>0):?><img src="<?=$arReview["USER_AVATAR"]?>" alt="<?=$arReview["AUTHOR_NAME"];?>" border="0" /><?else:?><img src="<?=$templateFolder;?>/images/no_photo.png" alt="" border="0" /><?endif;?></div>
			<div class="alx_reviews_user_name"><?if(strlen($arReview["SHOW_USER_PATH"])>0):?><a href="<?=$arReview["SHOW_USER_PATH"]?>" target="_blank" rel="nofollow"><?endif;?><?=$arReview["AUTHOR_NAME"];?><?if(strlen($arReview["SHOW_USER_PATH"])>0):?></a><?endif;?></div>
			<div class="alx_reviews_time"><?=$arReview["POST_DATE_FORMAT"];?></div>
			<div class="alx_reviews_dop_props">
        <?if(count($arReview["USER_FIELDS"]) > 0):?>
            <?foreach($arReview["USER_FIELDS"] as $FIELD_NAME=>$arUserField):
            if($arUserField["USER_TYPE_ID"] == "video" || $arUserField["USER_TYPE_ID"] == "file" || $arUserField["USER_TYPE_ID"] == "iblock_section" || $arUserField["USER_TYPE_ID"] == "iblock_element")
                continue;
                
            if ((is_array($arUserField["VALUE"]) && count($arUserField["VALUE"]) > 0) || (!is_array($arUserField["VALUE"]) && StrLen($arUserField["VALUE"]) > 0)):
            ?>
                <div class="alx_item_pole_rev">
                    <b><?=$arUserField["EDIT_FORM_LABEL"]?>:</b>
                        <?$APPLICATION->IncludeComponent(
                                "bitrix:system.field.view",
                                $arUserField["USER_TYPE"]["USER_TYPE_ID"],
                                array("arUserField" => $arUserField), null, array("HIDE_ICONS"=>"Y")
                            );
                        ?>
    		</div>
            <?endif;?>
            <?endforeach;?>
        <?endif;?>            
            </div>
			<div class="alx_clear_block">&nbsp;</div>
		</div>
		<div class="alx_reviews_vote_item">
			<div class="alx_reviews_form_vote_items">
				<?for($ii=1;$ii<=5;$ii++):
					$class="alx_reviews_form_vote_item";
					if($ii<=$arReview["RATING"])
						$class="alx_reviews_form_vote_item alx_reviews_form_vote_item_sel";
					?>
					<div class="<?=$class?>"></div>
				<?endfor;?> 
			</div>  
            <?if(!empty($arReview["USER_FIELDS_RATING"])):?>
			<div class="alx_reviews_item_vote_show"><a href="javascript:void(0)" onclick="jsReview.ShowVotes(<?=$arReview["ID"]?>)"><?=GetMessage('IMPROVED_REVIEW_T_REVIEW_LIST_SHOW_ALL_VOTES')?></a></div>
			<div class="alx_reviews_item_vote_list" id="review_all_votest_<?=$arReview["ID"]?>">
				<?foreach($arReview["USER_FIELDS_RATING"] as $k=>$arR):?>
					<div class="alx_reviews_item_vote">
						<div class="alx_review_rating_title"><?=$arR["EDIT_FORM_LABEL"]?></div>
						<div class="alx_reviews_form_vote_items">
						<?for($ii=1;$ii<=5;$ii++):
							$class="alx_reviews_form_vote_item";
							if($ii<=(int)$arR["VALUE"])
								$class="alx_reviews_form_vote_item alx_reviews_form_vote_item_sel";
							?>
							<div class="<?=$class?>"></div>
						<?endfor;?> 
						</div>                            
					</div>
				<?endforeach;?>  
				<div class="alx_clear_block">&nbsp;</div>
			</div>
            <?endif;?>
			<div class="alx_clear_block">&nbsp;</div>
		</div>
		<div class="alx_reviews_item_title"><?=$arReview["TITLE"];?></div>
		<div class="alx_reviews_item_sec_list" id="review_item_e_<?=$arReview["ID"]?>">
            <?if(!$arParams['COMMENTS_MODE']):?>
			<div class="alx_reviews_item_sec">
				<div class="alx_reviews_title_caps"><?=GetMessage("IMPROVED_REVIEW_T_REVIEW_LIST_MESSAGE_PLUS")?>:</div>
				<div class="alx_review_mess"><?=$arReview["MESSAGE_PLUS_HTML"];?></div>
            </div>
			<div class="alx_reviews_item_sec">
				<div class="alx_reviews_title_caps"><?=GetMessage("IMPROVED_REVIEW_T_REVIEW_LIST_MESSAGE_MINUS")?>:</div>
				<div class="alx_review_mess"><?=$arReview["MESSAGE_MINUS_HTML"];?></div>
			</div>
            <?endif;?>
			<div class="alx_reviews_item_sec">
				<div class="alx_reviews_title_caps"><?=GetMessage("IMPROVED_REVIEW_T_REVIEW_LIST_MESSAGE")?>:</div>
				<div class="alx_review_mess"><?=$arReview["MESSAGE_HTML"];?></div>
			</div>
            <?if(count($arReview["USER_FIELDS"]) > 0):?>
            <?foreach($arReview["USER_FIELDS"] as $FIELD_NAME=>$arUserField):
            if($arUserField["USER_TYPE_ID"] != "video" && $arUserField["USER_TYPE_ID"] != "file" && $arUserField["USER_TYPE_ID"] != "iblock_section" && $arUserField["USER_TYPE_ID"] != "iblock_element")
                continue;
                
            if ((is_array($arUserField["VALUE"]) && count($arUserField["VALUE"]) > 0) || (!is_array($arUserField["VALUE"]) && StrLen($arUserField["VALUE"]) > 0)):
            ?>
            <div class="alx_reviews_item_sec">
                    <div class="alx_reviews_title_caps"><?=$arUserField["EDIT_FORM_LABEL"]?>:</div>
                    <div class="alx_review_mess">
                        <?$APPLICATION->IncludeComponent(
                                "bitrix:system.field.view",
                                $arUserField["USER_TYPE"]["USER_TYPE_ID"],
                                array("arUserField" => $arUserField), null, array("HIDE_ICONS"=>"Y")
                            );
                        ?>
                    </div>
    		</div>
            <?endif;?>
            <?endforeach;?>
        <?endif;?>            
		</div>
        <?if(count($arReview["FILES"])>0):?>
			<div class="alx_item_pole_rev">
                <?foreach($arReview["FILES"] as $arFile):?>
                    <?if(!stristr($arFile["CONTENT_TYPE"], "image/")):?>
                    <a href="<?=$arFile["SRC"]?>" target="_blank" rel="nofollow"><?=$arFile["ORIGINAL_NAME"]?></a>
                    <?endif;?>
                <?endforeach;?>
			</div>
            <div id="review_attach" valign="center">
			<?foreach($arReview["FILES"] as $arFile):?>
					<?if(stristr($arFile["CONTENT_TYPE"], "image/")):?>
                    <?=CFile::ShowImage($arFile["SRC"], 250, 250, "border=0", "", true);?>
					<?endif;?>
			<?endforeach;?>
            </div>
            <br />
        <?endif?>
        <?if(strlen($arReview["REPLY_HTML"])>0):?>
           <div class="improved_reviw_answer">
    		<div class="improved_reviw_answer_top_border">&nbsp;</div>
    		<?=$arReview["REPLY_HTML"]?>
           </div><br />
        <?endif;?>
		<?if(($arParams["ALLOW_COMPLAINT"] && (($USER->IsAuthorized() && $arReview["USER_ID"] != $USER->GetID()) || !$arParams["ONLY_AUTH_COMPLAINT"]))):?>
        <a href="javascript:void(0)" onclick="jsReview.Complaint(<?=$arReview["ID"]?>);" class="alx_reviews_violation"><?=GetMessage("IMPROVED_REVIEW_T_REVIEW_LIST_REPORT")?></a>
        <div style="clear: both;"></div>
        <?endif;?>        
        <div class="alx_reviews_item_link"><noindex><a rel="nofollow" href="#review<?=$arReview["ID"];?>"><?=GetMessage('IMPROVED_REVIEW_T_REVIEW_LINK')?></a></noindex></div>
        <?if($arParams["ALLOW_VOTE"]):?>
		<div class="alx_reviews_item_vote_do">
			<div class="alx_rev_vot_titl"><?=GetMessage("IMPROVED_REVIEW_T_REVIEW_LIST_VOTE")?></div>
			<?if($arReview['ALLOW_VOTE']):?>
				<a href="javascript:void(0)" onclick="jsReview.vote(<?=$arReview["ID"]?>,true)" class="alx_reviews_vote_yes">
				<?else:?>
				<span class="alx_reviews_vote_yes">
			<?endif;?>
                <span class="alx_reviews_vote_txt"><?=GetMessage("MAIN_YES")?></span> <span class="alx_reviews_vote_count"><span class="alx_reviews_vote_count_arr"></span><span id="review_vote_p_r<?=$arReview["ID"]?>"><?=$arReview["VOTE_PLUS"];?></span></span>
            <?if($arReview['ALLOW_VOTE']):?></a><?else:?></span><?endif;?>
			
            <?if($arReview['ALLOW_VOTE']):?>
				<a href="javascript:void(0)" onclick="jsReview.vote(<?=$arReview["ID"]?>,false)" class="alx_reviews_vote_no">
			<?else:?>
				<span class="alx_reviews_vote_no">
			<?endif;?>
                <span class="alx_reviews_vote_txt"><?=GetMessage("MAIN_NO")?></span> <span class="alx_reviews_vote_count" id="review_vote_m_<?=$arReview["ID"]?>"><span class="alx_reviews_vote_count_arr"></span><span id="review_vote_m_r<?=$arReview["ID"]?>"><?=$arReview["VOTE_MINUS"];?></span></span>
            <?if($arReview['ALLOW_VOTE']):?></a><?else:?></span><?endif;?>
		</div>
        <?endif;?>
		<div class="alx_clear_block">&nbsp;</div>
		<?if($arParams["ALLOW_EDIT"]):?>
		<div class="alx_reviews_admin_prop">
			<a href="javascript:void(0)" onclick="jsReviewAdmin.Delete(<?=$arReview["ID"]?>);" class="alx_reviews_admin_prop_del"><?=GetMessage("MAIN_DELETE")?></a>
			<a href="javascript:void(0)" onclick="jsReview.Edit(<?=$arReview["ID"]?>);" class="alx_reviews_admin_prop_edit"><?=GetMessage("MAIN_EDIT")?></a>
            <?if($arReview["APPROVED"]=="Y"):?>
                <a href="javascript:void(0)" id="review-hide-app-link-<?=$arReview["ID"]?>" onclick="jsReviewAdmin.Hide(<?=$arReview["ID"]?>);" class="alx_reviews_admin_prop_hide"><?=GetMessage("IMPROVED_REVIEW_T_REVIEW_LIST_MODER_HIDE")?></a>
            <?else:?>
                <a href="javascript:void(0)" id="review-hide-app-link-<?=$arReview["ID"]?>" onclick="jsReviewAdmin.App(<?=$arReview["ID"]?>);" class="alx_reviews_admin_prop_hide"><?=GetMessage("IMPROVED_REVIEW_T_REVIEW_LIST_MODER_APP")?></a>
            <?endif;?>
		</div>
		<?endif?>
	</div>
  <?endforeach;?>
  <?if($arResult["ALL_CNT"]>$arParams["REVIEWS_ON_PAGE"] && !$arParams["SHOW_ALL"]):?>
  <a href="javascript:void(0)" onclick="BX.ajax.get(CURRENT_URL,{'IMPROVED_AJAX_CALL' : 'Y','showAll' : 'Y'},function (res) {BX.showWait(BX('#alx_reviews_list'));BX('alx_reviews_list').innerHTML = res;BX.closeWait(BX('#alx_reviews_list'));})" class="alx_reviews_show_more">
		<?=GetMessage("IMPROVED_REVIEW_T_REVIEW_LIST_SHOW_ALL")?> <span class="alx_reviews_count_all"><?=$arResult["ALL_CNT"]-$arParams["REVIEWS_ON_PAGE"]?></span>
  </a>
  <?endif;?>
</div>
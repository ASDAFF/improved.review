<?
/**
 * Copyright (c) 1/8/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<div class="alx_reviews_list" id="alx_reviews_list">
	<?foreach($arResult['ITEMS'] as $arReview):
    ?>
	<div class="alx_reviews_item" id="review-list-review-<?=$arReview["ID"]?>">
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
			<div class="alx_reviews_item_vote_show"><a href="javascript:void(0)" onclick="ReviewShowVotes(<?=$arReview["ID"]?>)"><?=GetMessage('IMPROVED_REVIEW_T_REVIEW_LIST_SHOW_ALL_VOTES')?></a></div>
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
		</div>
        <?if(strlen($arReview["REPLY_HTML"])>0):?>
           <div class="improved_reviw_answer">
    		<div class="improved_reviw_answer_top_border">&nbsp;</div>
    		<?=$arReview["REPLY_HTML"]?>
           </div><br />
        <?endif;?>
      
        <div class="alx_reviews_item_link"><noindex><a rel="nofollow" href="<?=$arReview['ELEMENT']['DETAIL_PAGE_URL']?>#review<?=$arReview["ID"];?>"><?=GetMessage('IMPROVED_REVIEW_T_REVIEW_LINK')?></a></noindex></div>
		<div class="alx_clear_block">&nbsp;</div>
	</div>
  <?endforeach;?>
</div>
<?=$arResult["NAV_STRING"];?>
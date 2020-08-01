<?
/**
 * Copyright (c) 1/8/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?if($arResult):$arParams["SHOW_MESS"] = true;?>
<script>
	var review_last_rating = 0;
    var stop_draw = false;
    function DrawRatingStar(num,out,star,substar)
    {
        if((out && review_last_rating>0) || stop_draw)
            return;
            
        for(i=1;i<6;i++)
        {
            if(i<=num)
            {
                BX("review_rating_"+i).className='alx_vote_item_a';
            }
            else
            {
                BX("review_rating_"+i).className='alx_vote_item_na';
            }
            
            if(out && i==star+1)
                BX("review_rating_"+i).className='alx_vote_item_na alx_vote_item_a'+substar;
        }
        
        <?if($arParams["SHOW_MESS"]):?>
        review_rating_txt = BX('review_rating_txt');
            
        switch (num) {
			case 1:
					review_rating_txt.innerHTML='<?=GetMessage("IMPROVED_REVIEW_RATING_MESS_1");?>';
				break
            case 2:
					review_rating_txt.innerHTML='<?=GetMessage("IMPROVED_REVIEW_RATING_MESS_2");?>';
				break
            case 3:
					review_rating_txt.innerHTML='<?=GetMessage("IMPROVED_REVIEW_RATING_MESS_3");?>';
				break
            case 4:
					review_rating_txt.innerHTML='<?=GetMessage("IMPROVED_REVIEW_RATING_MESS_4");?>';
				break
            case 5:
					review_rating_txt.innerHTML='<?=GetMessage("IMPROVED_REVIEW_RATING_MESS_5");?>';
				break
		}        
        <?endif;?>
    }

	function ReviewSetRating(num)
	{
        review_last_rating = num;
        oData = {"RATING" : num,"ACTION" : "SET_RATING","sessid" : '<?=bitrix_sessid();?>',"ELEMENT_ID": <?=$arParams["ELEMENT_ID"];?>};
        BX.ajax.post(window.location.href,oData,function (res) 
        {
            stop_draw = true;
            eval(res);
        });       
	}
</script>
<?$arStar = explode(".",$arResult);?><? $Star = $arStar[0]; $subStar = isset($arStar[1]) ? $arStar[1] : 0;?>
<div class="alx_reviews_elem_vote" onmouseout="DrawRatingStar(<?=$Star;?>,true,<?=$Star?>,<?=$subStar?>);">
<?if($arParams["SHOW_TITLE"]):?>
	<div class="alx_vote_value">
		<b><? if(strlen($arParams["TITLE_TEXT"])>0): echo $arParams["TITLE_TEXT"]; else: echo GetMessage("IMPROVED_REVIEW_RATING_ITEM"); endif;?></b>
	</div>
<?endif;?>    
	<div class="alx_vote_items">
		<?for($i=1;$i<=5;$i++){?>
		<div id="review_rating_<?=$i;?>" 
            <?if($arParams["ALLOW_VOTE"]):?>
                onmouseover="DrawRatingStar(<?=$i;?>,false)" 
                onmouseout="DrawRatingStar(<?=$i;?>,false)" 
                onclick="ReviewSetRating(<?=$i;?>)"<?endif;?> 
                class="<?if($i<=$Star):?>alx_vote_item_a<?else:?>alx_vote_item_na <?if($i==$Star+1):?>alx_vote_item_a<?=$subStar?><?endif?><?endif?>">
                <img src="<?=$templateFolder?>/images/spacer.gif" alt="<?=$arResult?>" title="<?=$arResult?>" border="0" />
        </div>
		<?}?>
	</div>
    <div class="review_rating_txt" id="review_rating_txt"><?if($arParams["ALLOW_VOTE"]):?><?=GetMessage("IMPROVED_REVIEW_ADD_TP_SET_RATING");?><?else:?><?=GetMessage("IMPROVED_REVIEW_RATING_MESS_".$Star);?><?endif;?></div>
	<div class="clear_block">&nbsp;</div>
</div>
<?endif;?>
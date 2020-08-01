<?
/*
echo "<pre>";
print_r($arParams);
echo "</pre>";

echo "<pre>";
print_r($arResult);
echo "</pre>";
*/
?>
<input type="hidden" name="<?if($arParams["EDIT"]=="Y"):?>EDIT[<?endif;?><?=$arParams["arUserField"]["FIELD_NAME"]?><?if($arParams["EDIT"]=="Y"):?>]<?endif;?>" id="<?if($arParams["EDIT"]=="Y"):?>edit_<?endif;?><?=$arParams["arUserField"]["FIELD_NAME"]?>" value="" />
		<div class="alx_reviews_form_vote_uf">
       		     	<div class="alx_reviews_form_vote_items" onmouseout="jsReviewVote<?if($arParams["EDIT"]=="Y"):?>Edit<?endif;?>.Restore();">
                        <?
                            for($i=1; $i<=5; $i++):
                                $class = "alx_reviews_form_vote_item";
                                
                                if($arResult["VALUE"]["0"]>0 && $i<=$arResult["VALUE"]["0"])
                                {
                                    $class = "alx_reviews_form_vote_item alx_reviews_form_vote_item_sel";
                                    if($arParams["EDIT"]=="Y"):
                                    ?>
                                    <script>
                                    jsReviewVoteEdit.Set(<?=$i?>,'<?=$arParams["arUserField"]["FIELD_NAME"]?>',<?=$arParams["arUserField"]["ID"]?>);
                                    </script>
                                    <?endif;
                                }
                        ?>
        				    <div id="improved_item_vote<?if($arParams["EDIT"]=="Y"):?>_edit<?endif;?>_<?=$arParams["arUserField"]["ID"]?>_<?=$i?>" onmouseover="jsReviewVote<?if($arParams["EDIT"]=="Y"):?>Edit<?endif;?>.Curr(<?=$i?>,<?=$arParams["arUserField"]["ID"]?>)" onmouseout="jsReviewVote<?if($arParams["EDIT"]=="Y"):?>Edit<?endif;?>.Out(<?=$arParams["arUserField"]["ID"]?>)" onclick="jsReviewVote<?if($arParams["EDIT"]=="Y"):?>Edit<?endif;?>.Set(<?=$i?>,'<?if($arParams["EDIT"]=="Y"):?>edit_<?endif;?><?=$arParams["arUserField"]["FIELD_NAME"]?>',<?=$arParams["arUserField"]["ID"]?>)" class="<?=$class?>"></div>
                        <?endfor;?>
                    </div>
			<div class="alx_clear_block">&nbsp;</div>
		</div>
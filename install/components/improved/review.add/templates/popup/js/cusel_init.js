$(document).ready(function(){
       params = {
                changedEl: ".alx_reviews_form .alx_reviews_form_item_pole_uf .alx_reviews_form_field .bx-user-field-enum",
                visRows: 5,
                scrollArrows: true
        }
	cuSel(params);
	$('.alx_reviews_pole_select_uf').jScrollPane({
		showArrows: true,
		verticalDragMaxHeight: 33
	});
	$('.alx_reviews_checkbox_block .alx_reviews_checkbox_block_cont').click(function(){
		if($(this).parent().hasClass('alx_reviews_checkbox_block_check'))
		{
			$(this).parent().removeClass('alx_reviews_checkbox_block_check');
			$(this).find('input').removeAttr('checked');
		}
		else
		{
			$(this).parent().addClass('alx_reviews_checkbox_block_check');
			$(this).find('input').attr('checked', 'checked');
		}
		return false;
	});
	$('.alx_reviews_list .alx_reviews_item .alx_reviews_item_vote_show a').click(function(){
		if($(this).hasClass("alx_reviews_item_vote_show_open"))
		{
			$(this).removeClass('alx_reviews_item_vote_show_open');
			$(this).parent().siblings('.alx_reviews_item_vote_list').hide();
			$(this).html("Показать все оценки");
		}
		else
		{
			$(this).addClass('alx_reviews_item_vote_show_open');
			$(this).parent().siblings('.alx_reviews_item_vote_list').show();
			$(this).html("Свернуть все оценки");
		}
		return false;
	})
});
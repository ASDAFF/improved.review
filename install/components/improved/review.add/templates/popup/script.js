/*
 * Copyright (c) 1/8/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

var active_vote_prev = {};
var aReviewVote = function()
{
    this.Curr = function(num,id)
    {
        for(i=1;i<6;i++)
        {
            div = BX('improved_item_vote_'+id+'_'+i);
            if(num>=i)
                div.className='alx_reviews_form_vote_item alx_reviews_form_vote_item_sel';
            else
                div.className='alx_reviews_form_vote_item';
        }
    }
    this.Out = function(id)
    {
        for(i=1;i<6;i++)
        {
            div = BX('improved_item_vote_'+id+'_'+i);
            if(active_vote_prev[id]>=i)
                div.className='alx_reviews_form_vote_item alx_reviews_form_vote_item_sel';
            else
                div.className='alx_reviews_form_vote_item';
        }
    }
    this.Set = function(num,field,id)
    {
        active_vote_prev[id] = num;
        BX(field).value=num;
        this.Curr(num,id);
    }
    this.Restore = function()
    {
        for(var key in active_vote_prev)
        {
            num = active_vote_prev[key];
            this.Curr(num,key);
        }
    }
}
var jsReviewVote = new aReviewVote();

function onInsertRwText(e)
{
	if(review_ml>0)
	{
	   //alert(oLHErwc.GetEditorContent().length);
        BX("review_max_cnt").innerHTML = (review_ml - oLHErwc.GetEditorContent().length);
	}
    if(review_show_cnt)
    {
        BX("review_cnt_c").innerHTML = oLHErwc.GetEditorContent().length;
    }		
}
function onLightEditorShowRw(content)
{
    if (!window.oLHErwc)
            return BX.addCustomEvent(window, 'LHE_OnInit', function(){setTimeout(function(){onLightEditorShowRw(content);},   500);});

    //oLHErw.SetContent(content || '');
    //oLHErw.CreateFrame();
    //oLHErw.SetEditorContent(oLHErw.content);
    //oLHErw.SetFocus();
    
    BX.bind(oLHErwc.pEditorDocument, 'keydown', BX.proxy(onInsertRwText, oLHErwc));
    BX.bind(oLHErwc.pTextarea, 'keydown', BX.proxy(onInsertRwText, oLHErwc));
}
//onLightEditorShowRw();

function ShowReviewForm()
{
    window.oReviewDialog = new BX.CDialog({
    	content_url: CURRENT_URL,
        content_post: "SHOW_REVIEW_FORM=Y",
    	width: 1024,
    	height: 450,
    	min_height: 300,
    	min_width: 800,
    	resizable: true,
    });
    //BX.addClass(BX("file_input_reviewFile","adm-designed-file"));
    //BX.removeClass(BX("file_input_reviewFile"));
    window.oReviewDialog.Show();
    //BX.removeClass(BX("file_input_reviewFile"));
    //window.oReviewDialog.__expand();
    //BX.addCustomEvent(window, 'LHE_OnInit', function(obj){obj.SetEditorContent(obj.content);obj.SetContent(obj.content);});
    onLightEditorShowRw();
}
/*
 * Copyright (c) 1/8/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

var ne = 1;
var aReview = function()
{
	_this = this;
	
    this.vote = function(id,plus)
    {
        BX.ajax.post(CURRENT_URL,{"IMPROVED_AJAX_CALL" : "Y","VOTE" : "Y","RID" : id, "PLUS" : plus ? "Y":"N"},function (res) {
            eval(res);
            if(voteRes.TYPE)
            {
                BX('review_vote_p_r'+id).innerHTML = voteRes.PLUS;
                BX('review_vote_m_r'+id).innerHTML = voteRes.MINUS;
                BX('review_vote_r'+id).innerHTML = voteRes.VOTE;
            }
        });
        return false;
    }
    this.ShowVotes = function(id)
    {
        div = BX('review_all_votest_'+id);
        if(div.style.display=='block')
            div.style.display='none';
        else
            div.style.display='block';
    }
    this.PublicEdit = function(id)
    {
        div = BX('review_item_e_'+id);
    }
    
	this.Edit = function(id)
	{
        window.oReviewEditDialog = wred = new BX.CDialog({
        	content_url: CURRENT_URL,
            content_post: "RID="+escape(id)+"&ACTION=EDIT",
        	width: 800,
        	height: 320,
        	min_height: 300,
        	min_width: 800,
        	resizable: true
        });
	    window.oReviewEditDialog.Show();
        //onWindowClose
        BX.addCustomEvent(BX.WindowManager.Get(),'onWindowRegister',function () {
            delete JCLightHTMLEditor.items['MESSAGE_PLUS_e'];
            delete JCLightHTMLEditor.items['MESSAGE_MINUS_e'];
            delete JCLightHTMLEditor.items['MESSAGE_e'];
            delete JCLightHTMLEditor.items['REPLY'];
        });
        BX.addCustomEvent(window, 'LHE_OnInit', function(obj){obj.SetEditorContent(obj.content);obj.SetContent(obj.content);});
	}
    
    this.Complaint = function(id)
    {
        window.oReviewComplaintDialog = new BX.CDialog({
        	content_url: CURRENT_URL,
            content_post: "RID="+escape(id)+"&ACTION=COMPLAINT",
        	width: 800,
        	height: 320,
        	min_height: 300,
        	min_width: 800,
        	resizable: true
        });
    
	window.oReviewComplaintDialog.Show();        
    }        
}
var jsReview = new aReview();
/*
 * Copyright (c) 1/8/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

var aReviewAdmin = function()
{
    _this = this;
	this.SetBest = function(id)
	{
		BX.ajax.post(CURRENT_URL,{"IMPROVED_AJAX_CALL" : "Y","AJAX_ACTION": "SET_BEST","RID" : id,"sessid":bsmsessid},function (res) {
			eval(res);
			if(ajAction)
			{
				Obhref = BX("review-best-app-link-"+id);
				Obhref.innerHTML = ReviewModMessages.REVIEW_LIST_MODER_DEL_BEST;
				Obhref.setAttribute('onclick','jsReviewAdmin.DelBest('+id+');',0);
				if (Obhref.onclick != 'jsReviewAdmin.DelBest('+id+');') {
					Obhref.onclick = function() { jsReviewAdmin.DelBest(id); };
				}
                BX("review-list-review-"+id);
                BX.removeClass(BX("review-list-review-"+id),'alx_reviews_item');
                BX.addClass(BX("review-list-review-"+id),'alx_reviews_item best');
			}
		}
		);
	}
	this.DelBest = function(id)
	{
		BX.ajax.post(CURRENT_URL,{"IMPROVED_AJAX_CALL" : "Y","AJAX_ACTION": "DEL_BEST","RID" : id,"sessid":bsmsessid},function (res) {
			eval(res);
			if(ajAction)
			{
				Obhref = BX("review-best-app-link-"+id);
				Obhref.innerHTML = ReviewModMessages.REVIEW_LIST_MODER_SET_BEST;
				Obhref.setAttribute('onclick','jsReviewAdmin.SetBest('+id+');',0);
				if (Obhref.onclick != 'jsReviewAdmin.SetBest('+id+');') {
					Obhref.onclick = function() { jsReviewAdmin.SetBest(id); };
				}
                BX("review-list-review-"+id);
                BX.removeClass(BX("review-list-review-"+id),'alx_reviews_item best');
                BX.addClass(BX("review-list-review-"+id),'alx_reviews_item');
			}
		}
		);
	}        
	this.Hide = function(id)
	{
		BX.ajax.post(CURRENT_URL,{"IMPROVED_AJAX_CALL" : "Y","AJAX_ACTION": "HIDE","RID" : id,"sessid":bsmsessid},function (res) {
			eval(res);
			if(ajAction)
			{
				Obhref = BX("review-hide-app-link-"+id);
				Obhref.innerHTML = ReviewModMessages.REVIEW_LIST_MODER_APP;
				Obhref.setAttribute('onclick','jsReviewAdmin.App('+id+');',0);
				if (Obhref.onclick != 'jsReviewAdmin.App('+id+');') {
					Obhref.onclick = function() { jsReviewAdmin.App(id); };
				}
                BX("review-list-review-"+id);
                BX.removeClass(BX("review-list-review-"+id),'alx_reviews_item');
                BX.addClass(BX("review-list-review-"+id),'alx_reviews_item hide');
			}
		}
		);
	}
	this.App = function(id)
	{
		BX.ajax.post(CURRENT_URL,{"IMPROVED_AJAX_CALL" : "Y","AJAX_ACTION": "APPROVED","RID" : id,"sessid":bsmsessid},function (res) {
			eval(res);
			if(ajAction)
			{
				Obhref = BX("review-hide-app-link-"+id);
				Obhref.innerHTML = ReviewModMessages.REVIEW_LIST_MODER_HIDE;
				Obhref.setAttribute('onclick','jsReviewAdmin.Hide('+id+');',0);
				if (Obhref.onclick != 'jsReviewAdmin.Hide('+id+');') {
					Obhref.onclick = function() { jsReviewAdmin.Hide(id); };
				}
                BX.removeClass(BX("review-list-review-"+id),'alx_reviews_item hide');
                BX.addClass(BX("review-list-review-"+id),'alx_reviews_item');
			}				
		}
		);
	}
	this.Delete = function(id)
	{
        if (confirm(ReviewModMessages.REVIEW_LIST_MODER_DEL)) {		  
			BX.ajax.post(CURRENT_URL,{"IMPROVED_AJAX_CALL" : "Y","AJAX_ACTION" : "DELETE", "RID" : id,"sessid":bsmsessid},function (res) {
				eval(res);
				if(ajAction)
				{
                	if (BX.browser.IsOpera())
                		BX.remove(BX('review-list-review-'+id));
                	else
                		BX.hide(BX('review-list-review-'+id));
                        
                    BX('review_cnt').innerHTML = BX('review_cnt').innerHTML-1                    
				}
			});
        }
	}   
    this.Edit = function(id)
    {
        
    } 
}	
var jsReviewAdmin = new aReviewAdmin();
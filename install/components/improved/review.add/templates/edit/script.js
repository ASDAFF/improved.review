/*
 * Copyright (c) 1/8/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

var active_vote_prev_edit = {};
var aReviewVoteEdit = function()
{
    this.Curr = function(num,id)
    {
        for(i=1;i<6;i++)
        {
            div = BX('improved_item_vote_edit_'+id+'_'+i);
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
            div = BX('improved_item_vote_edit_'+id+'_'+i);
            if(active_vote_prev_edit[id]>=i)
                div.className='alx_reviews_form_vote_item alx_reviews_form_vote_item_sel';
            else
                div.className='alx_reviews_form_vote_item';
        }
    }
    this.Set = function(num,field,id)
    {
        active_vote_prev_edit[id] = num;
        BX(field).value=num;
        this.Curr(num,id);
    }
    this.Restore = function()
    {
        for(var key in active_vote_prev_edit)
        {
            num = active_vote_prev_edit[key];
            this.Curr(num,key);
        }
    }
}
var jsReviewVoteEdit = new aReviewVoteEdit();



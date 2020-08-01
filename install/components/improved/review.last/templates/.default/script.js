ReviewShowVotes = function(id)
{
    div = BX('review_all_votest_'+id);
    if(div.style.display=='block')
        div.style.display='none';
    else
        div.style.display='block';
}
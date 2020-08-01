<?

Class aReviewComplaintMain
{
        Function GetByID($ID,$arSelect = Array())
        {
                return  aReviewComplaint::GetList(Array(), Array("ID" => IntVal($ID)));
        }
}
?>
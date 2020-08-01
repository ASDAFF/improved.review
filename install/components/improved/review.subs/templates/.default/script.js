/*
 * Copyright (c) 1/8/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

function ElSub()
{
    var btnOK = new BX.CWindowButton({
            'title': SubBut,
            'action': function()
            {
                    BX.showWait();
    
                    var _form = BX('review_subs');
                    var _email = BX('SUBS_EMAIL');
                    var _emailF = BX('SUBS_EMAIL_F');
                    var _sessid = BX('sessid');
                    var _err = BX('SUBS_ERROR');
                    var _captcha_sid = BX('captcha_sid');
                    var _captcha_word = BX('captcha_word');
                    var _captcha_sidF = BX('captcha_sidF');
                    var _captcha_wordF = BX('captcha_wordF');                    
                    var _captcha_img_s = BX('captcha_img_s');
                    if(_form)
                    {
                            if(_email.value.length > 0)
                            {
                                _emailF.value = _email.value;
                                _captcha_sidF.value = _captcha_sid.value;
                                _captcha_wordF.value = _captcha_word.value;   
                                oData = {"SUBS" : "Y","ACTION" : "CHECK","sessid" : _sessid.value, "EMAIL" : _email.value, "captcha_word":_captcha_word.value,"captcha_sid":_captcha_sid.value};
                                BX.ajax.post(window.location.href,oData,function (res) {
                                        eval(res);
                                        if(subRes.TYPE)
                                        {
                                            _form.submit();
                                            this.parentWindow.Close();
                                        }
                                        else
                                        {
                                            _err.innerHTML = subRes.ERR;
                                            _captcha_sid.value = _captcha_sid.value+Math.random();
                                            //_captcha_word.value = '';
                                            _captcha_img_s.src = '/bitrix/tools/captcha.php?captcha_sid='+_captcha_sid.value+'1';
                                            BX.closeWait();
                                        }
                                });
                            }
                    }
            }
    });    
		window.oSubsDialog = new BX.CDialog({
			content_url: '/bitrix/components/improved/review.subs/window.php',
			width: 400,
			height:200,
			min_height: 170,
			min_width: 400,
			resizable: false,
            buttons: [btnOK, BX.CDialog.btnCancel]
		});
		window.oSubsDialog.Show();
			
return true;   
}
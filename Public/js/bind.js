// 电子邮箱检测
function isEmail(a){
	var b=/^[\w-.]+@[\w-]+(\.(\w)+)*(\.(\w){2,3})$/;
	return b.test(a);
}
function isUsername(str) {
  var re = /^[\w]{1}[\w-_.]{4,18}[\w]{1}$/;
  return re.test(str);
}
function isMobel(value)

{
    if(/^13\d{9}$/g.test(value)||(/^15[0-35-9]\d{8}$/g.test(value))||(/^18[05-9]\d{8}$/g.test(value))||(/^14[0-9]\d{8}$/g.test(value)))
    {
        return true;
    }else{
        return false;
    }

}

// 注册
$(document).ready(function(){
//$.cookie('regok', null);
//帐号检测
var isu = false;
$("#user_name")
	.focus(function()	{$("#user_name_tips").hide();})
	.blur(function()
	{
		if ($(this).val()=='')
		{
			$("#user_name_tips").html('<img src="'+PUBLIC+'/images/error.gif"  align="middle" />').show();
		}else if ($(this).val().length<2) {
			$("#user_name_tips").html('<img src="'+PUBLIC+'/images/error.gif"  align="middle" />').show();
			isu = false;
		}else if ($(this).val().length>20) {
			$("#user_name_tips").html('<img src="'+PUBLIC+'/images/error.gif"  align="middle" />').show();
			isu = false;
		}else {

						$("#user_name_tips").html('<img src="'+PUBLIC+'/images/right.gif" align="middle" />').show();
						//$.cookie('account', $(this).val());
						isu = true;
		}
});




//个人注册emali检测
var ise = false;
$("#email")
	.focus(function()
	{
		$("#email_tips").hide();
	})
	.blur(function()
	{
		if($(this).val()=='')
		{
			$("#email_tips").html('<img src="'+PUBLIC+'/images/error.gif"  align="middle" />').show();
			ise = false;
		}else if(!isEmail($(this).val()))
		{
			$("#email_tips").html('<img src="'+PUBLIC+'/images/error.gif"  align="middle" />').show();
			ise = false;
		}
		else
		{
			$.post(APP+'/Public/checkEmail',
			{
				email:$(this).val()
			},
			function(a)
			{
				if(a=='0'){
                    $("#email_tips").html('<img src="'+PUBLIC+'/images/right.gif"   align="middle" />').show();
                    ise = true;
				}
				else
				{
					$("#email_tips").html('<img src="'+PUBLIC+'/images/error.gif"  align="middle" />').show();
					ise = false;

				}
			})
		}
} );



//密码检测
var isp = false;
$("#password")
	.focus(function()
	{
		$(this).val("");
		$("#password_tips").hide();
	})
	.blur(function()
	{
		if($(this).val()=='')
		{
			$("#password_tips").html('<img src="'+PUBLIC+'/images/error.gif"   align="middle" />').show();

		}
		else if($(this).val().length<6)
		{
			$("#password_tips").html('<img src="'+PUBLIC+'/images/error.gif"   align="middle" />').show();
			isp = false;
		}
		else
		{
			$("#password_tips").html('<img src="'+PUBLIC+'/images/right.gif"   align="middle" />').show();
			isp = true;

		}
	});
// 手机号检测
var ism = false;
$("#mobile").focus(function() {	$("#mobile_tips").hide();}).blur(function(){
	if($(this).val()==''){
		$("#mobile_tips").html('<img src="'+PUBLIC+'/images/error.gif"align="middle" />手机号码不能为空').show();
		ism = false;
		}else{
            if(!isMobel($(this).val()) ||$('#mobile').val().length>11){
				$("#mobile_tips").html('<img src="'+PUBLIC+'/images/error.gif" align="middle" />请输入正确的手机号').show();
				ism = false;
			}else{

			$.post(APP+'/Public/checkPhone',
			{
				mobiles:$(this).val()
			},
			function(a)
			{
				if(a=='1'){
                  $("#mobile_tips").html(' <img src="'+PUBLIC+'/images/right.gif" align="middle" />手机号正确').show();
				    ism = true;
				}
				else
				{
					$("#mobile_tips").html(' <img src="'+PUBLIC+'/images/error.gif" align="middle" />此手机号已注册过了').show();
					ism = false;

				}
			})



			}
	}
});


//提交时的检测
$("#bindFrom").submit( function () {

	var a=$("#email").val(),p=$("#password").val(),u=$("#user_name").val(),m=$("#mobile").val();
	if(!u)
	{
	    $("#user_name_tips").html('<img src="'+PUBLIC+'/images/error.gif" align="middle" />用户名不能为空').show();
		return false;
		}
	if(!m)
	{
	    $("#mobile_tips").html('<img src="'+PUBLIC+'/images/error.gif" align="middle" />手机号码不能为空').show();
		return false;
		}
	if(!a){

		$("#email_tips").html('<img src="'+PUBLIC+'/images/error.gif" align="middle" />邮箱不能为空').show();
		return false;
	}
	if(!p){
		$("#password_tips").html('<img src="'+PUBLIC+'/images/error.gif" align="middle" />密码不能为空').show();
		return false;
	}

	if(!ism||!isp||!ise){
		return false;
	}
	return true;
});


});
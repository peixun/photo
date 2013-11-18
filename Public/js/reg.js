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
    if(/^13\d{9}$/g.test(value)||(/^15[0-35-9]\d{8}$/g.test(value))||(/^18[01-9]\d{8}$/g.test(value))||(/^14[0-9]\d{8}$/g.test(value)))
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

var isus = false;
$("#names")
	.focus(function()	{$("#names_tips").hide();})
	.blur(function()
	{
		if ($(this).val()=='')
		{
			$("#names_tips").html('<img src="'+PUBLIC+'/images/error.gif"  align="middle" />').show();
            isus = false;
		}else if ($(this).val().length<2) {
			$("#names_tips").html('<img src="'+PUBLIC+'/images/error.gif"  align="middle" />').show();
			isus = false;
		}else if ($(this).val().length>20) {
			$("#names_tips").html('<img src="'+PUBLIC+'/images/error.gif"  align="middle" />').show();
			isus = false;
		}else {

						$("#names_tips").html('<img src="'+PUBLIC+'/images/right.gif"   align="middle" />').show();
						//$.cookie('account', $(this).val());
						isus = true;
		}
});

//固定电话
var istel = false;
$("#tel")
	.focus(function()	{$("#tel_tips").hide();})
	.blur(function()
	{
		if ($(this).val()=='')
		{
			$("#tel_tips").html('<img src="'+PUBLIC+'/images/error.gif"  align="middle" />').show();
            istel = false;
		}else if ($(this).val().length<8) {
			$("#tel_tips").html('<img src="'+PUBLIC+'/images/error.gif"  align="middle" />').show();
			istel = false;
		}else if ($(this).val().length>20) {
			$("#tel_tips").html('<img src="'+PUBLIC+'/images/error.gif"  align="middle" />').show();
			istel = false;
		}else {

						$("#tel_tips").html('<img src="'+PUBLIC+'/images/right.gif"   align="middle" />').show();
						//$.cookie('account', $(this).val());
						istel = true;
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

//emali检测
var ises = false;
$("#emails")
	.focus(function()
	{
		$("#emails_tips").hide();
	})
	.blur(function()
	{
		if($(this).val()=='')
		{
			$("#emails_tips").html('<img src="'+PUBLIC+'/images/error.gif"  align="middle" />').show();
			ises = false;
		}else if(!isEmail($(this).val()))
		{
			$("#emails_tips").html('<img src="'+PUBLIC+'/images/error.gif"  align="middle" />').show();
			ises = false;
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
                    $("#emails_tips").html('<img src="'+PUBLIC+'/images/right.gif"   align="middle" />').show();
                    ises = true;
				}
				else
				{
					$("#emails_tips").html('<img src="'+PUBLIC+'/images/error.gif"  align="middle" />').show();
					ises = false;

				}
			})
		}
} );

//emali检测
//var isii = false;
$("#oldpws")
	.focus(function()
	{
		$("#oldpws_tips").hide();
	})
	.blur(function()
	{
		if($(this).val()=='')
		{
			$("#email_tips").html(' <span class="regerror"></span>').show();
		}
		else
		{
			$.post(APP+'/Public/checkOldpw',
			{
				oldpws:$(this).val()
			},
			function(a)
			{
				if(a=='1'){
                    $("#oldpws_tips").html('<img src="'+PUBLIC+'/images/right.gif" align="middle" />恭喜您!输入的旧密码正确').show();
                    isii = true;
				}
				else
				{
					$("#oldpws_tips").html(' <img src="'+PUBLIC+'/images/error.gif"  align="middle"/>输入的旧密码不正确').show();
					isii = false;

				}
			})
		}
} );


//充值卡检测
$("#cardnum")
	.focus(function()
	{
		$("#cardnum_tips").hide();
	})
	.blur(function()
	{
		if($(this).val()=='')
		{
			$("#cardnum_tips").html(' <img src="'+PUBLIC+'/images/error.gif" />充值卡号不能为空').show();
		}else if(!isNum($(this).val()))
		{
			$("#cardnum_tips").html(' <img src="'+PUBLIC+'/images/error.gif" />充值卡号格式不正确').show();

		}

} );

//充值卡密码检测
$("#cardpw")
	.focus(function()
	{
		$("#cardpw_tips").hide();
	})
	.blur(function()
	{
		if($(this).val()=='')
		{
			$("#cardpw_tips").html(' <img src="'+PUBLIC+'/images/error.gif" />充值卡号密码不能为空').show();
		}

} );
//充值卡密码检测
$("#recardpwss")
	.focus(function()
	{
		$(this).val("");
		$("#recardpwss_tips").hide();
	})
	.blur(function()
	{
		if($(this).val()=='')
		{
			$("#recardpwss_tips").html(' <img src="'+PUBLIC+'/images/error.gif" />重复充值卡号密码不能为空').show();

		}
		else if($(this).val()!=$("#cardpw").val())
		{
			$("#recardpwss_tips").html('<img src="'+PUBLIC+'/images/error.gif" /> 两次输入的充值卡号密码不一致').show();

		}

	});


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

//密码检测
var isps = false;
$("#passwords")
	.focus(function()
	{
		$(this).val("");
		$("#passwords_tips").hide();
	})
	.blur(function()
	{
		if($(this).val()=='')
		{
			$("#passwords_tips").html('<img src="'+PUBLIC+'/images/error.gif"   align="middle" />').show();
			isps = false;
		}
		else if($(this).val().length<6)
		{
			$("#passwords_tips").html('<img src="'+PUBLIC+'/images/error.gif"   align="middle" />').show();
			isps = false;
		}
		else
		{
			$("#passwords_tips").html('<img src="'+PUBLIC+'/images/right.gif"   align="middle" />').show();
			isps = true;

		}
	});


// 重复密码检测
var isr = false;
$("#repassword")
	.focus(function()
	{
	 	$(this).val("");
		$("#repassword_tips").hide();
	})
	.blur(function()
	{
		if($(this).val()=='')
		{
			$("#repassword_tips").html('<img src="'+PUBLIC+'/images/error.gif"   align="middle" />').show();
			isr = false;
		}
		else if($(this).val()!=$("#password").val())
		{
			$("#repassword_tips").html('<img src="'+PUBLIC+'/images/error.gif"   align="middle" />').show();
			isr = false;
		}
		else
		{
			$("#repassword_tips").html('<img src="'+PUBLIC+'/images/right.gif"   align="middle" />').show();
			isr = true;
		}
	});

// 重复密码检测
var isrs = false;
$("#repasswords")
	.focus(function()
	{
	 	$(this).val("");
		$("#repasswords_tips").hide();
	})
	.blur(function()
	{
		if($(this).val()=='')
		{
			$("#repasswords_tips").html('<img src="'+PUBLIC+'/images/error.gif"   align="middle" />').show();
			isrs = false;
		}
		else if($(this).val()!=$("#passwords").val())
		{
			$("#repasswords_tips").html('<img src="'+PUBLIC+'/images/error.gif"   align="middle" />').show();
			isrs = false;
		}
		else
		{
			$("#repasswords_tips").html('<img src="'+PUBLIC+'/images/right.gif"   align="middle" />').show();
			isrs = true;
		}
	});





    // 重复新密码检测
     var isaas = false;
$("#address")
	.focus(function()
	{
		$(this).val("");
		$("#address_tips").hide();
	})
	.blur(function()
	{
		if($(this).val()=='')
		{
			$("#address_tips").html(' <img src="'+PUBLIC+'/images/error.gif" align="middle" />地址不能为空').show();
             var isaas = false;
		}
		else
		{
            var isaas = true;
			$("#address_tips").html('<img src="'+PUBLIC+'/images/right.gif" align="middle"/> 地址检查正确！').show();

		}
	});
    var isf = false;
    $("#fname")
	.focus(function()
	{
		$(this).val("");
		$("#fname_tips").hide();
	})
	.blur(function()
	{
		if($(this).val()=='')
		{
			$("#fname_tips").html(' <img src="'+PUBLIC+'/images/error.gif" align="middle"/>姓名不能为空').show();
             var isf = false;
		}
		else
		{
            var isf = true;
			$("#fname_tips").html('<img src="'+PUBLIC+'/images/right.gif" align="middle" /> 姓名检查正确！').show();

		}
	});
// 重复充值卡密码检测
$("#recardpw")
	.focus(function()
	{
		$(this).val("");
		$("#recardpw_tips").hide();
	})
	.blur(function()
	{
		if($(this).val()=='')
		{
			$("#recardpw_tips").html(' <img src="'+PUBLIC+'/images/error.gif" />').show();
		}
		else if($(this).val()!=$("#cardpw").val())
		{
			$("#recardpw_tips").html('<img src="'+PUBLIC+'/images/error.gif" />').show();

		}
		else
		{
			$("#recardpw_tips").html('<img src="'+PUBLIC+'/images/right.gif" />').show();

		}
	});


//检测密码安全等级
$("#pw").keyup( function()
{
	var pwd=$(this).val();
	var sRe=[/[a-zA-Z]/g,/\d/g,/[^a-zA-Z0-9]/g];
	var sLe=[1,2,5];
	var sFa=[0,0,10,20];
	var iKn=0;
	var iSt=0;
	for(i=0;i<sRe.length;i++)
	{
		var cMa=pwd.match(sRe[i]);
		if(cMa!=null){
			iSt+=cMa.length*sLe[i];
			iKn++;
		}
	}

	iSt+=sFa[iKn];
	//空
	if(iSt == 0)
	{
		$('#pw_low').removeClass('pw_low');
		$('#pw_mid').removeClass('pw_mid');
		$('#pw_high').removeClass('pw_high');
	}else if(iSt>=15 && iSt<25){//中
		$('#pw_low').removeClass('pw_low');
		$('#pw_mid').addClass('pw_mid');
		$('#pw_high').removeClass('pw_high');
	}else if(iSt>=25){//高
		$('#pw_low').removeClass('pw_low');
		$('#pw_mid').removeClass('pw_mid');
		$('#pw_high').addClass('pw_high');
	}else{//低
		$('#pw_low').addClass('pw_low');
		$('#pw_mid').removeClass('pw_mid');
		$('#pw_high').removeClass('pw_high');
	}

} );

//检测密码安全等级
$("#pwd1").keyup( function()
{
	var pwd=$(this).val();
	var sRe=[/[a-zA-Z]/g,/\d/g,/[^a-zA-Z0-9]/g];
	var sLe=[1,2,5];
	var sFa=[0,0,10,20];
	var iKn=0;
	var iSt=0;
	for(i=0;i<sRe.length;i++)
	{
		var cMa=pwd.match(sRe[i]);
		if(cMa!=null){
			iSt+=cMa.length*sLe[i];
			iKn++;
		}
	}

	iSt+=sFa[iKn];
	//空
	if(iSt == 0)
	{
		$('#pw_low').removeClass('pw_low');
		$('#pw_mid').removeClass('pw_mid');
		$('#pw_high').removeClass('pw_high');
	}else if(iSt>=15 && iSt<25){//中
		$('#pw_low').removeClass('pw_low');
		$('#pw_mid').addClass('pw_mid');
		$('#pw_high').removeClass('pw_high');
	}else if(iSt>=25){//高
		$('#pw_low').removeClass('pw_low');
		$('#pw_mid').removeClass('pw_mid');
		$('#pw_high').addClass('pw_high');
	}else{//低
		$('#pw_low').addClass('pw_low');
		$('#pw_mid').removeClass('pw_mid');
		$('#pw_high').removeClass('pw_high');
	}

} );

// 验证码检测
var isv = false;
$("#verify").focus(function() {	$("#ver_tips").hide();}).blur(function(){
	if($(this).val()==''){
		$("#verify_tips").html('<img src="'+PUBLIC+'/images/error.gif" align="middle"/>验证码不能为空').show();
		isv = false;
		}else{
		$.post(APP+'/Public/checkVerify',{
			verify:$(this).val()
			} ,
            function(a){
			if(a=='1'){
				$("#verify_tips").html(' <img src="'+PUBLIC+'/images/right.gif" align="middle"/>验证码正确').show();
				isv = true;

			} else{
				$("#verify_tips").html(' <img src="'+PUBLIC+'/images/error.gif" align="middle" />验证码不对').show();
				isv = false;

			}
			} )
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

var ismc = false;
$("#company_name")
	.focus(function()	{$("#company_name_tips").hide();})
	.blur(function()
	{
		if ($(this).val()=='')
		{
			$("#company_name_tips").html('<img src="'+PUBLIC+'/images/error.gif" align="middle" />公司不能为空').show();
            ismc = false;
		}else if ($(this).val().length<3) {
			$("#company_name_tips").html('<img src="'+PUBLIC+'/images/error.gif" align="middle" />不能少于3个字符').show();
            ismc = false;

		}else if ($(this).val().length>120) {
			$("#company_name_tips").html('<img src="'+PUBLIC+'/images/error.gif" align="middle" />不能大于120个字符').show();

		}

        else {
			$.post(APP+'/Public/checkCompany',
				{company_name:$(this).val()},
				function(a)
				{
					if(a=='1')
					{
						$("#company_name_tips").html('<img src="'+PUBLIC+'/images/right.gif" align="middle" />恭喜您!该公司名可以使用').show();
						//$.cookie('account', $(this).val());
                        ismc = true;

					}
					else
					{
						$("#company_name_tips").html('<img src="'+PUBLIC+'/images/error.gif" align="middle" />此公司名已注册，请更改').show();
                        ismc = false;
					}
				})
		}
});

// 手机号检测
var isms = false;
$("#mobiles").focus(function() {	$("#mobiles_tips").hide();}).blur(function(){
	if($(this).val()==''){
		$("#mobiles_tips").html('<img src="'+PUBLIC+'/images/error.gif" align="middle" />手机号码不能为空').show();
		  isms = false;
		}else{
            //var t=/^(13\d{9})|(15\d{9})|(18\d{9})|(0\d{10,11})$/;
			if(!isMobel($(this).val())||$('#mobiles').val().length>11){
				$("#mobiles_tips").html('<img src="'+PUBLIC+'/images/error.gif" align="middle" />请输入正确的手机号').show();
				isms = false;
			}else{

			$.post(APP+'/Public/checkPhone',
			{
				mobiles:$(this).val()
			},
			function(a)
			{
				if(a=='1'){
                  $("#mobiles_tips").html(' <img src="'+PUBLIC+'/images/right.gif" align="middle" />手机号正确').show();
				    isms = true;
				}
				else
				{
					$("#mobiles_tips").html(' <img src="'+PUBLIC+'/images/error.gif" align="middle" />此手机号已注册过了').show();
					isms = false;

				}
			})



			}
	}
});


// 邮编检测
var isms = false;
$("#postcode").focus(function() {	$("#postcode_tips").hide();}).blur(function(){
if($(this).val()!=''){
			var t=/^(\d{6})$/;
			if(!t.test($('#postcode').val()) ||$('#postcode').val().length>6){
				$("#postcode_tips").html('<img src="'+PUBLIC+'/images/error.gif" align="middle"/>').show();
				//$('#postcode').val('');
				isms = false;
			}
			else{
				$("#postcode_tips").html(' <img src="'+PUBLIC+'/images/right.gif" align="middle" />').show();
				isms = true;
			}

		}

});



//提交时的检测
$("#regForm").submit( function () {

	var a=$("#email").val(),p=$("#password").val(),r=$("#repassword").val(),u=$("#user_name").val(),m=$("#mobile").val(),d=$("#agreement").is(":checked");;
	if(!u)
	{
	    $("#user_name_tips").html('<img src="'+PUBLIC+'/images/error.gif" align="middle" />用户名不能为空').show();
		return false;
		}
	if(!m)
	{
	    $("#mobile_tips").html('<img src="'+PUBLIC+'/images/error.gif" align="middle" />登录账户不能为空').show();
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
	if(!r){
		$("#repassword_tips").html(' <img src="'+PUBLIC+'/images/error.gif" align="middle" />确认密码不能为空').show();
		return false;
	}
	if(!d){
		alert("请确认是否已经阅读过《注册服务条款》!");
		return false;
	}

	if(!ise||!isp||!isr||!isu||!ism){
		return false;
	}
	return true;
});


//提交时的检测
$("#regForm12").submit( function () {

	var a=$("#email").val(),p=$("#password").val(),r=$("#repassword").val(),u=$("#user_name").val(),m=$("#mobile").val(),d=$("#agreement").is(":checked");;
	if(!u)
	{
	    $("#user_name_tips").html('<img src="'+PUBLIC+'/images/error.gif" align="middle" />用户名不能为空').show();
		return false;
		}
	if(!m)
	{
	    $("#mobile_tips").html('<img src="'+PUBLIC+'/images/error.gif" align="middle" />登录账户不能为空').show();
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
	if(!r){
		$("#repassword_tips").html(' <img src="'+PUBLIC+'/images/error.gif" align="middle" />确认密码不能为空').show();
		return false;
	}


	if(!ise||!isp||!isr||!isu||!ism){
		return false;
	}
	return true;
});


//提交时的检测
$("#regForm1").submit( function () {

	var a=$("#emails").val(),m=$("#mobiles").val(),p=$("#passwords").val(),r=$("#repasswords").val(),u=$("#names").val(),c=$("#company_name").val(),t=$("#tel").val(),d=$("#agreement1").is(":checked");
	if(!u)
	{
	    $("#names_tips").html('<img src="'+PUBLIC+'/images/error.gif" align="middle" />企业负责人不能为空').show();
		return false;
		}
	if(!m)
	{
	    $("#mobiles_tips").html('<img src="'+PUBLIC+'/images/error.gif" align="middle" />登录账户人不能为空').show();
		return false;
		}
	if(!t)
	{
	    $("#tel_tips").html('<img src="'+PUBLIC+'/images/error.gif" align="middle" />固定电话不能为空').show();
		return false;
		}
	if(!c)
	{
	    $("#company_name_tips").html('<img src="'+PUBLIC+'/images/error.gif"  align="middle"/>公司名称不能为空').show();
		return false;
		}
	if(!a){

		$("#emails_tips").html('<img src="'+PUBLIC+'/images/error.gif" align="middle" />邮箱不能为空').show();
		return false;
	}
	if(!p){
		$("#passwords_tips").html('<img src="'+PUBLIC+'/images/error.gif" align="middle" />密码不能为空').show();
		return false;
	}
	if(!r){
		$("#repasswords_tips").html(' <img src="'+PUBLIC+'/images/error.gif" align="middle" />确认密码不能为空').show();
		return false;
	}
	if(!d){
		alert("请确认是否已经阅读过《注册服务条款》!");
		return false;
	}

	if(!ises||!isms||!isus||!ismc||!isps||!isrs||!istel){
		return false;
	}
	return true;
});



});
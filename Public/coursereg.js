// 邮箱检测
function isEmail(a){
	var b=/^[\w-.]+@[\w-]+(\.(\w)+)*(\.(\w){2,3})$/;
	return b.test(a);
}


/*
判断是否为数字
---------------------------------------
*/
function isNum(num){
    var i,j,strTemp;
    strTemp="0123456789";
    if ( num.length== 0){
        return false;
    }
    for (i=0;i<num.length;i++){
        j=strTemp.indexOf(num.charAt(i));   
        if (j==-1){//说明有字符不是数字
            return false;
        }
    }
   
    return true;//说明是数字
}
/*
判断是不是手机号码
---------------------------------
*/
function isPhone(str){

    reg=/^[0]?13\d{9}$/gi;
    reg2=/^[0]?1\d{10}$/gi;
    if(!reg.test(str)&&!reg2.test(str)){
        return false;
    }
    return true;
}

var namebool;

//登录名检测
$(document).ready(function(){

$("#name")
	.focus(function()	{$("#name_tips").html().hidden();})
	.blur(function()
	{	 
		
		if ($(this).val()=='')
		{
			$("#name_tips").html('<font color=red>用户名不能为空</font>').show();
		}else if ($(this).val().length<4) {
			$("#name_tips").html('<font color=red>不能少于4个字符</font>').show();

		}else if ($(this).val().length>20) {
			$("#name_tips").html('<font color=red>不能大于20个字符</font>').show();

		}    
        else {
			$.post(APP+'/Public/checkAccount',
				{username:$(this).val()},
				function(a)
				{
					
					if(a=='0') 	{  //数据库中已有此用户
					$("#name_tips").html('<font color=red>对不起，此帐号已注册，请更改!</font>').show();
						namebool = false;
				    }else{
						alert(a);
						//alert("恭喜您!该帐号可以使用");					
						$("#name_tips").html('<font color="green">恭喜您!该帐号可以使用</font>').show();	
						namebool = true;
					}
				})
		}
});


$("#Password")
	.focus(function(){
		$("#password_tips").html("6-20个字符（字母、数字、特殊符号），不区分大小写").show();
	})
	.blur(function()
	{
		if ($(this).val()=='')
		{
			$("#password_tips").html('<font color=red>密码不能为空</font>').show();
		}else if ($(this).val().length<6) {
			$("#password_tips").html('<font color=red>密码不能少于6个字符</font>').show();

		}else if ($(this).val().length>20) {
			$("#password_tips").html('<font color=red>密码不能大于20个字符</font>').show();

		}	
	});

	

$("#tags")
	.focus(function(){
		$(this).val() = '';
	});
	

 

 
	//检测密码安全等级
$("#Password").keyup( function()
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
	if(iSt < 6)
	{
		
		$("#password_safe").html('<img src="./images/bjw.gif">').show();
	}else if(iSt>=20 && iSt<30){//中
		
		$("#password_safe").html('<img src="./images/bjz.gif">').show();
		
	}else if(iSt>=30){//高
		$("#password_safe").html('<img src="./images/bjg.gif">').show();
	}else{//低
		 $("#password_safe").html('<img src="./images/bjd.gif">').show();
	}

} );


$("#OldPassword")
		.focus(function(){$("#oldPassword_tips").hide();})
		.blur(function(){
			if($(this).val() == ''){
				$("#oldPassword_tips").html('<font color=red>不能为空!</font>').show();
			}
			else if($(this).val() != $('#Password').val() ){
				$("#oldPassword_tips").html('<font color=red>两次输入密码不一致!</font>').show();
			}
		
		
		});


//emali检测
var emailbool;
$("#email")
	.focus(function()
	{
		$("#email_tips").hide();
	})
	.blur(function()
	{
		 
		if($(this).val()=='')
		{
			$("#email_tips").html(' <font color=red>邮箱不能为空</font> ').show();
		}else if(!isEmail($(this).val()))
		{
			$("#email_tips").html(' <font color=red>邮箱格式不正确</font>').show();
			ise = false;
		}else{

				$.post(APP+'/Public/checkEmail',
				{emails:$(this).val()},
				function(a)
				{
					if(a=='0'){  //数据库中已有此用户
                  
						$("#email_tips").html('<font color=red>对不起，此email已注册，请更改!</font>').show();
						emailbool = false;
				    }else{
					
						//alert("恭喜您!该帐号可以使用");					
						$("#email_tips").html('<font color="green">恭喜您!该email可以使用</font>').show();
						emailbool = true;	
					}
				})
		}	
} );




//重复密码
var  repwlbool;
$("#repw")
	.focus(function()
	{
		$(this).val("");
		$("#repw_tips").hide();
	})
	.blur(function()
	{
		if($(this).val()=='')
		{
			$("#repw_tips").html('<font color=red>重复密码不能为空').show();
			repwlbool= false;
		}
		else if($(this).val()!=$("#pw").val())
		{
			$("#repw_tips").html('<font color=red> 两次输入的密码不一致').show();
			repwlbool= false;
		}
		else
		{
			$("#repw_tips").html('<font color="green"> 密码检查正确！').show();
			repwlbool= true;
		}
	});

//密码
var  pwlbool;
$("#pw")
	.focus(function()
	{
		$(this).val("");
		$("#pw_tips").hide();
	})
	.blur(function()
	{
		if($(this).val()=='')
		{
			$("#pw_tips").html('<font color=red>密码不能为空').show();
			pwlbool= false;
		}

		else
		{
			$("#pw_tips").html('<font color="green"> 密码检查正确！').show();
			pwlbool= true;
		}
	});


$("#Answer")
	.focus(function(){
		$("#answer_tips").hide();
	})
	.blur(function(){
	    if($(this).val() == ''){
			$("#answer_tips").html('<font color=red>提示回答不能为空！</font>').show();
		}
	});
	
	
$("#LinkName")
	.focus(function(){
		$("#linkname_tips").hide();
	})
	.blur(function(){
	    if($(this).val() == ''){
			$("#linkname_tips").html('<font color=red>不能为空！</font>').show();
		}
	});




	
	
 
$("#MobilePhone")
	.focus(function(){
		$("#mobile_tips").hide();
	})
	.blur(function(){
	    if($(this).val() ==''){}
	    else if(!isPhone($(this).val())){
			$("#mobile_tips").html('<font color=red>请输入正确的手机号！</font>').show();
		}
	});	
$("#QQ")
	.focus(function(){
		$("#qq_tips").hide();
	})
	.blur(function(){
	   
		if(!isNum($(this).val())){
			$("#qq_tips").html('<font color=red>QQ号只能为数字！</font>').show();
		}
	});	
$("#CompanyName")
	.focus(function(){
		$("#comname_tips").hide();
	})
	.blur(function(){
	    if($(this).val() == ''){
			$("#comname_tips").html('<font color=red>不能为空！</font>').show();
		}
	});	
$("#CompanyAddress")
	.focus(function(){
		$("#comaddr_tips").hide();
	})
	.blur(function(){
	    if($(this).val() == ''){
			$("#comaddr_tips").html('<font color=red>不能为空！</font>').show();
		}
	});	
$("#DealinAdd")
	.focus(function(){
		$("#dealinadd_tips").hide();
	})
	.blur(function(){
	    if($(this).val() == ''){
			$("#dealinadd_tips").html('<font color=red>不能为空！</font>').show();
		}
	});		
$("#CompanyDesc")
	.focus(function(){
		$("#comdesc_tips").hide();
	})
	.blur(function(){
	    if($(this).val() == ''){
			$("#comdesc_tips").html('<font color=red>不能为空！</font>').show();
		}
	});	

$("#frm").submit( function (){
	if(!ise){

		$("#email_tips").html(' <font color=red>email可能已存在或格式不正确</font> ').show();
		return false;
	}else if(!isu){
		$("#uname_tips").html(' <font color=red>昵称可能以存在或者格式不争取</font> ').show();
		return false;
	}else if(!ispw){
		$("#repw_tips").html('<font color="red"> 检查密码是否正确！').show();
		return false; 
	}else{
		return true;
	}
	
	

});




	
	
	//提交时的检测	
$("#regForm").submit( function () {
		var p=$("#pw").val(),r=$("#repw").val(),e=$("#email").val();
			if(!e){
				
			$("#email_tips").html('<font color=red>邮箱不能为空</font>').show();
			return false
		}
 
		
		if(!p){
			$("#pw_tips").html('<font color=red>密码不能为空</font>').show();
			return false
		}

		if(!r){
			$("#repw_tips").html('<font color=red>重复密码不能为空</font>').show();
			return false
		}
	
			 
	    if(!emailbool||!repwlbool||!pwbool)
	    {
		return false
		}
		return true
	});
		

});
 
function itemShow(itemName,showId,num,bgItemName,clsName)       //(itemName+num)系列栏目名称，showID需要显示的编号
{
	var clsNameArr=new Array(2)
	if(clsName.indexOf("|")<=0){
		clsNameArr[0]=clsName
		clsNameArr[1]=""
	}else{
		clsNameArr[0]=clsName.split("|")[0]
		clsNameArr[1]=clsName.split("|")[1]
	}
	for(i=1;i<=num;i++)
	{ 
//	alert(i);

		if(document.getElementById(itemName+i)!=null)
			document.getElementById(itemName+i).style.display="none"
		if(document.getElementById(bgItemName+i)!=null)
			document.getElementById(bgItemName+i).className=clsNameArr[1]
		
		if(i==showId)
		{
			if(document.getElementById(itemName+i)!=null)
				document.getElementById(itemName+i).style.display=""
			else
				alert("未找到您请求的内容!")
			if(document.getElementById(bgItemName+i)!=null)
				document.getElementById(bgItemName+i).className=clsNameArr[0]
		}
	}
}
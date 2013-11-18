function wValDisp(sts,idx)
{
	if(getObject("sbArea_"+ sts +"").style.display == "none")
	{
		getObject("sbArea_"+ sts +"").style.display = "";
	}
	else
	{
		getObject("sbArea_"+ sts +"").style.display = "none";
	}
}
function getObject(objectId)
{ 
	// checkW3C DOM, then MSIE 4, then NN 4. 
	if(document.getElementById && document.getElementById(objectId))
	{ 
		return document.getElementById(objectId);
	}
	else if (document.all && document.all(objectId))
	{ 
		return document.all(objectId); // IE4,5.0 
	}
	else if (document.layers && document.layers[objectId])
	{ 
		return document.layers[objectId];  // Netscape 4.x 
	}
	else
	{ 
		return false; 
	} 
}
function wValChg(idx,sts){
	if(idx == "s") getObject("wn_"+ sts +"").innerHTML = "搜户型";
	if(idx == "h") getObject("wn_"+ sts +"").innerHTML = "人才";
	if(idx == "j") getObject("wn_"+ sts +"").innerHTML = "网站";
	if(idx == "z") getObject("wn_"+ sts +"").innerHTML = "资讯";
	getObject("sbArea_"+ sts +"").style.display = "none";
}
function getNavigatorType()
{
	if(navigator.appName == "Microsoft Internet Explorer")
		return 1;  
	else if(navigator.appName == "Netscape")
		return 2;	
	else 
		return 0;
}
function setSelBox(event)
{
	var _event;
	switch (getNavigatorType())
	{
		case 1 : // IE
			_event = window.event;
			node = _event.srcElement;
			nodeName = _event.srcElement.className;
			break;
		case 2 : // Netscape
			_event = event;
			node = _event.target;
			nodeName = _event.target.className;
			break;
		default :
			nodeName = "None"; 
			break;
	}
	if(nodeName == "dselObj")
	{
		
	}else
	{
		try
		{
			document.getElementById("sbArea_h").style.display = "none";
		}
		catch(e){}
	}
}
document.onmousedown = setSelBox;
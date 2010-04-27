/*
 *This code sets GO.hasFocus to determine if GO has the focus
 */
GO.hasFocus=true;

if(navigator.appName == "Microsoft Internet Explorer"){
	document.onfocusin=function(){
		GO.hasFocus=true;
		//document.title='Focus';
	};
	document.onfocusout=function(){
		GO.hasFocus=false;
		//document.title='No focus';
	};
}else
{
	window.onfocus=function(){
		GO.hasFocus=true;
	}
	window.onblur=function(){
		GO.hasFocus=false;
	};
}

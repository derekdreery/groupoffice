/*
 *This code sets GO.hasFocus to determine if GO has the focus
 */
GO.hasFocus=true;

if(navigator.appName == "Microsoft Internet Explorer"){
	document.onfocusin=function(){
		GO.hasFocus=true;
		//document.title='Focus';
		if(GO.mainLayout)
			GO.mainLayout.fireEvent('focus', GO.mainLayout);
	};
	document.onfocusout=function(){
		GO.hasFocus=false;
		//document.title='No focus';
		if(GO.mainLayout)
			GO.mainLayout.fireEvent('blur', GO.mainLayout);
	};
}else
{
	window.onfocus=function(){
		GO.hasFocus=true;
		if(GO.mainLayout)
			GO.mainLayout.fireEvent('focus', GO.mainLayout);
	}
	window.onblur=function(){
		GO.hasFocus=false;
		if(GO.mainLayout)
			GO.mainLayout.fireEvent('blur', GO.mainLayout);
	};
}

GO.grid.ColumnRenderers = {
	
	yesNo : function(val){
		if(val == 1)
			return GO.lang.yes;
		else if(val == 0)
			return GO.lang.no;
		else
			return val;
	}
	
	
	
	
}
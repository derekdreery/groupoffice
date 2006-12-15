var FCKToolbarAutodataCombo = function( tooltip, style )
{
	this.CommandName = 'InsertHTML';
	//this.Command	= new FCKInsertHTMLCommand();
	this.Label		= this.GetLabel() ;
	this.Tooltip	= tooltip ? tooltip : this.Label ;
	this.Style		= style ? style : FCK_TOOLBARITEM_ICONTEXT ;
}

// Inherit from FCKToolbarSpecialCombo.
FCKToolbarAutodataCombo.prototype = new FCKToolbarSpecialCombo;

FCKToolbarAutodataCombo.prototype.GetLabel = function()
{
	return FCKLang.AutodataLabel;
}

FCKToolbarAutodataCombo.prototype.CreateItems = function( targetSpecialCombo )
{
	// Load the XML file into a FCKXml object.
	var oXml = new FCKXml() ;
	oXml.LoadUrl( FCKPlugins.Items['go_autodata'].Path + 'autodata.php') ;
	
	// Get the "Style" nodes defined in the XML file.
	var aAutodataNodes = oXml.SelectNodes( 'autodata/option' ) ;

	// Add each style to our "Styles" collection.
	for ( var i = 0 ; i < aAutodataNodes.length ; i++ )
	{		
		var text = aAutodataNodes[i].attributes.getNamedItem('text').value;		
		var value = aAutodataNodes[i].attributes.getNamedItem('value').value;
		this._Combo.AddItem(value, text) ;
	}	
}


var oAutodataCombo = new FCKToolbarAutodataCombo('go_autodata') ;
oAutodataCombo.FieldWidth = 250 ;
oAutodataCombo.PanelWidth = 300 ;
FCKToolbarItems.RegisterItem( 'go_autodata', oAutodataCombo ) ;

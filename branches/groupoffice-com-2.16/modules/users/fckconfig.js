FCKConfig.SpellChecker			= 'SpellerPages' ;

FCKConfig.LinkBrowser=false;
FCKConfig.LinkUpload=false;
FCKConfig.ImageBrowser=false;
FCKConfig.ImageUpload=false;
FCKConfig.FlashUpload =false;
FCKConfig.UseBROnCarriageReturn=true;
FCKConfig.IEForceVScroll=true;
FCKConfig.FullPage = true;

FCKConfig.ToolbarSets["Users"] = [
	['Source'],
	['Cut','Copy','Paste','PasteText','PasteWord','-','Print','SpellCheck'],
	['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
	['Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript'],
	['OrderedList','UnorderedList','-','Outdent','Indent'],
	['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'],
	['Link','Unlink','Anchor'],
	['Table','Rule','SpecialChar','UniversalKey'],
	'/',
	['FontFormat','FontName','FontSize'],
	['TextColor','BGColor'],['go_autodata']
] ;

var FCKInsertHTMLCommand = function()
{
	this.Name = 'InsertHTML' ;
}

FCKInsertHTMLCommand.prototype.Execute = function(html)
{
  FCK.InsertHtml(html);
}

FCKInsertHTMLCommand.prototype.GetState = function()
{
	return FCK_TRISTATE_OFF ;
}

FCKCommands.RegisterCommand('InsertHTML', new FCKInsertHTMLCommand());


FCKConfig.Plugins.Add( 'go_autodata', 'en');


FCKConfig.SpellChecker			= 'SpellerPages' ;

FCKConfig.LinkBrowser=false;
FCKConfig.LinkUpload=false;
FCKConfig.ImageBrowser=false;
FCKConfig.ImageUpload=false;
FCKConfig.FlashUpload =false;
FCKConfig.UseBROnCarriageReturn=true;
FCKConfig.IEForceVScroll=true;

FCKConfig.ToolbarSets["template"] = [
	['Source','DocProps','-','Templates'],
	['Cut','Copy','Paste','PasteText','PasteWord','-','Print','SpellCheck'],
	['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
	['Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript'],
	['OrderedList','UnorderedList','-','Outdent','Indent'],
	['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'],
	['Link','Unlink','Anchor'],
	['Image', 'Table','Rule','SpecialChar','PageBreak','UniversalKey'],
	'/',
	['FontFormat','FontName','FontSize'],
	['TextColor','BGColor'],['go_autodata']
] ;

FCKConfig.ToolbarSets["ImageManager"] = [
	['Source','DocProps','-','Templates'],
	['Cut','Copy','Paste','PasteText','PasteWord','-','Print','SpellCheck'],
	['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
	['Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript'],
	['OrderedList','UnorderedList','-','Outdent','Indent'],
	['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'],
	['Link','Unlink','Anchor'],
	['ImageManager', 'Table','Rule','SpecialChar','PageBreak','UniversalKey'],
	'/',
	['FontFormat','FontName','FontSize'],
	['TextColor','BGColor'],['go_autodata']
] ;

FCKConfig.ToolbarSets["email"] = [
	['Source','DocProps','-','Templates'],
	['Cut','Copy','Paste','PasteText','PasteWord','-','Print','SpellCheck'],
	['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
	['Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript'],
	['OrderedList','UnorderedList','-','Outdent','Indent'],
	['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'],
	['Link','Unlink','Anchor'],
	['Image','Table','Rule','SpecialChar','PageBreak','UniversalKey'],
	'/',
	['FontFormat','FontName','FontSize'],
	['TextColor','BGColor']
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
FCKConfig.Plugins.Add( 'ImageManager');

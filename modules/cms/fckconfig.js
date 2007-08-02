FCKConfig.SpellChecker			= 'SpellerPages' ;
FCKConfig.SkinPath = FCKConfig.BasePath + 'skins/default/' ;
FCKConfig.LinkBrowser=false;
FCKConfig.LinkUpload=false;
FCKConfig.ImageBrowser=false;
FCKConfig.ImageUpload=false;
FCKConfig.FlashUpload =false;
FCKConfig.IEForceVScroll=true;
FCKConfig.TabSpaces=4;

FCKConfig.ToolbarSets["cms"] = [
	['Source','DocProps','-','Templates'],
	['Cut','Copy','Paste','PasteText','PasteWord','-','Print','SpellCheck'],
	['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
	['Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript'],
	['OrderedList','UnorderedList','-','Outdent','Indent'],
	['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'],
	['Link','Unlink','Anchor'],
	['ImageManager', 'Flash', 'Table','Rule','SpecialChar','PageBreak'],
	'/',
	['Style','FontFormat','FontName','FontSize'],
	['TextColor','BGColor'],
	['FitWindow']
] ;

FCKConfig.ToolbarSets["cms_restricted"] = [
	['Source','DocProps','-','Templates'],
	['Cut','Copy','Paste','PasteText','PasteWord','-','Print','SpellCheck'],
	['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
	['Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript'],
	['OrderedList','UnorderedList','-','Outdent','Indent'],
	['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'],
	['Link', 'Unlink','Anchor'],
	['ImageManager','Flash','Table','Rule','SpecialChar','PageBreak', 'Style','FontFormat'],
	['FitWindow']
] ;

FCKConfig.Plugins.Add( 'ImageManager');
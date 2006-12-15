FCKConfig.SpellChecker			= 'SpellerPages' ;
FCKConfig.SkinPath = FCKConfig.BasePath + 'skins/default/' ;
FCKConfig.LinkBrowser=false;
FCKConfig.LinkUpload=false;
FCKConfig.ImageBrowser=false;
FCKConfig.ImageUpload=false;
FCKConfig.FlashUpload =false;
FCKConfig.UseBROnCarriageReturn=true;
FCKConfig.IEForceVScroll=true;

FCKConfig.ToolbarSets["cms"] = [
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
	['TextColor','BGColor']
] ;
FCKConfig.Plugins.Add( 'ImageManager');

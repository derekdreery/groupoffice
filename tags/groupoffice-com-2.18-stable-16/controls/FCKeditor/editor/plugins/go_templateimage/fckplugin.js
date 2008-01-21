


/* FCKCommands.RegisterCommand(commandName, command)
       commandName - Command name, referenced by the Toolbar, etc...
       command - Command object (must provide an Execute() function).
*/


// Register the related commands.




FCKCommands.RegisterCommand(
   'go_templateimage',
    new FCKPopupCommand(FCKConfig.PluginsPath + 'go_templateimage/select_image.php', 780, 580));


// Create the "Find" toolbar button.
var go_templateimageItem = new FCKToolbarButton('go_templateimage', 'Insert image');
go_templateimageItem.IconPath = FCKConfig.PluginsPath + 'go_templateimage/image.gif' ;

// 'My_Find' is the name used in the Toolbar config.
FCKToolbarItems.RegisterItem( 'go_templateimage', go_templateimageItem ) ;

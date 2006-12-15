<?php
/** 
 *   This file provides the textual interface of WikkiTikkiTavi
 * For more information see http://tavi.sourceforge.net/TaviTranslation
 *
 *   This file is divided into the sections according to subdirectories of 
 * where the constants are used. So that the constant PARSE_RecentChanges are
 * to be found somewhere within the parse-subdirectory.
 *
 *   See http://tavi.sourceforge.net/TaviTranslation for notes on how to 
 * translate the file into another language, and how to publish your changes.
 * Please do contribute to make 'Tavi available in multiple languages. 
 *   The gist of translation, is to copy this file and translate it. Store the
 * resulting work as lang_XX.php, where XX denotes the two characters used to
 * describe the language. And after that to add a line like the following to
 * your config.php:
 *   define(LANGUAGE_CODE, 'fr');
 * Using 'fr' for the french version, if available.
 */

/* General messages */
define('GEN_ErrorSuffix', 
       '<br />Please contact the <a href="mailto:' . 
       $Admin . '">administrator</a> for assistance.');

/* action-directory */
define('ACTION_RateControlIpBlocking', 'Rate control / IP blocking disabled');
define('ACTION_LockUnlockPages', 'Lock / unlock pages');
define('ACTION_BLockUnblockHosts', 'Block / unblock hosts');
define('ACTION_ErrorNameMatch',
       'You have entered an invalid user name.' . GEN_ErrorSuffix);
define('ACTION_ErrorAdminDisabled',
       'Administration features are disabled for this wiki.'.GEN_ErrorSuffix);
define('ACTION_ErrorPageLocked',
       'The page you have tried to edit is locked.' . GEN_ErrorSuffix);

/* lib-directory */
define('LIB_ErrorDatabaseQuery', 
       'Error executing database query.' . GEN_ErrorSuffix);
define('LIB_ErrorDatabaseConnect', 
       'Error connecting to database.' . GEN_ErrorSuffix); 
define('LIB_ErrorDatabaseSelect', 
       'Error selecting database.' . GEN_ErrorSuffix); 
define('LIB_ErrorCreatingTemp', 
       'Error creating temporary file.' . GEN_ErrorSuffix);
define('LIB_ErrorWritingTemp',
       'Error writing to temporary file.' . GEN_ErrorSuffix);
// The next two entries are parsed through the wiki engine, so leave the \'\'
// to get bolded entries... And the escaping of $!DiffCmd...   ;)
define('LIB_NoDiffAvailableSafeMode',
       '\'\'No diff available, due to wrong location of diffcmd. It should '.
       'have been inside '. ini_get("safe_mode_exec_dir") .
       'but is located at '. $DiffCmd .'. Please relocate diffcmd or '.
       'make sysAdmins create symbolic links into this directory. Also '.
       'remember to change configuration of $!DiffCmd accordingly.\'\'');       
define('LIB_NoDiffAvailable',
       '\'\'No diff available. $!DiffCmd points to ' . $DiffCmd .
       ' which doesn\'t exist or isn\'t readable\'\'');
define('LIB_ErrorDeniedAccess',
       'You have been denied access to this site.' . GEN_ErrorSuffix);
define('LIB_ErrorRateExceeded',
       'You have exeeded the number of pages you are allowed to visit in a '.
       'given period of time.  Please return later.' . GEN_ErrorSuffix);
define('LIB_ErrorInvalidPage',
       'Invalid page name.' . GEN_ErrorSuffix);

/* parse-directory */
define('PARSE_Added', 'Added: ');
define('PARSE_Changed', 'Changed: ');
define('PARSE_Deleted', 'Deleted: ');
define('PARSE_Never', 'Never'); 
// The next two entries, are joined with a counting variable inbetween
define('PARSE_CompleteListStart', 'See complete list (');
define('PARSE_CompleteListEnd',   ' entries)');
define('PARSE_RecentChanges', 'RecentChanges');
define('PARSE_Locked', 'Locked');
define('PARSE_BlockedRange', 'Blocked IP address ranges');
define('PARSE_EnterIpRange', 
       'Enter IP address range in form <tt>12.*</tt>, <tt>34.56.*</tt>, or ' .
       '<tt>78.90.123.*</tt>');
define('PARSE_EmptyToBeDeleted',
       ' ** Empty, scheduled for deletion ** ');
define('PARSE_From', 'from'); //ie. SomeThing *from* WantedPages, MacroSomePage
define('PARSE_ButtonButton', 'Block');
define('PARSE_ButtonBlock', 'Unblock');
define('PARSE_ButtonSave', 'Save');
define('PARSE_ButtonPreview', 'Preview');

/* Template directory */
// Note the change to use only TMPL_ as prefix instead of full TEMPLATE_
define('TMPL_Adminstration', 'Administration');
define('TMPL_EditDocument', 'Edit this document');
define('TMPL_NoEditDocument', 'This document can\'t be edited');
define('TMPL_EditArchiveVersion', 
       'Edit this <em>ARCHIVE VERSION</em> of this document');
// Next entry is followed by a date as produced by html_time()
define('TMPL_ViewDocHistory', 'View document history');
define('TMPL_DocLastModified', 'Document last modified');
define('TMPL_TwinPages', 'Twin pages:');
define('TMPL_Search', 'Search:');
define('TMPL_Editing', 'Editing');
define('TMPL_WarningOtherEditing',
       'Warning: since you started editing, this document has been changed '.
       'by someone else.  Please merge your edits into the current version '.
       'of this document.' );
define('TMPL_CurrentVersion', 'Current Version');
define('TMPL_ButtonSave', 'Save');
define('TMPL_ButtonPreview', 'Preview');
define('TMPL_YourUsername', 'Your user name is');
define('TMPL_VisitPrefs', 
       'Visit <a href="'. $PrefsScript. '">Preferences</a> to set '.
       'your user name');
define('TMPL_SummaryOfChange', 'Summary of change:');
define('TMPL_AddToCategory', 'Add document to category:');
define('TMPL_YourChanges', 'Your changes');
define('TMPL_PreviewCurrentVersion', 'Preview of Current Version');
define('TMPL_DifferencesIn', 'Differences In');
define('TMPL_DifferenceBetweenVersions', 'Difference between versions:');
define('TMPL_Find', 'Find');
define('TMPL_HistoryOf', 'History of');
define('TMPL_Older', 'Older');
define('TMPL_Newer', 'Newer');
define('TMPL_ButtonComputeDifference', 'Compute Difference');
define('TMPL_ChangesLastAuthor', 'Changes by last author:');
define('TMPL_Prefs', 'Preferences');
define('TMPL_Previewing', 'Previewing');
define('TMPL_Preview', 'Preview');

?>

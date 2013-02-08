<?php
set_time_limit(0);
require_once('PEAR/PackageFileManager.php');
require_once('PEAR/PackageFileManager2.php');
PEAR::setErrorHandling(PEAR_ERROR_DIE);

$packagedir = dirname(dirname(__FILE__));
$notes = '
Includes these PEAR items:
Req #11755: Remove Unused DocBlock_Lexer and PHPT Tests [ashnazg]
Req #11757: Move PhpUnit Tests into new tests dir [ashnazg]
Bug #10909: PHP Notice in PDF Converter line 210 [ashnazg]
Bug #11613: -q flag is not honoured [ashnazg]
Bug #11683: examples displayed using earthli style are not rendered properly [ashnazg|kguest]
Bug #11788: Two failing unit tests [ashnazg]
Bug #11809: Incorrect @name usage causes fatal crash [ashnazg]
Bug #12121: phpdoc.bat cannot launch php.exe [ashnazg|kalvaro]
Bug #12172: media files are not being copied [ashnazg|nemesis]
Bug #12201: Misleading error message [ashnazg|yunosh]
Doc #12459: @subpackage does not fix name conflicts [ashnazg]

Includes these Sourceforge items:
- [1846302] php version > 4.3.0 and tokenizer [ashnazg]
- [1845584] File suffix should be lowercase [ashnazg]
- [1842595] makedoc.sh doesn\'t like embedded blanks in paths [ashnazg|munroe_r]
- [1839924] addErrorDie has to exit with non-zero return code! [ashnazg|blueyed]
- [1826342] Data-dependent endless(?) loop in parser [ashnazg]
- [1733936] "phpdoc" does not return with error code "0" on success [ashnazg|blueyed]

';
$version = '1.4.1';
$release_stability = 'stable';
$api = '1.4.0';
$api_stability = 'stable';
$options = array(
'baseinstalldir' => 'PhpDocumentor',
'version' => $version,
'packagedirectory' => $packagedir,
'filelistgenerator' => 'cvs',
'notes' => $notes,
'package' => 'PhpDocumentor',
'dir_roles' => array(
    'Documentation' => 'doc',
    'docbuilder' => 'data',
    'HTML_TreeMenu-1.1.2' => 'data',
    'tutorials' => 'doc',
    'tests' => 'test',
    ),
'simpleoutput' => true,
'exceptions' =>
    array(
        'index.html' => 'data',
        'README' => 'doc',
        'ChangeLog' => 'doc',
        'LICENSE' => 'doc',
        'poweredbyphpdoc.gif' => 'data',
        'INSTALL' => 'doc',
        'FAQ' => 'doc',
        'Authors' => 'doc',
        'Release-1.4.1' => 'doc',
        'pear-phpdoc' => 'script',
        'pear-phpdoc.bat' => 'script',
        'HTML_TreeMenu-1.1.2/TreeMenu.php' => 'php',
        'phpDocumentor/Smarty-2.6.0/libs/debug.tpl' => 'php',
        'new_phpdoc.php' => 'data',
        'phpdoc.php' => 'data',
        ),
'ignore' =>
    array('package.xml',
          '*templates/PEAR/*',
          ),
'installexceptions' => array('pear-phpdoc' => '/', 'pear-phpdoc.bat' => '/', 'scripts/makedoc.sh' => '/'),
);
$pfm2 = PEAR_PackageFileManager2::importOptions(dirname(dirname(__FILE__))
    . DIRECTORY_SEPARATOR . 'package.xml', array_merge($options, array('packagefile' => 'package.xml')));
$pfm2->setReleaseVersion($version);
$pfm2->setReleaseStability($release_stability);
$pfm2->setAPIVersion($api);
$pfm2->setAPIStability($api_stability);
$pfm2->setLicense('LGPL', 'http://www.opensource.org/licenses/lgpl-license.php');
$pfm2->setNotes($notes);
$pfm2->clearDeps();
$pfm2->setPhpDep('4.2.0');
$pfm2->setPearinstallerDep('1.4.6');
$pfm2->addPackageDepWithChannel('optional', 'XML_Beautifier', 'pear.php.net', '1.1');
$pfm2->addReplacement('pear-phpdoc', 'pear-config', '@PHP-BIN@', 'php_bin');
$pfm2->addReplacement('pear-phpdoc.bat', 'pear-config', '@PHP-BIN@', 'php_bin');
$pfm2->addReplacement('pear-phpdoc.bat', 'pear-config', '@BIN-DIR@', 'bin_dir');
$pfm2->addReplacement('pear-phpdoc.bat', 'pear-config', '@PEAR-DIR@', 'php_dir');
$pfm2->addReplacement('pear-phpdoc.bat', 'pear-config', '@DATA-DIR@', 'data_dir');
$pfm2->addReplacement('README', 'package-info', '@VER@', 'version');
$pfm2->addReplacement('README', 'package-info', '@STABILITY@', 'state');
$pfm2->addReplacement('docbuilder/includes/utilities.php', 'pear-config', '@DATA-DIR@', 'data_dir');
$pfm2->addReplacement('docbuilder/builder.php', 'pear-config', '@DATA-DIR@', 'data_dir');
$pfm2->addReplacement('docbuilder/file_dialog.php', 'pear-config', '@DATA-DIR@', 'data_dir');
$pfm2->addReplacement('docbuilder/file_dialog.php', 'pear-config', '@WEB-DIR@', 'data_dir');
$pfm2->addReplacement('docbuilder/actions.php', 'pear-config', '@WEB-DIR@', 'data_dir');
$pfm2->addReplacement('docbuilder/top.php', 'pear-config', '@DATA-DIR@', 'data_dir');
$pfm2->addReplacement('docbuilder/config.php', 'pear-config', '@DATA-DIR@', 'data_dir');
$pfm2->addReplacement('docbuilder/config.php', 'pear-config', '@WEB-DIR@', 'data_dir');
$pfm2->addReplacement('phpDocumentor/Setup.inc.php', 'pear-config', '@DATA-DIR@', 'data_dir');
$pfm2->addReplacement('phpDocumentor/Converter.inc', 'pear-config', '@DATA-DIR@', 'data_dir');
$pfm2->addReplacement('phpDocumentor/Classes.inc', 'package-info', '@VER@', 'version');
$pfm2->addReplacement('phpDocumentor/DescHTML.inc', 'package-info', '@VER@', 'version');
$pfm2->addReplacement('phpDocumentor/Errors.inc', 'package-info', '@VER@', 'version');
$pfm2->addReplacement('phpDocumentor/EventStack.inc', 'package-info', '@VER@', 'version');
$pfm2->addReplacement('phpDocumentor/common.inc.php', 'package-info', '@VER@', 'version');
$pfm2->addReplacement('phpDocumentor/common.inc.php', 'pear-config', '@PEAR-DIR@', 'php_dir');
$pfm2->addReplacement('phpDocumentor/HighlightParser.inc', 'package-info', '@VER@', 'version');
$pfm2->addReplacement('phpDocumentor/IntermediateParser.inc', 'package-info', '@VER@', 'version');
$pfm2->addReplacement('phpDocumentor/IntermediateParser.inc', 'pear-config', '@PEAR-DIR@', 'php_dir');
$pfm2->addReplacement('phpDocumentor/phpDocumentorTParser.inc', 'package-info', '@VER@', 'version');
$pfm2->addReplacement('phpDocumentor/phpDocumentorTWordParser.inc', 'package-info', '@VER@', 'version');
$pfm2->addReplacement('phpDocumentor/ProceduralPages.inc', 'package-info', '@VER@', 'version');
$pfm2->addReplacement('phpDocumentor/Publisher.inc', 'package-info', '@VER@', 'version');
$pfm2->addReplacement('phpDocumentor/TutorialHighlightParser.inc', 'package-info', '@VER@', 'version');
$pfm2->addReplacement('phpDocumentor/WordParser.inc', 'package-info', '@VER@', 'version');
$pfm2->addReplacement('phpDocumentor/XMLpackagePageParser.inc', 'package-info', '@VER@', 'version');
$pfm2->addReplacement('user/pear-makedocs.ini', 'pear-config', '@PEAR-DIR@', 'php_dir');
$pfm2->addReplacement('user/pear-makedocs.ini', 'pear-config', '@DOC-DIR@', 'doc_dir');
$pfm2->addReplacement('user/pear-makedocs.ini', 'package-info', '@VER@', 'version');
$pfm2->addRole('inc', 'php');
$pfm2->addRole('sh', 'script');
$pfm2->addUnixEol('pear-phpdoc');
$pfm2->addUnixEol('phpdoc');
$pfm2->addWindowsEol('pear-phpdoc.bat');
$pfm2->addWindowsEol('phpdoc.bat');
$pfm2->generateContents();
$pfm2->setPackageType('php');
$pfm2->addRelease();
$pfm2->setOsInstallCondition('windows');
// these next few files are only used if the archive is extracted as-is
// without installing via "pear install blah"
$pfm2->addIgnoreToRelease("phpdoc");
$pfm2->addIgnoreToRelease('phpdoc.bat');
$pfm2->addIgnoreToRelease('user/makedocs.ini');
$pfm2->addIgnoreToRelease('scripts/makedoc.sh');
$pfm2->addInstallAs('pear-phpdoc', 'phpdoc');
$pfm2->addInstallAs('pear-phpdoc.bat', 'phpdoc.bat');
$pfm2->addInstallAs('user/pear-makedocs.ini', 'user/makedocs.ini');
$pfm2->addRelease();
// these next two files are only used if the archive is extracted as-is
// without installing via "pear install blah"
$pfm2->addIgnoreToRelease("phpdoc");
$pfm2->addIgnoreToRelease('phpdoc.bat');
$pfm2->addIgnoreToRelease('user/makedocs.ini');
$pfm2->addIgnoreToRelease('pear-phpdoc.bat');
$pfm2->addInstallAs('pear-phpdoc', 'phpdoc');
$pfm2->addInstallAs('user/pear-makedocs.ini', 'user/makedocs.ini');
if (isset($_GET['make']) || (isset($_SERVER['argv'][1]) && $_SERVER['argv'][1] == 'make')) {
    $pfm2->writePackageFile();
} else {
    $pfm2->debugPackageFile();
}
if (!isset($_GET['make']) && !(isset($_SERVER['argv'][1]) && $_SERVER['argv'][1] == 'make')) {
    echo '<a href="' . $_SERVER['PHP_SELF'] . '?make=1">Make this file</a>';
}
?>

<?php
/**
 * MediaWiki port of the WordPress theme Bouquet
 *
 * Bouquet is an elegant, simple theme inspired by the beauty found in flowers.
 * Notable features include two floral schemes, a responsive layout structure
 * that adapts to smaller devices, a right sidebar, a full-width template,
 * support for post formats, custom background, and custom header.
 *
 * @file
 * @ingroup Skins
 * @author Automattic
 * @author Jack Phoenix <jack@countervandalism.net> -- MediaWiki port
 * @date 16 February 2014
 *
 * To install, place the Bouquet folder (the folder containing this file!) into
 * skins/ and add this line to your wiki's LocalSettings.php:
 * require_once("$IP/skins/Bouquet/Bouquet.php");
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not a valid entry point.' );
}

// Skin credits that will show up on Special:Version
$wgExtensionCredits['skin'][] = array(
	'path' => __FILE__,
	'name' => 'Bouquet',
	'version' => '1.2.1',
	'author' => array( '[http://automattic.com Automattic]', 'Jack Phoenix' ),
	'description' => 'Elegant, simple theme inspired by the beauty found in flowers',
	'url' => 'https://www.mediawiki.org/wiki/Skin:Bouquet',
);

// The first instance must be strtolower()ed so that useskin=bouquet works and
// so that it does *not* force an initial capital (i.e. we do NOT want
// useskin=Bouquet) and the second instance is used to determine the name of
// *this* file.
$wgValidSkinNames['bouquet'] = 'Bouquet';

// Autoload the skin class, make it a valid skin, set up i18n, set up CSS & JS
// (via ResourceLoader)
$wgAutoloadClasses['SkinBouquet'] = __DIR__ . '/Bouquet.skin.php';
$wgMessagesDirs['SkinBouquet'] = __DIR__ . '/i18n';
$wgResourceModules['skins.bouquet'] = array(
	'styles' => array(
		// MonoBook also loads these
		'skins/common/commonElements.css' => array( 'media' => 'screen' ),
		'skins/common/commonContent.css' => array( 'media' => 'screen' ),
		'skins/common/commonInterface.css' => array( 'media' => 'screen' ),
		// Styles custom to the Bouquet skin
		'skins/Bouquet/resources/print.css' => array( 'media' => 'print' ),
		'skins/Bouquet/resources/style.css' => array( 'media' => 'screen' )
	),
	'position' => 'top'
);

// JS module
$wgResourceModules['skins.bouquet.js'] = array(
	'scripts' => '/skins/Bouquet/resources/js/navigation.js'
);

// Themes
$wgResourceModules['themeloader.skins.bouquet.forgetmenot'] = array(
	'styles' => array(
		'skins/Bouquet/resources/colors/forget-me-not/forget-me-not.css' => array( 'media' => 'screen' )
	)
);

$wgResourceModules['themeloader.skins.bouquet.pinkdogwood'] = array(
	'styles' => array(
		'skins/Bouquet/resources/colors/pink-dogwood/pink-dogwood.css' => array( 'media' => 'screen' )
	)
);

$wgResourceModules['themeloader.skins.bouquet.tigerlily'] = array(
	'styles' => array(
		'skins/Bouquet/resources/colors/tiger-lily/tiger-lily.css' => array( 'media' => 'screen' )
	)
);
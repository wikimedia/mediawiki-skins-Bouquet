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
 * @date 30 November 2014
 *
 * To install, place the Bouquet folder (the folder containing this file!) into
 * skins/ and add this line to your wiki's LocalSettings.php:
 * wfLoadSkin( 'Bouquet' );
 */

 if ( function_exists( 'wfLoadSkin' ) ) {
	wfLoadSkin( 'Bouquet' );
	// Keep i18n globals so mergeMessageFileList.php doesn't break
	$wgMessagesDirs['Bouquet'] = __DIR__ . '/i18n';
	wfWarn(
		'Deprecated PHP entry point used for Bouquet skin. Please use wfLoadSkin instead, ' .
		'see https://www.mediawiki.org/wiki/Extension_registration for more details.'
	);
	return;
} else {
	die( 'This version of the Bouquet skin requires MediaWiki 1.25+' );
}

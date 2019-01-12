<?php

/**
 * Inherit main code from SkinTemplate, set the CSS and template filter.
 *
 * @ingroup Skins
 */
class SkinBouquet extends SkinTemplate {
	public $skinname = 'bouquet', $stylename = 'bouquet',
		$template = 'BouquetTemplate';

	/**
	 * Load the skin's JS via ResourceLoader.
	 *
	 * @param $out OutputPage
	 */
	public function initPage( OutputPage $out ) {
		parent::initPage( $out );

		// Add JS
		$out->addModules( 'skins.bouquet.js' );
	}

	/**
	 * @param $out OutputPage
	 */
	function setupSkinUserCss( OutputPage $out ) {
		parent::setupSkinUserCss( $out );

		// Load CSS via ResourceLoader
		$out->addModuleStyles( [
			'mediawiki.skinning.interface',
			'mediawiki.skinning.content.externallinks',
			'skins.bouquet'
		] );
	}
}
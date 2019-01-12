<?php

class BouquetTemplate extends BaseTemplate {

	/**
	 * Get the site logo, either a customized one or the current theme's default
	 * one.
	 *
	 * @return string Logo image URL
	 */
	private function getLogo() {
		global $wgStylePath, $wgDefaultTheme;

		$logoURL = '';
		$customLogo = wfFindFile( 'Bouquet-skin-logo.png' );

		if ( is_object( $customLogo ) ) {
			// Prefer a custom logo over the defaults (obviously!)
			$logoURL = $customLogo->getUrl();
		} else {
			$logoURL = "{$wgStylePath}/Bouquet/resources/images/pink-header.png";
			// $wgDefaultTheme is provided by [[mw:Extension:Theme]] and it is set
			// by default at least on ShoutWiki, but probably not elsewhere
			// Grumble grumble grumble...
			// Pasta from /extensions/Theme/Theme.php
			$themeFromRequest = $this->getSkin()->getRequest()->getVal( 'usetheme', false );
			if ( $themeFromRequest ) {
				$themeName = $themeFromRequest;
			} elseif ( isset( $wgDefaultTheme ) && $wgDefaultTheme != 'default' ) {
				$themeName = $wgDefaultTheme;
			} else {
				$themeName = false;
			}

			// internal (hyphenless) theme name -> directory/logo file mapping
			// yes, this is lame as hell and I should just rename the dirs & files instead
			$map = [
				'forgetmenot' => 'forget-me-not',
				'pinkdogwood' => 'pink-dogwood',
				'tigerlily' => 'tiger-lily',
			];

			if ( isset( $map[$themeName] ) && $map[$themeName] ) {
				$logoURL = "{$wgStylePath}/Bouquet/resources/colors/{$map[$themeName]}/{$map[$themeName]}-header.png";
			}
		}

		return $logoURL;
	}

	/**
	 * Template filter callback for the Bouquet skin.
	 * Takes an associative array of data set from a SkinTemplate-based
	 * class, and a wrapper for MediaWiki's localization database, and
	 * outputs a formatted page.
	 */
	public function execute() {
		global $wgSitename;

		$this->data['pageLanguage'] = $this->getSkin()->getTitle()->getPageViewLanguage()->getHtmlCode();

		$this->html( 'headelement' );
?>
<div id="page" class="hfeed">
	<header id="branding" role="banner" class="clearfix" style="background-image: url(<?php echo $this->getLogo(); ?>);">
		<?php echo Html::element( 'a', [
				'href' => $this->data['nav_urls']['mainpage']['href'],
				'class' => 'header-link',
				'title' => $wgSitename,
				'rel' => 'home'
			] + Linker::tooltipAndAccesskeyAttribs( 'p-logo' ) ); ?>
		<div>
			<h1 class="firstHeading" id="site-title"><span dir="auto"><?php echo Html::element( 'a', [
							'href' => $this->data['nav_urls']['mainpage']['href'] ]
							+ Linker::tooltipAndAccesskeyAttribs( 'p-logo' ), $wgSitename ); ?></span></h1>
			<h2 id="site-description"><?php $this->msg( 'tagline' ) ?></h2>
		</div>
	</header><!-- #branding -->

	<div id="main">
		<div id="primary">
			<nav id="access" class="clearfix noprint" role="navigation">
				<h1 class="assistive-text section-heading">Main menu</h1>
				<div id="jump-to-nav" class="mw-jump skip-link screen-reader-text"><?php $this->msg( 'jumpto' ) ?> <a href="#content"><?php $this->msg( 'jumptonavigation' ) ?></a><?php $this->msg( 'comma-separator' ) ?><a href="#searchInput"><?php $this->msg( 'jumptosearch' ) ?></a></div>
				<div class="menu">
					<ul>
						<?php
							$service = new BouquetSkinNavigationService();
							$menuNodes = $service->parseMessage(
								'bouquet-navigation',
								[ 10, 10, 10, 10, 10, 10 ],
								60 * 60 * 3 // 3 hours
							);

							if ( is_array( $menuNodes ) && isset( $menuNodes[0] ) ) {
								$i = $counter = 0;
								foreach ( $menuNodes[0]['children'] as $level0 ) {
									$hasChildren = isset( $menuNodes[$level0]['children'] );
							?>
					<li class="page_item<?php echo ( $hasChildren ? ' page_item_has_children' : '' ) ?>">
						<a class="nav<?php echo $counter ?>_link" href="<?php echo htmlspecialchars( $menuNodes[$level0]['href'], ENT_QUOTES ) ?>"><?php echo htmlspecialchars( $menuNodes[$level0]['text'], ENT_QUOTES ) ?></a>
							<?php if ( $hasChildren ) { ?>
							<ul class="children">
<?php
									$asdf = 0;
									$allKidNodes = count( $menuNodes[$level0]['children'] );
									foreach ( $menuNodes[$level0]['children'] as $level1 ) {
?>
							<li class="page_item">
								<a href="<?php echo htmlspecialchars( $menuNodes[$level1]['href'], ENT_QUOTES ) ?>"><?php echo htmlspecialchars( $menuNodes[$level1]['text'], ENT_QUOTES ) ?></a>
							</li>
<?php
										}
										echo '</ul>';
										$counter++;
									}
									echo '</li>';
								}
							}
?>
					</ul>
				</div>
			</nav><!-- #access -->
			<div id="content-wrapper">
				<div id="content" role="main">
					<article class="post hentry">
						<?php if ( $this->data['sitenotice'] ) { ?><div id="siteNotice"><?php $this->html( 'sitenotice' ) ?></div><?php } ?>
						<header class="entry-header">
							<h1 id="firstHeading" class="firstHeading entry-title" lang="<?php $this->text( 'pageLanguage' ); ?>"><?php $this->html( 'title' ) ?></h1>
							<?php if ( $this->data['undelete'] ) { ?><div id="contentSub2"><?php $this->html( 'undelete' ) ?></div><?php } ?>
						</header><!-- .entry-header -->

						<div class="entry-content mw-body-content">
							<?php if ( $this->data['newtalk'] ) { ?><div class="usermessage"><?php $this->html( 'newtalk' ) ?></div><?php } ?>
							<!-- start content -->
							<?php
							$this->html( 'bodytext' );
							if ( $this->data['catlinks'] ) {
								$this->html( 'catlinks' );
							}
							?>
							<!-- end content -->
							<?php
							if ( $this->data['dataAfterContent'] ) {
								$this->html( 'dataAfterContent' );
							}
							?>
						</div><!-- .entry-content -->
					</article>

					<nav id="nav-below" class="noprint">
						<h1 class="assistive-text section-heading"><?php $this->msg( 'navigation' ) ?></h1>
					</nav><!-- #nav-below -->
				</div><!-- #content -->
			</div><!-- #content-wrapper -->
		</div><!-- #primary -->

		<div id="secondary-wrapper" class="noprint">
			<div id="search-area">
				<form role="search" method="get" id="searchformTop" class="searchform" action="<?php $this->text( 'wgScript' ) ?>">
					<div>
						<label class="screen-reader-text" for="searchInput"><?php $this->msg( 'search' ) ?></label>
						<input type="hidden" name="title" value="<?php $this->text( 'searchtitle' ) ?>"/>
						<?php
						echo $this->makeSearchInput( [ 'id' => 'searchInputTop', 'class' => 'mw-searchInput' ] );
						echo $this->makeSearchButton( 'go', [ 'id' => 'searchGoButtonTop', 'class' => 'searchButton', 'style' => 'display: none' ] );
						?>
						<div><a href="<?php $this->text( 'searchaction' ) ?>" rel="search"><?php $this->msg( 'powersearch-legend' ) ?></a></div>
					</div>
				</form>
			</div>
			<div id="secondary" class="widget-area" role="complementary">
				<?php
					$this->renderPersonalTools();
					$this->cactions();
					$this->renderPortals( $this->data['sidebar'] );
				?>
		</div><!-- #secondary .widget-area -->
	</div><!-- #secondary-wrapper -->

	</div><!-- #main -->
</div><!-- #page -->

		<?php
		$validFooterIcons = $this->getFooterIcons( 'icononly' );
		$validFooterLinks = $this->getFooterLinks( 'flat' ); // Additional footer links

		if ( count( $validFooterIcons ) + count( $validFooterLinks ) > 0 ) { ?>
<footer id="colophon" role="contentinfo"<?php $this->html( 'userlangattributes' ) ?>>
<?php
			$footerEnd = '</footer><!-- #colophon -->';
		} else {
			$footerEnd = '';
		}

		echo '<div id="site-generator-wrapper">';

		// @todo FIXME/CHECKME
		foreach ( $validFooterIcons as $blockName => $footerIcons ) { ?>
	<div id="f-<?php echo htmlspecialchars( $blockName ); ?>ico">
<?php
			foreach ( $footerIcons as $icon ) {
				echo $this->getSkin()->makeFooterIcon( $icon );
			}
?>
	</div>
<?php
		}

		$i = 0;
		$footerLen = count( $validFooterLinks );
		if ( $footerLen > 0 ) {
			echo '<div id="site-generator">';
			foreach ( $validFooterLinks as $aLink ) {
				$this->html( $aLink );
				// Output the separator for all items, save for the
				// last one
				if ( $i !== ( $footerLen - 1 ) ) {
					echo '<span class="sep"> | </span>';
				}
				$i++;
			}
			echo '</div><!-- #site-generator -->';
		}

		echo '</div><!-- #site-generator-wrapper -->';

		echo $footerEnd;

		$this->printTrail();
		echo Html::closeElement( 'body' );
		echo Html::closeElement( 'html' );
	} // end of execute() method

	/**
	 * @param $sidebar array
	 */
	protected function renderPortals( $sidebar ) {
		if ( !isset( $sidebar['SEARCH'] ) ) {
			$sidebar['SEARCH'] = true;
		}
		if ( !isset( $sidebar['TOOLBOX'] ) ) {
			$sidebar['TOOLBOX'] = true;
		}
		if ( !isset( $sidebar['LANGUAGES'] ) ) {
			$sidebar['LANGUAGES'] = true;
		}

		foreach ( $sidebar as $boxName => $content ) {
			if ( $content === false ) {
				continue;
			}

			if ( $boxName == 'SEARCH' ) {
				$this->searchBox();
			} elseif ( $boxName == 'TOOLBOX' ) {
				$this->toolbox();
			} elseif ( $boxName == 'LANGUAGES' ) {
				$this->languageBox();
			} else {
				$this->customBox( $boxName, $content );
			}
		}
	}

	function renderPersonalTools() {
		$this->customBox( 'personal', $this->getPersonalTools() );
	}

	function searchBox() {
		global $wgUseTwoButtonsSearchForm;
?>
						<aside id="search-3" class="widget widget_search">
							<form role="search" method="get" id="searchform" class="searchform" action="<?php $this->text( 'wgScript' ) ?>">
								<div>
									<label class="screen-reader-text" for="searchInput"><?php $this->msg( 'search' ) ?></label>
									<input type="hidden" name="title" value="<?php $this->text( 'searchtitle' ) ?>"/>
									<?php
										echo $this->makeSearchInput( [ 'id' => 'searchInput' ] );
										echo $this->makeSearchButton( 'go', [ 'id' => 'searchGoButton', 'class' => 'searchButton' ] );
										if ( $wgUseTwoButtonsSearchForm ) {
											echo '&#160;';
											echo $this->makeSearchButton( 'fulltext', [ 'id' => 'mw-searchButton', 'class' => 'searchButton' ] );
										} else { ?>
											<div><a href="<?php $this->text( 'searchaction' ) ?>" rel="search"><?php $this->msg( 'powersearch-legend' ) ?></a></div><?php
										} ?>
								</div>
							</form>
						</aside>
<?php
	}

	/**
	 * Prints the content actions bar.
	 */
	function cactions() {
?>
	<aside id="p-cactions" class="portlet widget" role="navigation">
		<h1 class="widget-title"><?php $this->msg( 'views' ) ?></h1>
			<ul><?php
				foreach ( $this->data['content_actions'] as $key => $tab ) {
					echo '
				' . $this->makeListItem( $key, $tab );
				} ?>
			</ul>
	</aside>
<?php
	}

	function toolbox() {
?>
	<aside class="portlet widget" id="p-tb" role="navigation">
		<h1 class="widget-title"><?php $this->msg( 'toolbox' ) ?></h1>
		<ul>
<?php
		foreach ( $this->getToolbox() as $key => $tbItem ) {
			echo $this->makeListItem( $key, $tbItem );
		}

		// Avoid PHP 7.1 warning of passing $this by reference
		$template = $this;
		Hooks::run( 'SkinTemplateToolboxEnd', [ &$template, true ] );
?>
		</ul>
	</aside>
<?php
	}

	function languageBox() {
		if ( $this->data['language_urls'] ) {
?>
	<aside id="p-lang" class="portlet widget" role="navigation">
		<h1 class="widget-title"<?php $this->html( 'userlangattributes' ) ?>><?php $this->msg( 'otherlanguages' ) ?></h1>
		<ul>
<?php		foreach ( $this->data['language_urls'] as $key => $langLink ) {
				echo $this->makeListItem( $key, $langLink );
			}
?>
		</ul>
	</aside>
<?php
		}
	}

	/**
	 * Render a sidebar box from user-supplied data (a portion of MediaWiki:Sidebar)
	 *
	 * @param string $bar
	 * @param array|string $cont
	 */
	function customBox( $bar, $cont ) {
		$portletAttribs = [
			'class' => 'generated-sidebar widget',
			'id' => Sanitizer::escapeIdForAttribute( "p-$bar" ),
			'role' => 'navigation'
		];
		$tooltip = Linker::titleAttrib( "p-$bar" );
		if ( $tooltip !== false ) {
			$portletAttribs['title'] = $tooltip;
		}
		echo '	' . Html::openElement( 'aside', $portletAttribs );

		$msgObj = wfMessage( $bar );
?>

		<h1 class="widget-title"><?php echo htmlspecialchars( $msgObj->exists() ? $msgObj->text() : $bar ); ?></h1>
<?php	if ( is_array( $cont ) ) { ?>
			<ul>
<?php 			foreach ( $cont as $key => $val ) {
					echo $this->makeListItem( $key, $val );
				}
?>
			</ul>
<?php	} else {
			// allow raw HTML block to be defined by extensions (such as NewsBox)
			echo $cont;
		}
?>
	</aside>
<?php
	}
}



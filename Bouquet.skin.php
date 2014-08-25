<?php
if ( !defined( 'MEDIAWIKI' ) ) {
	die( -1 );
}

/**
 * Inherit main code from SkinTemplate, set the CSS and template filter.
 *
 * @ingroup Skins
 */
class SkinBouquet extends SkinTemplate {
	var $skinname = 'bouquet', $stylename = 'bouquet',
		$template = 'BouquetTemplate', $useHeadElement = true;

	/**
	 * Load the skin's JS via ResourceLoader.
	 *
	 * @param $out OutputPage
	 */
	public function initPage( OutputPage $out ) {
		global $wgStylePath;

		parent::initPage( $out );

		// HTML5 shim has to be loaded this way for older IEs...
		$out->addHeadItem( 'html5shim',
			'<!--[if lt IE 9]>' .
			Html::element( 'script', array(
				'src' => $wgStylePath . '/Bouquet/resources/js/html5.js',
				'type' => 'text/javascript'
			) ) . '<![endif]-->'
		);

		// Add JS
		$out->addModules( 'skins.bouquet.js' );
	}

	/**
	 * @param $out OutputPage
	 */
	function setupSkinUserCss( OutputPage $out ) {
		parent::setupSkinUserCss( $out );

		// Load CSS via ResourceLoader
		$out->addModuleStyles( 'skins.bouquet' );
	}
}

class BouquetTemplate extends BaseTemplate {

	/**
	 * Get the site logo, either a customized one or the current theme's default
	 * one.
	 *
	 * @return String: logo image URL
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
			$map = array(
				'forgetmenot' => 'forget-me-not',
				'pinkdogwood' => 'pink-dogwood',
				'tigerlily' => 'tiger-lily',
			);

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
		<?php echo Html::element( 'a', array(
				'href' => $this->data['nav_urls']['mainpage']['href'],
				'class' => 'header-link',
				'title' => $wgSitename,
				'rel' => 'home'
			) + Linker::tooltipAndAccesskeyAttribs( 'p-logo' ) ); ?>
		<div>
			<h1 class="firstHeading" id="site-title"><span dir="auto"><?php echo Html::element( 'a', array(
							'href' => $this->data['nav_urls']['mainpage']['href'] )
							+ Linker::tooltipAndAccesskeyAttribs( 'p-logo' ), $wgSitename ); ?></span></h1>
			<h2 id="site-description"><?php $this->msg( 'tagline' ) ?></h2>
		</div>
	</header><!-- #branding -->

	<div id="main">
		<div id="primary">
			<nav id="access" class="noprint" role="navigation" class="clearfix">
				<h1 class="assistive-text section-heading">Main menu</h1>
				<div id="jump-to-nav" class="mw-jump skip-link screen-reader-text"><?php $this->msg( 'jumpto' ) ?> <a href="#content"><?php $this->msg( 'jumptonavigation' ) ?></a><?php $this->msg( 'comma-separator' ) ?><a href="#searchInput"><?php $this->msg( 'jumptosearch' ) ?></a></div>
				<div class="menu">
					<ul>
						<?php
							$service = new BouquetSkinNavigationService();
							$menuNodes = $service->parseMessage(
								'bouquet-navigation',
								array( 10, 10, 10, 10, 10, 10 ),
								60 * 60 * 3 // 3 hours
							);

							if ( is_array( $menuNodes ) && isset( $menuNodes[0] ) ) {
								$i = $counter = 0;
								foreach ( $menuNodes[0]['children'] as $level0 ) {
									$hasChildren = isset( $menuNodes[$level0]['children'] );
							?>
					<li class="page_item<?php echo ( $hasChildren ? ' page_item_has_children' : '' ) ?>">
						<a class="nav<?php echo $counter ?>_link" href="<?php echo $menuNodes[$level0]['href'] ?>"><?php echo $menuNodes[$level0]['text'] ?></a>
							<?php if ( $hasChildren ) { ?>
							<ul class="children">
<?php
									$asdf = 0;
									$allKidNodes = count( $menuNodes[$level0]['children'] );
									foreach ( $menuNodes[$level0]['children'] as $level1 ) {
?>
							<li class="page_item">
								<a href="<?php echo $menuNodes[$level1]['href'] ?>"><?php echo $menuNodes[$level1]['text'] ?></a>
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
						<header class="entry-header">
							<h1 id="firstHeading" class="firstHeading entry-title" lang="<?php $this->text( 'pageLanguage' ); ?>"><span dir="auto"><?php $this->html( 'title' ) ?></span></h1>
							<?php if ( $this->data['undelete'] ) { ?><div id="contentSub2"><?php $this->html( 'undelete' ) ?></div><?php } ?>
						</header><!-- .entry-header -->

						<div class="entry-content mw-body">
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
						echo $this->makeSearchInput( array( 'id' => 'searchInputTop', 'class' => 'mw-searchInput' ) );
						echo $this->makeSearchButton( 'go', array( 'id' => 'searchGoButtonTop', 'class' => 'searchButton', 'style' => 'display: none' ) );
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
										echo $this->makeSearchInput( array( 'id' => 'searchInput' ) );
										echo $this->makeSearchButton( 'go', array( 'id' => 'searchGoButton', 'class' => 'searchButton' ) );
										if ( $wgUseTwoButtonsSearchForm ) {
											echo '&#160;';
											echo $this->makeSearchButton( 'fulltext', array( 'id' => 'mw-searchButton', 'class' => 'searchButton' ) );
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
		wfRunHooks( 'MonoBookTemplateToolboxEnd', array( &$this ) );
		wfRunHooks( 'SkinTemplateToolboxEnd', array( &$this, true ) );
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
	 * @param $bar string
	 * @param $cont array|string
	 */
	function customBox( $bar, $cont ) {
		$portletAttribs = array(
			'class' => 'generated-sidebar widget',
			'id' => Sanitizer::escapeId( "p-$bar" ),
			'role' => 'navigation'
		);
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

/**
 * A fork of Oasis' NavigationService with some changes.
 * Namely the name was changed and "magic word" handling was removed from
 * parseMessage() and some (related) unused functions were also removed.
 */
class BouquetSkinNavigationService {

	const version = '0.01';

	/**
	 * Parses a system message by exploding along newlines.
	 *
	 * @param $messageName String: name of the MediaWiki message to parse
	 * @param $maxChildrenAtLevel Array:
	 * @param $duration Integer: cache duration for memcached calls
	 * @param $forContent Boolean: is the message we're supposed to parse in
	 *								the wiki's content language (true) or not?
	 * @return Array
	 */
	public function parseMessage( $messageName, $maxChildrenAtLevel = array(), $duration, $forContent = false ) {
		wfProfileIn( __METHOD__ );

		global $wgLang, $wgContLang, $wgMemc;

		$this->forContent = $forContent;

		$useCache = $wgLang->getCode() == $wgContLang->getCode();

		if ( $useCache || $this->forContent ) {
			$cacheKey = wfMemcKey( $messageName, self::version );
			$nodes = $wgMemc->get( $cacheKey );
		}

		if ( empty( $nodes ) ) {
			if ( $this->forContent ) {
				$lines = explode( "\n", wfMessage( $messageName )->inContentLanguage()->text() );
			} else {
				$lines = explode( "\n", wfMessage( $messageName )->text() );
			}
			$nodes = $this->parseLines( $lines, $maxChildrenAtLevel );

			if ( $useCache || $this->forContent ) {
				$wgMemc->set( $cacheKey, $nodes, $duration );
			}
		}

		wfProfileOut( __METHOD__ );
		return $nodes;
	}

	/**
	 * Function used by parseMessage() above.
	 *
	 * @param $lines String: newline-separated lines from the supplied MW: msg
	 * @param $maxChildrenAtLevel Array:
	 * @return Array
	 */
	private function parseLines( $lines, $maxChildrenAtLevel = array() ) {
		wfProfileIn( __METHOD__ );

		$nodes = array();

		if ( is_array( $lines ) && count( $lines ) > 0 ) {
			$lastDepth = 0;
			$i = 0;
			$lastSkip = null;

			foreach ( $lines as $line ) {
				// we are interested only in lines that are not empty and start with asterisk
				if ( trim( $line ) != '' && $line{0} == '*' ) {
					$depth = strrpos( $line, '*' ) + 1;

					if ( $lastSkip !== null && $depth >= $lastSkip ) {
						continue;
					} else {
						$lastSkip = null;
					}

					if ( $depth == $lastDepth + 1 ) {
						$parentIndex = $i;
					} elseif ( $depth == $lastDepth ) {
						$parentIndex = $nodes[$i]['parentIndex'];
					} else {
						for ( $x = $i; $x >= 0; $x-- ) {
							if ( $x == 0 ) {
								$parentIndex = 0;
								break;
							}
							if ( $nodes[$x]['depth'] <= $depth - 1 ) {
								$parentIndex = $x;
								break;
							}
						}
					}

					if ( isset( $maxChildrenAtLevel[$depth - 1] ) ) {
						if ( isset( $nodes[$parentIndex]['children'] ) ) {
							if ( count( $nodes[$parentIndex]['children'] ) >= $maxChildrenAtLevel[$depth - 1] ) {
								$lastSkip = $depth;
								continue;
							}
						}
					}

					$node = $this->parseOneLine( $line );
					$node['parentIndex'] = $parentIndex;
					$node['depth'] = $depth;

					$nodes[$node['parentIndex']]['children'][] = $i + 1;
					$nodes[$i + 1] = $node;
					$lastDepth = $node['depth'];
					$i++;
				}
			}
		}

		wfProfileOut( __METHOD__ );
		return $nodes;
	}

	/**
	 * @param $line String: line to parse
	 * @return Array
	 */
	private function parseOneLine( $line ) {
		wfProfileIn( __METHOD__ );

		// trim spaces and asterisks from line and then split it to maximum two chunks
		$lineArr = explode( '|', trim( $line, '* ' ), 2 );

		// trim [ and ] from line to have just http://en.wikipedia.org instead of [http://en.wikipedia.org] for external links
		$lineArr[0] = trim( $lineArr[0], '[]' );

		if ( count( $lineArr ) == 2 && $lineArr[1] != '' ) {
			$link = trim( wfMessage( $lineArr[0] )->inContentLanguage()->text() );
			$desc = trim( $lineArr[1] );
		} else {
			$link = $desc = trim( $lineArr[0] );
		}

		$text = $this->forContent ? wfMessage( $desc )->inContentLanguage() : wfMessage( $desc );
		if ( $text->isDisabled() ) {
			$text = $desc;
		}

		if ( wfMessage( $lineArr[0] )->isDisabled() ) {
			$link = $lineArr[0];
		}

		if ( preg_match( '/^(?:' . wfUrlProtocols() . ')/', $link ) ) {
			$href = $link;
		} else {
			if ( empty( $link ) ) {
				$href = '#';
			} elseif ( $link{0} == '#' ) {
				$href = '#';
			} else {
				$title = Title::newFromText( $link );
				if ( is_object( $title ) ) {
					$href = $title->fixSpecialName()->getLocalURL();
				} else {
					$href = '#';
				}
			}
		}

		wfProfileOut( __METHOD__ );
		return array(
			'original' => $lineArr[0],
			'text' => $text,
			'href' => $href
		);
	}

} // end of the BouquetSkinNavigationService clas
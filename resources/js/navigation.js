/**
 * navigation.js
 *
 * Handles toggling the navigation menu for small screens.
 */
( function () {
	const container = document.getElementById( 'access' ),
		button = container.getElementsByTagName( 'h1' )[ 0 ],
		menu = container.getElementsByTagName( 'ul' )[ 0 ];

	if ( undefined === button || undefined === menu ) {
		return false;
	}

	button.onclick = function () {
		if ( !menu.className.includes( 'nav-menu' ) ) {
			menu.className = 'nav-menu';
		}

		if ( button.className.includes( 'toggled-on' ) ) {
			button.className = button.className.replace( ' toggled-on', '' );
			menu.className = menu.className.replace( ' toggled-on', '' );
			container.className = container.className.replace( 'main-small-navigation', 'navigation-main' );
		} else {
			button.className += ' toggled-on';
			menu.className += ' toggled-on';
			container.className = container.className.replace( 'navigation-main', 'main-small-navigation' );
		}
	};

	// Hide menu toggle button if menu is empty.
	if ( !menu.childNodes.length ) {
		button.style.display = 'none';
	}
}() );

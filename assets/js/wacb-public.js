( function () {
	'use strict';

	if ( ! window.wacbPublic || ! window.wacbPublic.ajaxUrl || ! window.wacbPublic.nonce ) {
		return;
	}

	var sendTrackingRequest = function ( pageUrl ) {
		if ( ! pageUrl ) {
			return;
		}

		var payload = new window.FormData();

		payload.append( 'action', window.wacbPublic.action );
		payload.append( 'nonce', window.wacbPublic.nonce );
		payload.append( 'page_url', pageUrl );

		if ( navigator.sendBeacon ) {
			navigator.sendBeacon( window.wacbPublic.ajaxUrl, payload );
			return;
		}

		window.fetch( window.wacbPublic.ajaxUrl, {
			method: 'POST',
			body: payload,
			credentials: 'same-origin',
			keepalive: true
		} ).catch( function () {} );
	};

	document.addEventListener( 'click', function ( event ) {
		var target = event.target.closest( '[data-wacb-track]' );

		if ( ! target ) {
			return;
		}

		sendTrackingRequest( target.getAttribute( 'data-wacb-page-url' ) || window.location.href );
	} );
}() );

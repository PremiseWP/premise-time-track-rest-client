<?php
/**
 * Reset page.
 *
 * @package PTTRC
 * @subpackage views
 */

// Send message to Chrome extension from iframe
// in order to save credentials in LocalStorage.
//if ( $_SESSION['is_iframe'] ) : ?>
<script>
// Called sometime after postMessage is called
function receiveResetMessage(event)
{
	console.log(event.origin);
	// Do we trust the sender of this message?
	// TODO: really check for extension's ID once released.
	if (event.origin.indexOf( "chrome-extension://" ) !== 0 )
		return;

	// event.source is window.opener
	// event.data is "hello there!"

	// Assuming you've verified the origin of the received message (which
	// you must do in any case), a convenient idiom for replying to a
	// message is to call postMessage on event.source and provide
	// event.origin as the targetOrigin.
	event.source.postMessage( 'reset', event.origin );

	window.removeEventListener("message", receiveResetMessage);
}

window.addEventListener("message", receiveResetMessage, false);

// Redirect to index after 3 seconds.
window.setTimeout(function () {
	document.location.href = '/';
}, 3000);
</script>

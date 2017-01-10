/**
 * Javascripts
 */

$(document).ready(function(){

	ajaxSearchTimers();
});

function searchTimers() {

	console.log($('.search-timers').val());
	$.get( '?step=ajax-search-timers&title=' +
		encodeURIComponent( $('.search-timers').val() ) ).done( function( data ) {

		$('.ajax-results-timers').show().html( data );
	});
}

function ajaxSearchTimers() {

	// Setup before functions.
	var typingTimer;               // Timer identifier.
	var doneTypingInterval = 2000; // Time in ms (2 seconds).

	// On keyup, start the countdown.
	$('.search-timers').on('keyup', function(){

		clearTimeout( typingTimer );

		if ( $('.search-timers').val() ) {

			typingTimer = setTimeout( searchTimers, doneTypingInterval );
		} else {

			$('.ajax-results-timers').hide();
		}
	});
}

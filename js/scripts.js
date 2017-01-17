/**
 * Javascripts
 *
 * @package PTTRC
 * @subpackage js
 */

$(document).ready(function(){

	ajaxSearchTimers();

	unfoldLinks();

	addTag();

	deleteTag();
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

function unfoldLinks() {

	$('.unfold').click(function(){
		$(this).parent().addClass('unfolded');

		return false;
	});
}


function addTag() {

	var newTagID = 0;

	$('.new-tag-add-button').click(function(){

		var tagInput = $(this).prev('.new-tag-input'),
			tag = tagInput.val();

		if ( ! tag ) {

			return false;
		}

		var taxonomy = tagInput.attr('list'),
			dataListTags = $('#' + taxonomy).find('option'),
			tagID = 'new' + ( newTagID++ );

		// If tag found in datalist, get ID.
		dataListTags.each(function(){
			if ( this.value === tag ) {
				tagID = this.id;
			}
		});

		var tagHTML = '<span>' +
			'<button type="button" class="tag-delete-button">' +
				'<span class="remove-tag-icon" aria-hidden="true"></span>' +
				'<span class="screen-reader-text">Remove term</span>' +
			'</button>' +
			'<input type="hidden" name="ptt[' + taxonomy + '][' + tagID + ']" value="' + tag + '" />' +
			tag +
		'</span>';

		// Add tag to list.
		$( '.tags-list.' + taxonomy ).prepend( tagHTML );

		// Empty input value.
		tagInput.val('');

		deleteTag();

		return false;
	});
}


function deleteTag() {

	$('.tag-delete-button').click(function(){

		$(this).parent().remove();

		return false;
	});
}

/**
 * Javascripts
 *
 * @package PTTRC
 * @subpackage js
 */

$(document).ready(function(){

	$('.ptt-details-form input[type="text"]').focus(function(){
		$(this).click(function(e){
			e.stopPropagation();
			return false;
		});
	});

	ajaxSearchTimers();

	unfoldLinks();

	addTag();

	deleteTag();
});

function searchTimers() {

	// Add spinner while loading.
	$('.ajax-results-timers').show().html( '<i class="fa fa-spinner fa-spin"></i>' );

	console.log($('.search-timers').val());
	$.get( '?step=ajax-search-timers&title=' +
		encodeURIComponent( $('.search-timers').val() ) ).done( function( data ) {

		$('.ajax-results-timers').html( data );
	});
}

function ajaxSearchTimers() {

	// Setup before functions.
	var typingTimer;               // Timer identifier.
	var doneTypingInterval = 750; // Time in ms (0.75 seconds).

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
			tag = tagInput.val(),
			tagValue = tag;

		if ( ! tag ) {

			return false;
		}

		var taxonomy = tagInput.attr('list'),
			dataListTags = $('#' + taxonomy).find('option'),
			tagID = 'new' + ( newTagID++ );

		// If tag found in datalist, get ID.
		dataListTags.each(function(){
			if ( this.value === tag ) {
				tagValue = this.id;
				tagID = '';
			}
		});

		var tagHTML = '<span>' +
			'<button type="button" class="tag-delete-button">' +
				'<span class="remove-tag-icon" aria-hidden="true"></span>' +
				'<span class="screen-reader-text">Remove term</span>' +
			'</button>' +
			'<input type="hidden" name="ptt[' + taxonomy + '][' + tagID + ']" value="' + tagValue + '" />' +
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


function receiveEditMessage(event) {
	console.log(event.origin);

	// Do we trust the sender of this message?  (might be
	// different from what we originally opened, for example).
	if (wordpressUrl.indexOf( event.origin ) !== 0)
		return;

	console.log(event.data);

	// Should be edit URL.
	if ( event.data !== 'reset' )
	{
		// Edit ptt!
		document.location.href = event.data;
	}
}

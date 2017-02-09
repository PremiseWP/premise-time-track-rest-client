<?php
/**
 * Show Wordpress page template.
 * Embed Wordpress page URL using an iframe.
 *
 * @package PTTRC
 * @subpackage views
 */

// Add iframe parameter.
$url = $args['url'];

if ( strpos( $url, '?' ) ) {

	$url .= '&';
} else {

	$url .= '?';
}

global $here;

$url .= 'iframe=1&rest-client-url=' . urlencode( $here );
?>
<a href="javascript: window.history.go( -1 );" class="button">Back</a>
<iframe src="<?php echo $url; ?>" class="wordpress-iframe" id="wordpress-iframe"></iframe>
<script>
	// Edit.
	window.addEventListener("message", receiveEditMessage, false);

	var wordpressUrl = <?php echo json_encode( $args['url'] ); ?>;

	$('#wordpress-iframe').load(function(){
		this.contentWindow.postMessage("edit", '*');
	});
</script>

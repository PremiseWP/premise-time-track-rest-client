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

$url .= 'iframe=1';
?>
<a href="javascript: window.history.go( -1 );" class="button">Back</a>
<iframe src="<?php echo $url; ?>" class="wordpress-iframe"></iframe>

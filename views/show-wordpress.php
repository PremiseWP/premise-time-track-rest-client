<?php
/**
 * Show Wordpress page template.
 * Embed Wordpress page URL using an iframe.
 *
 * @package PTTRC
 * @subpackage views
 */
?>
<a href="javascript: window.history.go( -1 );" class="button">Back</a>
<iframe src="<?php echo $args['url']; ?>" class="wordpress-iframe"></iframe>

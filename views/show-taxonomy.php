<?php

// Show taxonomy template.
// Embed taxonomy URL in Wordpress site using an iframe.

?>
<a href="javascript: window.history.go( -1 );" class="button">Back</a>
<iframe src="<?php echo $args['taxonomy_url']; ?>" class="taxonomy-iframe"></iframe>

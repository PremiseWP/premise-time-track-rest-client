<?php
/**
 * Display timers results.
 *
 * @package PTTRC
 * @subpackage views
 */

$timers = $args['timers'];

if ( ! $timers ) : ?>

<p>No timers found.</p>

<?php else : ?>
	<ul>
	<?php foreach ( (array) $timers as $timer ) : ?>
		<li>
			<a href="?step=show-wordpress&amp;url=<?php echo rawurlencode( $timer['link'] ); ?>">
				<?php echo htmlspecialchars( $timer['title']['raw'] ); ?>
			</a>
		</li>
	<?php endforeach; ?>
	</ul>
<?php endif;

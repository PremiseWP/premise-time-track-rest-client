<?php

/** @var \League\OAuth1\Client\Server\User */
// $ptts = $args['ptts'];
$clients = $args['taxonomies']['clients'];
$projects = $args['taxonomies']['projects'];
$timesheets = $args['taxonomies']['timesheets'];

/** @var array */
$auth_urls = $_SESSION['site_auth_urls'];

/** @var \League\OAuth1\Client\Credentials\TokenCredentials */
$access_token = $args['tokenCredentials'];

?>
<a href="?step=ptt-form" class="button new-timer">New Timer</a>
<div class="search-timers-wrapper">
	<input type="text" name="search-timers" class="search-timers" placeholder="Search timers" />
	<div class="ajax-results-timers"></div>
</div>

<?php /*<div class="ptt-details">
	<div class="count"><?php echo count( $ptts ); ?> Timers found.</div>

	<?php foreach ( $ptts as $ptt ) : ?>
	<div class="details">
		<h4><?php echo htmlspecialchars( $ptt['title']['raw'] ); ?>
			<small><a href="<?php echo htmlspecialchars( $ptt['link'] ); ?>" target="_blank">View post</a> |
				<a href="?step=ptt-form&amp;ptt-id=<?php echo $ptt['id']; ?>">Edit</a> |
				<a href="?step=ptt-delete&amp;ptt-id=<?php echo $ptt['id']; ?>">Trash</a></small></h4>

		Time: <?php echo $ptt['pwptt_hours']; ?>
		<?php echo $ptt['content']['rendered']; ?>
		<br />

	</div>
	<?php endforeach; ?>
</div>


<p>Connected to <code><?php echo htmlspecialchars( $_SESSION['site_base'] ) ?></code>.
	<a class="reset" href="?step=reset">Reset?</a></p>*/
?>


<h2 class="title-clients">Clients:</h2>
<?php if ( $clients ) : ?>
	<ul class="taxonomy-terms-list clients">
	<?php foreach ( (array) $clients as $client ) : ?>

		<li>
			<a href="?step=show-wordpress&amp;url=<?php echo rawurlencode( $client['link'] ); // Open link in iframe! ?>">
				<?php echo htmlspecialchars( $client['name'] ); ?>
			</a>
		</li>

	<?php endforeach; ?>
	</ul>
<?php else : ?>
	<p class="no-taxonomy-found">No clients found.</p>
<?php endif; ?>


<h2>Projects:</h2>
<?php if ( $projects ) : ?>
	<ul class="taxonomy-terms-list projects">
	<?php foreach ( (array) $projects as $project ) : ?>

		<li>
			<a href="?step=show-wordpress&amp;url=<?php echo rawurlencode( $project['link'] ); // Open link in iframe! ?>">
				<?php echo htmlspecialchars( $project['name'] ); ?>
			</a>
		</li>

	<?php endforeach; ?>
	</ul>
<?php else : ?>
	<p class="no-taxonomy-found">No projects found.</p>
<?php endif; ?>
<h2>Timesheets:</h2>
<?php if ( $timesheets ) : ?>
	<ul class="taxonomy-terms-list timesheets">
	<?php foreach ( (array) $timesheets as $timesheet ) : ?>

		<li>
			<a href="?step=show-wordpress&amp;url=<?php echo rawurlencode( $timesheet['link'] ); // Open link in iframe! ?>">
				<?php echo htmlspecialchars( $timesheet['name'] ); ?>
			</a>
		</li>

	<?php endforeach; ?>
	</ul>
<?php else : ?>
	<p class="no-taxonomy-found">No timesheets found.</p>
<?php endif; ?>


<?php /*<div class="extra-detail">
	<h3>OAuth endpoints</h3>
	<dl>
		<dt>Request Token Endpoint</dt>
		<dd><code><?php echo htmlspecialchars( $auth_urls->request ) ?></code></dd>
		<dt>Authorize Endpoint</dt>
		<dd><code><?php echo htmlspecialchars( $auth_urls->authorize ) ?></code></dd>
		<dt>Access Token Endpoint</dt>
		<dd><code><?php echo htmlspecialchars( $auth_urls->access ) ?></code></dd>
	</dl>

	<h3>OAuth credentials</h3>
	<dl>
		<dt>Client Key</dt>
		<dd><code><?php echo htmlspecialchars( $_SESSION['client_key'] ) ?></code></dd>
		<dt>Client Secret</dt>
		<dd><code><?php echo htmlspecialchars( $_SESSION['client_secret'] ) ?></code></dd>

		<dt>Access Token</dt>
		<dd><code><?php /*echo htmlspecialchars( $access_token->getIdentifier() ) ?></code></dd>
		<dt>Access Token Secret</dt>
		<dd><code><?php /*echo htmlspecialchars( $access_token->getSecret() 0) ?></code></dd>
	</dl>
</div>*/

// Send message to Chrome extension from iframe
// in order to save credentials in LocalStorage.
//if ( $_SESSION['is_iframe'] ) : ?>
<script>
// Called sometime after postMessage is called
function receiveMessage(event)
{
	console.log(event.origin);
	// Do we trust the sender of this message?
	// TODO: really check for extension's ID once released.
	if (event.origin.indexOf( "chrome-extension://" ) !== 0 )
		return;

	var ptt = {
		site_base: <?php echo json_encode( $_SESSION['site_base'] ); ?>,
		client_key: <?php echo json_encode( $_SESSION['client_key'] ); ?>,
		client_secret: <?php echo json_encode( $_SESSION['client_secret'] ); ?>,
		token_credentials: <?php echo json_encode( $_SESSION['token_credentials'] ); ?>
	};

	ptt = JSON.stringify( ptt );
	// console.log(ptt);

	// event.source is window.opener
	// event.data is "hello there!"

	// Assuming you've verified the origin of the received message (which
	// you must do in any case), a convenient idiom for replying to a
	// message is to call postMessage on event.source and provide
	// event.origin as the targetOrigin.
	event.source.postMessage( ptt, event.origin );
}

window.addEventListener("message", receiveMessage, false);
</script>
<?php //endif;

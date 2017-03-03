<?php
/**
 * PTT details.
 *
 * @package PTTRC
 * @subpackage views
 */

// $ptts = $args['ptts'];
if ( $args['taxonomies'] ) {

	$clients = isset( $args['taxonomies']['clients'] ) ? $args['taxonomies']['clients'] : false;
	$projects = isset( $args['taxonomies']['projects'] ) ? $args['taxonomies']['projects'] : false;
	$timesheets = isset( $args['taxonomies']['timesheets'] ) ? $args['taxonomies']['timesheets'] : false;
}

if ( $projects && $timesheets ) : ?>
	<a href="?step=ptt-form" class="button new-timer">New Timer</a>
	<div class="search-timers-wrapper">
		<input type="text" name="search-timers" class="search-timers" placeholder="Search timers" />
		<div class="ajax-results-timers"></div>
	</div>
<?php endif;

/** @var array */
$auth_urls = $_SESSION['site_auth_urls'];

/** @var \League\OAuth1\Client\Credentials\TokenCredentials */
$access_token = $args['tokenCredentials'];


if ( $args['taxonomies'] ) :

if ( $clients !== false ) : ?>
<h2 class="title-clients">Clients:</h2>
<?php if ( count( $clients ) ) : ?>
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
<?php endif;
endif;


if ( $projects !== false ) : ?>
<h2>Projects:</h2>
<?php if ( count( $projects ) ) : ?>
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
<?php endif;
endif;

if ( $timesheets !== false ) : ?>
<h2>Timesheets:</h2>
<?php if ( count( $timesheets ) ) : ?>
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
<?php endif;
endif;

endif; ?>


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

	event.source.postMessage( ptt, event.origin );
}

window.addEventListener("message", receiveMessage, false);
</script>
<?php //endif;

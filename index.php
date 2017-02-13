<?php
/**
 * Application index.
 *
 * @package PTTRC
 */

namespace WP_REST\ExampleClient\WebDemo;

use Exception;
use WordPress\Discovery;
use WP_REST\ExampleClient;

//error_reporting( E_ALL );

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Include config.inc.php file.
 *
 * Do NOT change for require_once, include_once allows the error message to be displayed.
 */
if ( ! include_once __DIR__ . '/config.inc.php' ) {

	die ( 'config.inc.php file not found. Please read the installation directions.' );
}

require_once __DIR__ . '/includes/helpers.php';

// Start session
session_start();

// Work out where we are.
$here = get_requested_url();

// What should we show?
$step = isset( $_GET['step'] ) ? $_GET['step'] : '';
//$_SESSION['is_iframe'] = ( ! isset( $_SESSION['is_iframe'] ) && isset( $_GET['is_iframe'] ) ) ?
	//$_GET['is_iframe'] : '';

// Session & authenticated?
if ( ! $step
	&& isset( $_SESSION['site_base'] )
	&& isset( $_SESSION['client_key'] )
	&& isset( $_SESSION['client_secret'] )
	&& isset( $_SESSION['token_credentials'] ) ) {

	// Go to Step 4 directly.
	$step = 'ptt-details';
}

elseif ( isset( $_GET['site_base'] )
	&& isset( $_GET['client_key'] )
	&& isset( $_GET['client_secret'] )
	&& isset( $_GET['token_credentials'] ) )
{
	// Recreate session.
	$_SESSION['client_key'] = $_GET['client_key'];//I9aT2lBzYE2n
	$_SESSION['client_secret'] = $_GET['client_secret'];//0WwKpqHwgoVOgwwI7HgyjdAItd4DLZd8wEIQ2R6eRp0Lvqd8
	$_SESSION['token_credentials'] = $_GET['token_credentials']; //O%3A49%3A%22League%5COAuth1%5CClient%5CCredentials%5CTokenCredentials%22%3A2%3A%7Bs%3A13%3A%22%00%2A%00identifier%22%3Bs%3A24%3A%229xMnHuPSmJrLKaWlyEDBytRu%22%3Bs%3A9%3A%22%00%2A%00secret%22%3Bs%3A48%3A%22z66bCzlBQX9smdjsx3ROS89ltMq7UZaej6YJ56dC3FmiZDbg%22%3B%7D

	// http://localhost:8080/?site_base=http%3A%2F%2Flocalhost%2Ftest%2Fpremisesplitview%2F&client_key=I9aT2lBzYE2n&client_secret=0WwKpqHwgoVOgwwI7HgyjdAItd4DLZd8wEIQ2R6eRp0Lvqd8&token_credentials=O%3A49%3A%22League%5COAuth1%5CClient%5CCredentials%5CTokenCredentials%22%3A2%3A%7Bs%3A13%3A%22%00%2A%00identifier%22%3Bs%3A24%3A%229xMnHuPSmJrLKaWlyEDBytRu%22%3Bs%3A9%3A%22%00%2A%00secret%22%3Bs%3A48%3A%22z66bCzlBQX9smdjsx3ROS89ltMq7UZaej6YJ56dC3FmiZDbg%22%3B%7D

	$site = Discovery\discover( $_GET['site_base'] );//http%3A%2F%2Flocalhost%2Ftest%2Fpremisesplitview%2F
	$_SESSION['site_base'] = $site->getIndexURL();
	$_SESSION['site_auth_urls'] = $site->getAuthenticationData( 'oauth1' );

	// Go to Step 4 directly.
	$step = 'ptt-details';
}


switch ( $step ) {
	// Step 0: Pre-Discovery
	case '':
		/*return output_page( load_template( 'discovery-form' ) );*/

	// Step 1: Discovery
	case 'discover':

		if ( isset( $_GET['uri'] ) &&
			! empty( $_GET['uri'] ) ) {

			$uri = $_GET['uri'];

		} elseif ( defined( 'WORDPRESS_URI' ) ) {

			$uri = WORDPRESS_URI;
		}

		if ( empty( $uri ) ) {
			return output_page( load_template( 'discovery-form' ) );
		}

		try {
			$site = Discovery\discover( $uri );
		}
		catch (Exception $e) {
			$error = sprintf( "Error while discovering: %s.", htmlspecialchars( $e->getMessage() ) );
			return output_page( load_template( 'discovery-form' ), 'Discover', $error );
		}
		if ( empty( $site ) ) {
			$error = sprintf( "Couldn't find the API at <code>%s</code>.", htmlspecialchars( $uri ) );
			return output_page( load_template( 'discovery-form' ), 'Discover', $error );
		}
		if ( ! $site->supportsAuthentication( 'oauth1' ) ) {
			$error = "Site doesn't appear to support OAuth 1.0a authentication.";
			return output_page( load_template( 'discovery-form' ), 'Discover', $error );
		}

		$_SESSION['site_base'] = $site->getIndexURL();
		$_SESSION['site_auth_urls'] = $site->getAuthenticationData( 'oauth1' );

		return output_page( load_template( 'credential-form' ) );

	// Step 2: Pre-Authorization
	case 'preauth':
		if ( empty( $_GET['client_key'] ) || empty( $_GET['client_secret']) ) {
			return output_page( load_template( 'discovery-form' ) );
		}

		$_SESSION['client_key'] = $_GET['client_key'];
		$_SESSION['client_secret'] = $_GET['client_secret'];

		$server = get_server();

		// First part of OAuth 1.0 authentication is retrieving temporary credentials.
		// These identify you as a client to the server.
		try {
			$temporaryCredentials = $server->getTemporaryCredentials();
		} catch ( Exception $e ) {
			$error = $e->getMessage();
			return output_page( load_template( 'credential-form' ), 'Discover', $error );
		}

		// Store the credentials in the session.
		$_SESSION['temporary_credentials'] = serialize($temporaryCredentials);
		session_write_close();

		// Second part of OAuth 1.0 authentication is to redirect the
		// resource owner to the login screen on the server.
		$server->authorize($temporaryCredentials);
		return;

	// Step 3: Upgrade Credentials
	case 'authorize':
		$server = get_server();

		// Retrieve the temporary credentials from step 2
		$temporaryCredentials = unserialize($_SESSION['temporary_credentials']);

		// Third and final part to OAuth 1.0 authentication is to retrieve token
		// credentials (formally known as access tokens in earlier OAuth 1.0
		// specs).
		$tokenCredentials = $server->getTokenCredentials($temporaryCredentials, $_GET['oauth_token'], $_GET['oauth_verifier']);

		// Now, we'll store the token credentials and discard the temporary
		// ones - they're irrelevant at this stage.
		unset($_SESSION['temporary_credentials']);
		$_SESSION['token_credentials'] = serialize($tokenCredentials);
		session_write_close();

		// Redirect to the ptt page
		header("Location: {$here}?step=ptt-details");
		return;

	// Step 4: Retrieve details
	case 'ptt-details':
		$server = get_server();

		// Check somebody hasn't manually entered this URL in,
		// by checking that we have the token credentials in
		// the session.
		if ( ! isset($_SESSION['token_credentials'])) {
			echo 'No token credentials.';
			exit(1);
		}

		// Retrieve our token credentials. From here, it's play time!
		$tokenCredentials = unserialize( $_SESSION['token_credentials'] );

		try {
			// $ptts = $server->fetchPremiseTimeTracker( $tokenCredentials );
			$taxonomies = $server->fetchPremiseTimeTrackerTaxonomies( $tokenCredentials );
		}
		catch ( Exception $e ) {
			$error = $e->getMessage();

			// Handle 404 / No CPT.
			if ( strpos( $error, '404' ) ) {
				$error = '<a href="https://github.com/PremiseWP/premise-time-track" target="_blank">
					Premise Time Tracker plugin</a> not found. Please install it and activate it.';
			} elseif ( strpos( $error, '403' ) ) {

				// 403 Sorry, you are not allowed to edit terms in this taxonomy.
				// This is a Freelancer or an Author.
				$taxonomies = false;

				unset( $error );
			}

			if ( $error ) {

				return output_page( load_template( 'ptt-details' ), 'Dashboard', $error );
			}
		}

		//var_dump($ptts);

		if ( isset( $_GET['error'] )
			&& $_GET['error'] ) {

			$error = $_GET['error'];
		}

		return output_page( load_template( 'ptt-details', compact( 'taxonomies' ) ), 'Dashboard', $error );

	// Step 5: Timer form.
	case 'ptt-form':
		$server = get_server();

		// Check somebody hasn't manually entered this URL in,
		// by checking that we have the token credentials in
		// the session.
		if ( ! isset($_SESSION['token_credentials'])) {
			echo 'No token credentials.';
			exit(1);
		}

		// Retrieve our token credentials. From here, it's play time!
		$tokenCredentials = unserialize($_SESSION['token_credentials']);

		if ( isset( $_GET['ptt-id'] )
			&& $_GET['ptt-id'] ) {
			try {
				$ptt = $server->fetchPremiseTimeTracker( $tokenCredentials, $_GET['ptt-id'] );
			}
			catch ( Exception $e ) {
				$error = $e->getMessage();
				return output_page( load_template( 'ptt-form' ), 'New Timer', $error );
			}
		} else {

			// New Timer.
			$ptt = array();
		}

		try {
			$taxonomies = $server->fetchPremiseTimeTrackerTaxonomies( $tokenCredentials );
		}
		catch ( Exception $e ) {
			$error = $e->getMessage();
			return output_page( load_template( 'ptt-form', compact( 'ptt' ) ), 'New Timer', $error );
		}
		//var_dump($ptt);

		if ( isset( $_GET['error'] )
			&& $_GET['error'] ) {

			$error = $_GET['error'];
		}

		return output_page( load_template( 'ptt-form', compact( 'ptt', 'taxonomies' ) ), 'New Timer', $error );

	case 'ptt-save':
		$server = get_server();

		// Check somebody hasn't manually entered this URL in,
		// by checking that we have the token credentials in
		// the session.
		if ( ! isset($_SESSION['token_credentials'])) {
			echo 'No token credentials.';
			exit(1);
		}

		// Retrieve our token credentials. From here, it's play time!
		$tokenCredentials = unserialize( $_SESSION['token_credentials'] );

		$ptt = isset( $_GET['ptt'] ) && is_array( $_GET['ptt'] ) ? $_GET['ptt'] : '';
		$ptt_id = isset( $_GET['ptt-id'] ) && $_GET['ptt-id'] ? $_GET['ptt-id'] : '';

		try {
			$server->savePremiseTimeTracker( $tokenCredentials, $ptt_id, $ptt );
		}
		catch ( Exception $e ) {
			$error = $e->getMessage();

			$error = urlencode( $error );

			// Redirect to the ptt form
			header("Location: {$here}?step=ptt-form&ptt-id={$ptt_id}&error={$error}");
			return;
		}

		if ( ! $ptt_id ) {

			// Confirm PTT created.
			return output_page( load_template( 'ptt-created' ), 'Timer created' );
		} else {

			// Confirm PTT updated.
			return output_page( load_template( 'ptt-updated' ), 'Timer updated' );
		}

	case 'ptt-delete':
		$server = get_server();

		// Check somebody hasn't manually entered this URL in,
		// by checking that we have the token credentials in
		// the session.
		if ( ! isset( $_SESSION['token_credentials'] ) ) {
			echo 'No token credentials.';
			exit(1);
		}

		// Retrieve our token credentials. From here, it's play time!
		$tokenCredentials = unserialize( $_SESSION['token_credentials'] );

		$ptt_id = isset( $_GET['ptt-id'] ) && $_GET['ptt-id'] ? $_GET['ptt-id'] : '';

		try {
			$server->deletePremiseTimeTracker( $tokenCredentials, $ptt_id );
		}
		catch ( Exception $e ) {
			$error = $e->getMessage();

			$error = urlencode( $error );
		}

		// Redirect to the ptt page
		header( "Location: {$here}?step=ptt-details&error={$error}" );
		return;

	case 'show-wordpress':

		// Check somebody hasn't manually entered this URL in,
		// by checking that we have the token credentials in
		// the session.
		if ( ! isset( $_SESSION['token_credentials'] ) ) {
			echo 'No token credentials.';
			exit(1);
		}

		// URL.
		$url = isset( $_GET['url'] ) ? $_GET['url'] : '';

		// Check it!
		if ( strpos( $url, substr( $_SESSION['site_base'], 0, -8 ) ) !== 0 ) {

			// Redirect to ptt-details + error.
			$error = 'Malformed Wordpress URL.';

			$error = urlencode( $error . var_dump($url) );

			// Redirect to the ptt details.
			header( "Location: {$here}?step=ptt-details&error={$error}" );
			return;
		}

		return output_page( load_template( 'show-wordpress', compact( 'url' ) ), 'Timer page' );

	case 'ajax-search-timers':

		// Check AJAX.
		if ( empty( $_SERVER['HTTP_X_REQUESTED_WITH'] )
			|| $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest' ) {

			// Redirect to the ptt details.
			header( "Location: {$here}?step=ptt-details" );
			return;
		}

		$server = get_server();

		// Check somebody hasn't manually entered this URL in,
		// by checking that we have the token credentials in
		// the session.
		if ( ! isset( $_SESSION['token_credentials'] ) ) {
			echo 'No token credentials.';
			exit(1);
		}

		// Retrieve our token credentials. From here, it's play time!
		$tokenCredentials = unserialize( $_SESSION['token_credentials'] );

		// Taxonomy URL.
		$ptt_title = isset( $_GET['title'] ) ? trim( $_GET['title'] ) : '';

		// Check it!
		if ( ! $ptt_title ) {

			return;
		}

		// Search Timers by title.
		try {
			$timers = $server->searchPremiseTimeTracker( $tokenCredentials, $ptt_title );
		}
		catch ( Exception $e ) {
			$error = $e->getMessage();

			$error = urlencode( $error );

			// Redirect to the ptt page
			header( "Location: {$here}?step=ptt-details&error={$error}" );
		}

		echo load_template( 'ajax-search-timers', compact( 'timers' ) );

		return;

	// Reset session data
	case 'reset':
		session_destroy();

		return output_page( load_template( 'reset' ), 'Reset page' );

		// Redirect back to the start
		//header("Location: {$here}");
		return;
}

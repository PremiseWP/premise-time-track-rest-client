<?php
/**
 * PremiseTimeTracker model.
 *
 * @package PTTRC
 * @subpackage lib
 */

namespace WP_REST\ExampleClient;

use Exception;
use League\OAuth1\Client\Server\Server;
use League\OAuth1\Client\Server\User;
use League\OAuth1\Client\Credentials\TokenCredentials;

class PremiseTimeTracker extends PremiseWP {

	/**
	 * {@inheritDoc}
	 */
	public function urlPremiseTimeTracker( $ptt_id )
	{
		return rtrim( $this->baseUri, '/' ) . '/wp/v2/premise_time_tracker' .
			( $ptt_id ? '/' . $ptt_id : '' ) . '?context=edit';
	}


	/**
	 * {@inheritDoc}
	 */
	public function urlPremiseTimeTrackerByAuthor( $ptt_author )
	{
		return rtrim( $this->baseUri, '/' ) . '/wp/v2/premise_time_tracker/' .
			'?author=' . $ptt_author . '&context=edit';
	}


	/**
	 * {@inheritDoc}
	 */
	public function urlPremiseTimeTrackerSearch( $ptt_title )
	{
		return rtrim( $this->baseUri, '/' ) . '/wp/v2/premise_time_tracker?context=edit&search=' .
			rawurlencode( $ptt_title );
	}


	/**
	 * {@inheritDoc}
	 */
	public function urlPremiseTimeTrackerSearchLimitToAuthor( $ptt_title, $ptt_author )
	{
		return rtrim( $this->baseUri, '/' ) . '/wp/v2/premise_time_tracker?context=edit&search=' .
			rawurlencode( $ptt_title ) . '&author=' . $ptt_author;
	}


	/**
	 * {@inheritDoc}
	 */
	public function urlPremiseTimeTrackerMeta( $ptt_id, $meta_id )
	{
		return rtrim( $this->baseUri, '/' ) . '/wp/v2/premise_time_tracker/' .
			$ptt_id . '/meta' . ( $meta_id ? '/' . $meta_id : '' ) . '?context=edit';
	}


	/**
	 * {@inheritDoc}
	 */
	public function urlPremiseTimeTrackerClient( $client_id = '' )
	{
		return rtrim( $this->baseUri, '/' ) . '/wp/v2/premise_time_tracker_client/' .
			( $client_id ? '/' . $client_id : '' ) . '?context=edit';
	}


	/**
	 * {@inheritDoc}
	 */
	public function urlPremiseTimeTrackerClientSlug( $client_slug = '' )
	{
		return rtrim( $this->baseUri, '/' ) . '/wp/v2/premise_time_tracker_client/?slug=' .
			$client_slug . '&context=view';
	}


	/**
	 * {@inheritDoc}
	 */
	public function urlPremiseTimeTrackerProject( $project_id = '' )
	{
		return rtrim( $this->baseUri, '/' ) . '/wp/v2/premise_time_tracker_project/' .
			( $project_id ? '/' . $project_id : '' ) . '?context=edit';
	}


	/**
	 * {@inheritDoc}
	 */
	public function urlPremiseTimeTrackerTimesheet( $timesheet_id = '' )
	{
		return rtrim( $this->baseUri, '/' ) . '/wp/v2/premise_time_tracker_timesheet/' .
			( $timesheet_id ? '/' . $timesheet_id : '' ) . '?context=edit';
	}


	/**
	 * {@inheritDoc}
	 */
	public function urlPremiseTimeTrackerTimesheetByPost( $post_id = '' )
	{
		return rtrim( $this->baseUri, '/' ) . '/wp/v2/premise_time_tracker_timesheet/' .
			'?post=' . $post_id . '&context=edit';
	}


	/**
	 * {@inheritDoc}
	 *
	 * @internal The current user endpoint gives a redirection, so we need to
	 *     override the HTTP call to avoid redirections.
	 */
	public function fetchPremiseTimeTracker( TokenCredentials $tokenCredentials, $ptt_id = 0, $force = false )
	{
		$url = $this->urlPremiseTimeTracker( $ptt_id );

		return $this->fetchObject( $tokenCredentials, $url, $force );
	}


	/**
	 * {@inheritDoc}
	 *
	 * @internal The current user endpoint gives a redirection, so we need to
	 *     override the HTTP call to avoid redirections.
	 */
	public function fetchPremiseTimeTrackerByAuthor( TokenCredentials $tokenCredentials, $ptt_author = 0, $force = false )
	{
		$url = $this->urlPremiseTimeTrackerByAuthor( $ptt_author );

		return $this->fetchObject( $tokenCredentials, $url, $force );
	}


	/**
	 * {@inheritDoc}
	 *
	 * @internal The current user endpoint gives a redirection, so we need to
	 *     override the HTTP call to avoid redirections.
	 */
	public function fetchPremiseTimeTrackerTaxonomies( TokenCredentials $tokenCredentials, $force = false )
	{
		$taxonomies = false;

		$user_details = $this->fetchUserDetails( $tokenCredentials );

		if ( $user_details['pwptt_profile_level'] === 'client' ) {

			// Client.
			$clients = $this->fetchPremiseTimeTrackerClientsView( $tokenCredentials );

			$taxonomies['clients'] = $clients;

			return $taxonomies;
		}

		// Freelancer.
		$is_freelancer = $user_details['pwptt_profile_level'] === 'freelancer';

		$url = $this->urlPremiseTimeTrackerClient();

		$taxonomies['clients'] = $this->fetchObject( $tokenCredentials, $url, $force );

		$url = $this->urlPremiseTimeTrackerProject();

		$taxonomies['projects'] = $this->fetchObject( $tokenCredentials, $url, $force );

		if ( $is_freelancer ) {

			// Freelancer: deny access to others Timesheets.
			// Check each Timesheet to see if has any Freelancer's posts.
			$taxonomies['timesheets'] = $this->fetchPremiseTimeTrackerTimesheetsFreelancer( $tokenCredentials );

		} else {

			$url = $this->urlPremiseTimeTrackerTimesheet();

			$taxonomies['timesheets'] = $this->fetchObject( $tokenCredentials, $url, $force );
		}

		return $taxonomies;
	}


	/**
	 * {@inheritDoc}
	 *
	 * @internal The current user endpoint gives a redirection, so we need to
	 *     override the HTTP call to avoid redirections.
	 */
	public function fetchPremiseTimeTrackerTimesheetsFreelancer( TokenCredentials $tokenCredentials, $force = false )
	{
		$timesheets = false;

		$user_details = $this->fetchUserDetails( $tokenCredentials );

		$my_posts = $this->fetchPremiseTimeTrackerByAuthor( $tokenCredentials, $user_details['id'], $force );

		foreach ( (array) $my_posts as $post ) {

			$url = $this->urlPremiseTimeTrackerTimesheetByPost( $post['id'] );

			$timesheets_post = $this->fetchObject( $tokenCredentials, $url, $force );

			foreach ( (array) $timesheets_post as $timesheet ) {

				$timesheets[ $timesheet['id'] ] = $timesheet;
			}
		}

		return $timesheets;
	}


	/**
	 * {@inheritDoc}
	 *
	 * @internal The current user endpoint gives a redirection, so we need to
	 *     override the HTTP call to avoid redirections.
	 */
	public function fetchPremiseTimeTrackerClientsView( TokenCredentials $tokenCredentials, $force = false )
	{
		$clients_view = false;

		$user_details = $this->fetchUserDetails( $tokenCredentials );

		$clients = $user_details['pwptt_clients'];

		foreach ( (array) $clients as $client_slug => $yes ) {

			if ( ! $yes ) {

				continue;
			}

			$url = $this->urlPremiseTimeTrackerClientSlug( $client_slug );

			$clients_view[] = $this->fetchObject( $tokenCredentials, $url, $force )[0];
		}

		return $clients_view;
	}


	/**
	 * {@inheritDoc}
	 *
	 * @internal The current user endpoint gives a redirection, so we need to
	 *     override the HTTP call to avoid redirections.
	 */
	public function searchPremiseTimeTracker( TokenCredentials $tokenCredentials, $ptt_title, $force = false )
	{
		$user_details = $this->fetchUserDetails( $tokenCredentials );

		if ( $user_details['pwptt_profile_level'] === 'client' ) {

			// Client.
			return false;
		}

		// Freelancer.
		if ( $user_details['pwptt_profile_level'] === 'freelancer' ) {

			$url = $this->urlPremiseTimeTrackerSearchLimitToAuthor( $ptt_title, $user_details['id'] );

		} else {

			$url = $this->urlPremiseTimeTrackerSearch( $ptt_title );
		}

		return $this->fetchObject( $tokenCredentials, $url, $force );
	}


	/**
	 * {@inheritDoc}
	 *
	 * @internal The current user endpoint gives a redirection, so we need to
	 *     override the HTTP call to avoid redirections.
	 */
	public function savePremiseTimeTracker( TokenCredentials $tokenCredentials, $ptt_id, $ptt )
	{
		// Updated PTT with new term IDs :).
		$ptt = $this->savePremiseTimeTrackerNewTerms( $tokenCredentials, $ptt_id, $ptt );

		$url = $this->urlPremiseTimeTracker( $ptt_id );

		$body = array(
			'title' => $ptt['title'],
			'content' => $ptt['content'],
			'pwptt_hours' => $ptt['pwptt_hours'],
		);

		if ( isset( $ptt['status'] ) ) {

			$body['status'] = $ptt['status'];
		}

		if ( isset( $ptt['date'] ) &&
			$ptt['date'] ) {

			$body['date'] = date( 'c', strtotime( $ptt['date'] . ' 12:00:00' ) );
		}

		// Associate terms.
		$body['premise_time_tracker_client'] = (array) $ptt['clients'];

		$body['premise_time_tracker_project'] = (array) $ptt['projects'];

		$body['premise_time_tracker_timesheet'] = (array) $ptt['timesheets'];

		//var_dump($body);exit;
		return $this->saveObject( $tokenCredentials, $url, $body );
	}


	/**
	 * {@inheritDoc}
	 *
	 * @internal The current user endpoint gives a redirection, so we need to
	 *     override the HTTP call to avoid redirections.
	 */
	public function savePremiseTimeTrackerNewTerms( TokenCredentials $tokenCredentials, $ptt_id, $ptt )
	{
		// New Clients.
		if ( isset( $ptt['clients'] ) &&
			is_array( $ptt['clients'] ) ) {

			$url = $this->urlPremiseTimeTrackerClient();

			$ptt['clients'] = $this->savePremiseTimeTrackerNewTerm( $tokenCredentials, $ptt['clients'], $url );
		}

		// New Projects.
		if ( isset( $ptt['projects'] ) &&
			is_array( $ptt['projects'] ) ) {

			$url = $this->urlPremiseTimeTrackerProject();

			$ptt['projects'] = $this->savePremiseTimeTrackerNewTerm( $tokenCredentials, $ptt['projects'], $url );
		}

		// New Timesheets.
		if ( isset( $ptt['timesheets'] ) &&
			is_array( $ptt['timesheets'] ) ) {

			$url = $this->urlPremiseTimeTrackerTimesheet();

			$ptt['timesheets'] = $this->savePremiseTimeTrackerNewTerm( $tokenCredentials, $ptt['timesheets'], $url );
		}

		// Updated PTT with new IDs :).
		return $ptt;
	}


	/**
	 * {@inheritDoc}
	 *
	 * @internal The current user endpoint gives a redirection, so we need to
	 *     override the HTTP call to avoid redirections.
	 */
	public function savePremiseTimeTrackerNewTerm( TokenCredentials $tokenCredentials, $terms, $url )
	{
		foreach ( (array) $terms as $id => $term ) {

			if ( strpos( $id, 'new' ) !== 0 ) {

				continue;
			}

			if ( $term === '' ) {

				unset( $terms[ $id ] );

				continue;
			}

			$body = array(
				'name' => $term,
			);

			$new_tax_object = $this->saveObject( $tokenCredentials, $url, $body );

			// Update term ID and object.
			unset( $terms[ $id ] );

			$terms[] = $new_tax_object['id'];
		}

		return $terms;
	}


	/**
	 * {@inheritDoc}
	 *
	 * @internal The current user endpoint gives a redirection, so we need to
	 *     override the HTTP call to avoid redirections.
	 */
	public function deletePremiseTimeTracker( TokenCredentials $tokenCredentials, $ptt_id )
	{
		$url = $this->urlPremiseTimeTracker( $ptt_id );

		return $this->deleteObject( $tokenCredentials, $url );
	}
}

<?php

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
	public function urlPremiseTimeTrackerSearch( $ptt_title )
	{
		return rtrim( $this->baseUri, '/' ) . '/wp/v2/premise_time_tracker?context=edit&search=' .
			rawurlencode( $ptt_title );
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
	public function urlPremiseTimeTrackerClient( $client_id )
	{
		return rtrim( $this->baseUri, '/' ) . '/wp/v2/premise_time_tracker_client/' .
			( $client_id ? '/' . $client_id : '' ) . '?context=edit';
	}


	/**
	 * {@inheritDoc}
	 */
	public function urlPremiseTimeTrackerProject( $project_id )
	{
		return rtrim( $this->baseUri, '/' ) . '/wp/v2/premise_time_tracker_project/' .
			( $project_id ? '/' . $project_id : '' ) . '?context=edit';
	}


	/**
	 * {@inheritDoc}
	 */
	public function urlPremiseTimeTrackerTimesheet( $timesheet_id )
	{
		return rtrim( $this->baseUri, '/' ) . '/wp/v2/premise_time_tracker_timesheet/' .
			( $timesheet_id ? '/' . $timesheet_id : '' ) . '?context=edit';
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
	public function fetchPremiseTimeTrackerTaxonomies( TokenCredentials $tokenCredentials, $force = false )
	{
		$url = $this->urlPremiseTimeTrackerClient();

		$taxonomies['clients'] = $this->fetchObject( $tokenCredentials, $url, $force );

		$url = $this->urlPremiseTimeTrackerProject();

		$taxonomies['projects'] = $this->fetchObject( $tokenCredentials, $url, $force );

		$url = $this->urlPremiseTimeTrackerTimesheet();

		$taxonomies['timesheets'] = $this->fetchObject( $tokenCredentials, $url, $force );

		return $taxonomies;
	}


	/**
	 * {@inheritDoc}
	 *
	 * @internal The current user endpoint gives a redirection, so we need to
	 *     override the HTTP call to avoid redirections.
	 */
	public function searchPremiseTimeTracker( TokenCredentials $tokenCredentials, $ptt_title, $force = false )
	{
		$url = $this->urlPremiseTimeTrackerSearch( $ptt_title );

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
		$url = $this->urlPremiseTimeTracker( $ptt_id );

		$body = array(
			'title' => $ptt['title'],
			'content' => $ptt['content'],
			'pwptt_hours' => $ptt['pwptt_hours'],
		);

		if ( isset( $ptt['status'] ) ) {

			$body['status'] = $ptt['status'];
		}

		return $this->saveObject( $tokenCredentials, $url, $body );
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

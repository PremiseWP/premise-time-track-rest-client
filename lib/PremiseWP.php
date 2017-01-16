<?php
/**
 * PremiseWP model.
 *
 * @package PTTRC
 * @subpackage lib
 */

namespace WP_REST\ExampleClient;

use Exception;
use League\OAuth1\Client\Server\Server;
use League\OAuth1\Client\Server\User;
use League\OAuth1\Client\Credentials\TokenCredentials;

class PremiseWP extends WordPress {

	/**
	 * {@inheritDoc}
	 *
	 * @internal The current user endpoint gives a redirection, so we need to
	 *     override the HTTP call to avoid redirections.
	 */
	protected function fetchObject( TokenCredentials $tokenCredentials, $url, $force = false )
	{
		if ( ! $force &&
			isset( $this->cachedObjectResponse[ $url ] ) ) {

			return $this->cachedObjectResponse[ $url ];
		}

		$client = $this->createHttpClient();

		$headers = $this->getHeaders( $tokenCredentials, 'GET', $url );

		try {
			$response = $client->get( $url, $headers, array( 'allow_redirects' => false ) )->send();
		} catch ( BadResponseException $e ) {
			$response = $e->getResponse();
			$body = $response->getBody();
			$statusCode = $response->getStatusCode();

			throw new \Exception(
				"Received error [$body] with status code [$statusCode] when retrieving token credentials."
			);
		}

		switch ( $this->responseType  ) {
			case 'json':
				$this->cachedObjectResponse[ $url ] = $response->json();
				break;

			case 'xml':
				$this->cachedObjectResponse[ $url ] = $response->xml();
				break;

			case 'string':
				parse_str($response->getBody(), $this->cachedObjectResponse[ $url ]);
				break;

			default:
				throw new \InvalidArgumentException( "Invalid response type [{$this->responseType}]." );
		}

		return $this->cachedObjectResponse[ $url ];
	}



	/**
	 * {@inheritDoc}
	 *
	 * @internal The current user endpoint gives a redirection, so we need to
	 *     override the HTTP call to avoid redirections.
	 */
	protected function saveObject( TokenCredentials $tokenCredentials, $url, $body )
	{
		$client = $this->createHttpClient();

		// getHeaders(CredentialsInterface $credentials, $method, $url, array $bodyParameters = array())
		$headers = $this->getHeaders($tokenCredentials, 'POST', $url, $body);

		try {
			// http://guzzle3.readthedocs.io/http-client/client.html#creating-requests-with-a-client
			$request = $client->post( $url, $headers, $body, array( 'allow_redirects' => false ) );
			$response = $request->send();
		} catch (BadResponseException $e) {
			$response = $e->getResponse();
			$body = $response->getBody();
			$statusCode = $response->getStatusCode();

			throw new \Exception(
				"Received error [$body] with status code [$statusCode] when retrieving token credentials."
			);
		}

		switch ($this->responseType) {
			case 'json':
				$this->cachedObjectResponse[ $url ] = $response->json();
				break;

			case 'xml':
				$this->cachedObjectResponse[ $url ] = $response->xml();
				break;

			case 'string':
				parse_str($response->getBody(), $this->cachedObjectResponse[ $url ]);
				break;

			default:
				throw new \InvalidArgumentException("Invalid response type [{$this->responseType}].");
		}

		return $this->cachedObjectResponse[ $url ];
	}



	/**
	 * {@inheritDoc}
	 *
	 * @internal The current user endpoint gives a redirection, so we need to
	 *     override the HTTP call to avoid redirections.
	 */
	protected function deleteObject( TokenCredentials $tokenCredentials, $url )
	{
		$client = $this->createHttpClient();

		// getHeaders(CredentialsInterface $credentials, $method, $url, array $bodyParameters = array())
		$headers = $this->getHeaders( $tokenCredentials, 'DELETE', $url );

		try {
			// http://guzzle3.readthedocs.io/http-client/client.html#creating-requests-with-a-client
			$request = $client->delete( $url, $headers, array( 'allow_redirects' => false ) );
			$response = $request->send();
		} catch (BadResponseException $e) {
			$response = $e->getResponse();
			$body = $response->getBody();
			$statusCode = $response->getStatusCode();

			throw new \Exception(
				"Received error [$body] with status code [$statusCode] when retrieving token credentials."
			);

			return false;
		}

		return true;
	}
}

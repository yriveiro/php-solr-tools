<?php
namespace SolrTools\Client;


use \Exception;
use \Requests;

class SolrClient
{
	const DEFAULT_HTTP_RETRIES = 10;
	const DEFAULT_HTTP_TIMEOUT = 30;

	protected $nodes;
	protected $retries;
	protected $timeout;
	protected $lastError;

	public function __construct(
		array $nodes,
		$retries = self::DEFAULT_HTTP_RETRIES,
		$timeout = self::DEFAULT_HTTP_TIMEOUT
	)
	{
		$this->nodes = $nodes;
		$this->retries = $retries;
		$this->timeout = $timeout;
	}

	public function createCollection(array $properties)
	{
		$response = null;

		$url = sprintf(
			"http://%s/solr/admin/collections?action=CREATE&%s&wt=json",
			$this->nodes[array_rand($this->nodes)],
			http_build_query($properties)
		);

		try {
			$response = Requests::get(
				$url,
				array('Accept' => 'application/json'),
				array('timeout' => $this->timeout)
			);
		} catch (Exception $e) {
			$this->lastError = $e;
		}

		if (!is_null($response)) {
			$body = json_decode($response->body);

			if ($response->status_code === 200) {
				return array($response->status_code, 'ok');
			}

			return array($response->status_code, $body->error->msg);
		}

		return array(500, $this->lastError->getMessage());
	}
}
<?php
namespace SolrTools\Client;


use \Exception;
use \SolrTools\Cluster\ClusterState;
use \Requests;


class SolrClient
{
	const DEFAULT_HTTP_RETRIES = 10;
	const DEFAULT_HTTP_TIMEOUT = 30;

	protected $clusterNodes;
	protected $retries;
	protected $timeout;
	protected $lastError;
	protected $clusterStateInstance;

	public function __construct(
		array $clusterNodes,
		$retries = self::DEFAULT_HTTP_RETRIES,
		$timeout = self::DEFAULT_HTTP_TIMEOUT
	)
	{
		$this->clusterNodes = $clusterNodes;
		$this->retries = (int) $retries;
		$this->timeout = (int) $timeout;
	}

	public function initClusterState()
	{
		$this->clusterStateInstance = new ClusterState(
			$this->clusterNodes,
			$this->retries,
			$this->timeout
		);

		$this->clusterStateInstance->init();
	}

	public function getClusterState()
	{
		return $this->clusterStateInstance;
	}

	public function refreshClusterState()
	{
		$this->getClusterState()->refresh();
	}

	public function getCollection($name)
	{
		return $this->getClusterState()->getCollection($name);
	}

	public function execAPICollectionCommand($url, $forceSync = true)
	{
		$response = null;

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
			if ($response->status_code === 200) {
				if ($forceSync) {
					$this->refreshClusterState();
				}
			}

			return array($response->status_code, $response->body);
		}

		return array(500, $this->lastError->getMessage());
	}

	public function createCollection(array $properties, $forceSync = true)
	{

		$url = sprintf(
			"http://%s/solr/admin/collections?action=CREATE&%s&wt=json",
			$this->clusterNodes[array_rand($this->clusterNodes)],
			http_build_query($properties)
		);

		return $this->execAPICollectionCommand($url, $forceSync);
	}

	public function deleteCollection(array $properties, $forceSync = true)
	{
		$response = null;

		$url = sprintf(
			"http://%s/solr/admin/collections?action=DELETE&%s&wt=json",
			$this->clusterNodes[array_rand($this->clusterNodes)],
			http_build_query($properties)
		);

		return $this->execAPICollectionCommand($url, $forceSync);
	}
}
<?php
namespace SolrTools\Client;


use \Exception;
use \SolrTools\Cluster\ClusterState;
use \SolrTools\Collection\CollectionAPI;
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

	public function createCollection(array $properties, $forceSync = true)
	{
		$response = CollectionAPI::createCollection(
			$properties,
			$this->clusterNodes[array_rand($this->clusterNodes)]
		);

		if ($response[0] === 200) {
			if ($forceSync) {
				$this->refreshClusterState();
			}
		}

		return $response;
	}

	public function deleteCollection(array $properties, $forceSync = true)
	{
		$response = CollectionAPI::deleteCollection(
			$properties,
			$this->clusterNodes[array_rand($this->clusterNodes)]
		);

		if ($response[0] === 200) {
			if ($forceSync) {
				$this->refreshClusterState();
			}
		}

		return $response;
	}

	public function createAliasCollection(array $properties, $forceSync = true)
	{
		$response = CollectionAPI::createAlias(
			$properties,
			$this->clusterNodes[array_rand($this->clusterNodes)]
		);

		if ($response[0] === 200) {
			if ($forceSync) {
				$this->refreshClusterState();
			}
		}

		return $response;
	}
}
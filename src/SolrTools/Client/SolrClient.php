<?php
namespace SolrTools\Client;


use \Exception;
use \SolrTools\Cluster\ClusterState;
use \SolrTools\Collection\CollectionAPI;
use \Requests;


class SolrClient
{
	const HTTP_RETRIES = 10;
	const HTTP_TIMEOUT = 30;

	protected $clusterNodes;
	protected $retries;
	protected $timeout;
	protected $lastError;
	protected $clusterStateInstance;

	public function __construct(
		array $clusterNodes,
		$retries = self::HTTP_RETRIES,
		$timeout = self::HTTP_TIMEOUT
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

	public function getClusterNodes()
	{
		return $this->clusterNodes;
	}

	public function getRandomNode()
	{
		return $this->clusterNodes[array_rand($this->clusterNodes)];
	}

	public function getCollection($name)
	{
		return $this->getClusterState()->getCollection($name);
	}

	public function createCollection(array $params, $forceSync = true)
	{
		$response = CollectionAPI::create($params, $this->getRandomNode());

		if ($response[0] === 200) {
			if ($forceSync) {
				$this->refreshClusterState();
			}
		}

		return $response;
	}

	public function deleteCollection(array $params, $forceSync = true)
	{
		$response = CollectionAPI::delete($params, $this->getRandomNode());

		if ($response[0] === 200) {
			if ($forceSync) {
				$this->refreshClusterState();
			}
		}

		return $response;
	}

	public function createAliasCollection(array $params, $forceSync = true)
	{
		$response = CollectionAPI::createAlias($params, $this->getRandomNode());

		if ($response[0] === 200) {
			if ($forceSync) {
				$this->refreshClusterState();
			}
		}

		return $response;
	}

	public function deleteAliasCollection(array $params, $forceSync = true)
	{
		$response = CollectionAPI::deleteAlias($params, $this->getRandomNode());

		if ($response[0] === 200) {
			if ($forceSync) {
				$this->refreshClusterState();
			}
		}

		return $response;
	}

	public function ping($collection)
	{
		$response = CollectionAPI::ping($collection, $this->getRandomNode());

		return $response;
	}
}
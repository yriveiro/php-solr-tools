<?php
namespace SolrTools\Cluster;


use \Exception;
use \SolrTools\Collection\Collection;
use \Requests;


class ClusterState
{
	const DEFAULT_HTTP_RETRIES = 10;
	const DEFAULT_HTTP_TIMEOUT = 30;
	const ZK_CMD_TEMPLATE = "%s/solr/zookeeper?detail=true&path=/clusterstate.json";

	protected static $headers = array('Accept' => 'application/json');
	protected $clusterNodes;
	protected $retries;
	protected $timeout;
	protected $lastError;
	protected $collections = array();

	private $timestamp;


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

	public function getNodeUrl()
	{
		return sprintf("http://%s", $this->clusterNodes[array_rand($this->clusterNodes)]);
	}

	public function resetLastError()
	{
		$this->lastError = null;
	}

	public function init()
	{
		$this->read($this->fetch());
	}

	public function refresh()
	{
		$this->init();
	}

	public function fetch()
	{
		$response = null;
		$retries = 0;
		$this->resetLastError();

		while (true) {
			$url = sprintf(self::ZK_CMD_TEMPLATE,$this->getNodeUrl());

			try {
				$response = Requests::get(
					$url,
					self::$headers,
					array('timeout' => $this->timeout)
				);
			} catch (Exception $e) {
				// We can try other node
				$this->lastError = $e;
			}

			if (!is_null($response)) {
				if ($response->status_code === 200) {
					 break;
				}
			}

			if ($retries >= $this->retries) {
				throw new Exception('Impossible fetch cluster state');
			}

			$retries++;
		}

		return json_decode($response->body);
	}

	public function read($clusterState)
	{
		$this->timestamp = time();

		$collections = json_decode($clusterState->znode->data);

		if (count((array) $collections) > 0) {
			foreach ($collections as $name => $data) {
				$collection = new Collection($name, $data);
				$this->collections[$name] = $collection;
			}
		}
	}

	public function collectionExists($name)
	{
		return in_array($name, array_keys($this->collections));
	}

	public function getCollection($name, $forceSync = false)
	{
		if (!$this->collectionExists($name)) {
			if ($forceSync) {
				$this->refreshClusterState();

				if ($this->collectionExists($name)) {
					return $this->collections[$name];
				}
			}

			throw new Exception('Collection not exists in cluster, out of date?.');
		}

		return $this->collections[$name];
	}

	public function getCollections()
	{
		return $this->collections;
	}
}
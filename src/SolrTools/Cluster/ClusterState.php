<?php
namespace SolrTools\Cluster;


use \Exception;
use SolrTools\Cluster\Collection;
use \Requests;


class ClusterState
{
	const DEFAULT_HTTP_RETRIES = 10;
	const DEFAULT_HTTP_TIMEOUT = 30;


	protected $nodes;
	protected $retries;
	protected $timeout;
	protected $lastError;
	protected $collections = array();

	private $timestamp;


	public function __construct(
		array $nodes,
		$retries = self::DEFAULT_HTTP_RETRIES,
		$timeout = self::DEFAULT_HTTP_TIMEOUT
	)
	{
		$this->nodes = $nodes;
		$this->retries = (int) $retries;
	}

	public function getNodeUrl()
	{
		return sprintf("http://%s", $this->nodes[array_rand($this->nodes)]);
	}

	public function resetLastError()
	{
		$this->lastError = null;
	}

	public function fetch()
	{
		$response = null;
		$retries = 0;
		$this->resetLastError();

		while (true) {
			$url = sprintf(
				"%s/solr/zookeeper?detail=true&path=/clusterstate.json",
				$this->getNodeUrl()
			);

			try {
				$response = Requests::get(
					$url,
					array('Accept' => 'application/json'),
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

	public function init()
	{
		$this->read($this->fetch());
	}

	public function refresh()
	{
		$this->init();
	}

	public function getCollection($name)
	{
		if (!in_array($name, array_keys($this->collections))) {
			throw new Exception('Collection not exists in cluster, out of date?.');
		}

		return $this->collections[$name];
	}

	public function getCollections()
	{
		return $this->collections;
	}
}
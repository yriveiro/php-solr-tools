<?php
namespace SolrTools\Client;


use \Exception;
use \SolrTools\Collection\CollectionAPI;


class SolrClient
{
	const RETRIES = 10;
	const TIMEOUT = 30;

	protected $node;
	protected $retries;
	protected $timeout;

	public function __construct($node,	$retries = self::RETRIES, $timeout = self::TIMEOUT)
	{
		$this->node = $node;
		$this->retries = (int) $retries;
		$this->timeout = (int) $timeout;
	}

	public function createCollection(array $params)
	{
		return CollectionAPI::create($params, $this->node, $this->timeout);
	}

	public function deleteCollection(array $params)
	{
		return CollectionAPI::delete($params, $this->node, $this->timeout);
	}

	public function reloadCollection(array $params)
	{
		return CollectionAPI::reload($params, $this->node, $this->timeout);
	}

	public function createAliasCollection(array $params)
	{
		return CollectionAPI::createAlias($params, $this->node, $this->timeout);
	}

	public function deleteAliasCollection(array $params)
	{
		return CollectionAPI::deleteAlias($params, $this->node, $this->timeout);
	}

	public function ping($collection)
	{
		return CollectionAPI::ping($collection, $this->node, $this->timeout);
	}

	public function add(SolrDoc $doc, $collection) {
		//pass
	}

	public function delete() {}

	public function deleteByQuery() {}

	public function search() {}
}
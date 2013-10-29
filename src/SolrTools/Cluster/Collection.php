<?php
namespace SolrTools\Cluster;


use \Exception;


class Collection
{
	const ROUTER_COMPOSITEID = 'compositeId';

	protected $name;
	protected $baseUrls = array();


	public function __construct($name, $data)
	{
		$this->name = $name;

		$this->parse($data);
	}

	public function parse($data)
	{
		if (!isset($data->shards)) {
			throw new Exception("Collection '$this->name' doesn't have any shard.");
		}

		$this->parseBaseUrlsFromShards($data->shards);
	}

	public function parseBaseUrlsFromShards($shards)
	{
		foreach($shards as $shard => $sData)
		{
			foreach ($sData->replicas as $replica => $rData) {
				array_push($this->baseUrls, $rData->base_url);
			}
		}
	}

	public function getBaseUrl()
	{
		return sprintf(
			"%s/%s",
			$this->baseUrls[array_rand($this->baseUrls)],
			$this->name
		);
	}

	public function getUpdateUrl()
	{
		return sprintf(
			"%s/%s/update",
			$this->baseUrls[array_rand($this->baseUrls)],
			$this->name
		);
	}

	public function getSelectUrl()
	{
		return sprintf(
			"%s/%s/select",
			$this->baseUrls[array_rand($this->baseUrls)],
			$this->name
		);
	}
}
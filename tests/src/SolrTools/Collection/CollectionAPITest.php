<?php
namespace tests\src\SolrTools\Collection;


use Exception;
use \SolrTools\Collection\CollectionAPI;
use \PHPUnit_Framework_TestCase;


class CollectionAPITest extends PHPUnit_Framework_TestCase
{
	public function testCreateCollection()
	{
		$properties = array(
			'name' => 'phpunit',
			'numShards' => 2,
			'replicationFactor' => 1,
			'maxShardsPerNode' => 2,
			'collection.configName' => 'default'
		);

		list($code, $response) = CollectionAPI::createCollection(
			$properties,
			'localhost:8983'
		);

		$response = json_decode($response);

		$this->assertEquals(200, $code);
	}

	public function testCreateCollectionFailed()
	{
		$properties = array(
			'name' => 'phpunit',
			'numShards' => 2,
			'replicationFactor' => 1,
			'maxShardsPerNode' => 2,
			'collection.configName' => 'default'
		);

		list($code, $response) = CollectionAPI::createCollection(
			$properties,
			'noHost:8983'
		);

		$response = json_decode($response);

		$this->assertEquals(500, $code);
	}

	public function testReloadCollection()
	{
		$properties = array(
			'name' => 'phpunit',
		);

		list($code, $response) = CollectionAPI::reloadCollection(
			$properties,
			'localhost:8983'
		);

		$this->assertEquals(200, $code);
	}

	public function testCreateAlias()
	{
		$properties = array(
			'name' => 'alias-phpunit',
			'collections' => 'phpunit'
		);

		list($code, $response) = CollectionAPI::createAlias(
			$properties,
			'localhost:8983'
		);

		$this->assertEquals(200, $code);
	}

	public function testDeleteAlias()
	{
		$properties = array(
			'name' => 'alias-phpunit',
		);

		list($code, $response) = CollectionAPI::deleteAlias(
			$properties,
			'localhost:8983'
		);

		$this->assertEquals(200, $code);
	}

	public function testDeleteCollection()
	{
		$properties = array(
			'name' => 'phpunit',
		);

		list($code, $response) = CollectionAPI::deleteCollection(
			$properties,
			'localhost:8983'
		);

		$this->assertEquals(200, $code);
	}

	public function testDeleteCollectionFailed()
	{
		$properties = array(
			'name' => 'phpunit',
		);

		list($code, $response) = CollectionAPI::deleteCollection(
			$properties,
			'noHost:8983'
		);

		$response = json_decode($response);

		$this->assertEquals(500, $code);
	}
}
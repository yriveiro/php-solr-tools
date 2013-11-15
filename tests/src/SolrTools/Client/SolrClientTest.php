<?php

namespace tests\src\SolrTools\Client;

use Exception;
use \SolrTools\Client\SolrClient;
use \PHPUnit_Framework_TestCase;

class ClientTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->cli = new SolrClient($_ENV['node']);
	}

	public function testCreateCollection()
	{
		$parameters = array(
			'name' => 'phpunit',
			'numShards' => 2,
			'replicationFactor' => 1,
			'maxShardsPerNode' => 2,
			'collection.configName' => 'default'
		);

		list($code, $response) = $this->cli->createCollection($parameters);

		$this->assertEquals(200, $code, $response);
	}

	public function testCreateCollectionFailure()
	{
		$parameters = array(
			'name' => 'phpunit',
			'numShards' => 2,
			'replicationFactor' => 1,
			'maxShardsPerNode' => 2,
			'collection.configName' => 'default'
		);

		list($code, $response) = $this->cli->createCollection($parameters);

		$response = json_decode($response);

		$this->assertEquals(400, $code);
		$this->assertEquals(
			'collection already exists: phpunit',
			$response->error->msg
		);
	}

	public function testCreateCollectionFailureNodeDown()
	{
		$cli = new SolrClient('unknown:8983');

		$parameters = array(
			'name' => 'phpunit',
			'numShards' => 2,
			'replicationFactor' => 1,
			'maxShardsPerNode' => 2,
			'collection.configName' => 'default'
		);

		list($code, $response) = $cli->createCollection($parameters);

		$this->assertEquals(500, $code, $response);
	}

	public function testPing()
	{
		$parameters = array(
			'name' => 'phpunit',
			'numShards' => 2,
			'replicationFactor' => 1,
			'maxShardsPerNode' => 2,
			'collection.configName' => 'default'
		);

		$this->cli->createCollection($parameters);
		list($code, $response) = $this->cli->ping('phpunit', $_ENV['node']);

		$this->assertEquals(200, $code, $response);
	}

	public function testCreateAliasCollection()
	{
		$parameters = array(
			'name' => 'alias-phpunit',
			'collections' => 'phpunit'
		);

		list($code, $response) = $this->cli->createAliasCollection($parameters);

		$this->assertEquals(200, $code, $response);
	}

	public function testDeleteAliasCollection()
	{
		$parameters = array(
			'name' => 'alias-phpunit',
		);

		list($code, $response) = $this->cli->deleteAliasCollection($parameters);

		$this->assertEquals(200, $code, $response);
	}


	public function testDeleteCollection()
	{
		$parameters = array(
			'name' => 'phpunit'
		);

		list($code, $response) = $this->cli->deleteCollection($parameters);

		$this->assertEquals(200, $code, $response);
	}

	public function testDeleteCollectionFailure()
	{
		$parameters = array(
			'name' => 'collectionNotExists'
		);

		list($code, $response) = $this->cli->deleteCollection($parameters);

		$response = json_decode($response);

		$this->assertEquals(400, $code);
		$this->assertEquals(
			'Could not find collection:collectionNotExists',
			$response->error->msg
		);
	}

	public function testDeleteCollectionFailureNodeDown()
	{
		$cli = new SolrClient(array('unknown:8983'));

		$parameters = array(
			'name' => 'phpunit',
		);

		list($code, $response) = $cli->deleteCollection($parameters);

		$this->assertEquals(500, $code, $response);
	}
}
<?php

namespace tests\src\SolrTools\Client;

use Exception;
use \SolrTools\Client\SolrClient;
use \PHPUnit_Framework_TestCase;

class ClientTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->cli = new SolrClient(array('localhost:8983'));
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

		list($code, $msg) = $this->cli->createCollection($parameters);

		$this->assertEquals(200, $code);
		$this->assertEquals('ok', $msg);
	}

	public function testCreateCollectionFails()
	{
		$parameters = array(
			'name' => 'phpunit',
			'numShards' => 2,
			'replicationFactor' => 1,
			'maxShardsPerNode' => 2,
			'collection.configName' => 'default'
		);

		list($code, $msg) = $this->cli->createCollection($parameters);

		$this->assertEquals(400, $code);
		$this->assertEquals('collection already exists: phpunit', $msg);
	}
}
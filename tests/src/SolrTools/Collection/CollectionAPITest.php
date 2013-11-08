<?php
namespace tests\src\SolrTools\Collection;


use Exception;
use \SolrTools\Collection\CollectionAPI;
use \PHPUnit_Framework_TestCase;


class CollectionAPITest extends PHPUnit_Framework_TestCase
{
	public function tearDown()
	{
		CollectionAPI::delete(array('name' => 'phpunit'), $_ENV['node']);
		CollectionAPI::deleteAlias(array('name' => 'alias-phpunit'), $_ENV['node']);
	}

	public function testCreate()
	{
		$params = array(
			'name' => 'phpunit',
			'numShards' => 2,
			'replicationFactor' => 1,
			'maxShardsPerNode' => 2,
			'collection.configName' => 'default'
		);

		list($code, $response) = CollectionAPI::create($params,	$_ENV['node']);

		$this->assertEquals(200, $code, $response);
	}

	public function testCreateFailedNotValidHosts()
	{
		$params = array(
			'name' => 'phpunit',
			'numShards' => 2,
			'replicationFactor' => 1,
			'maxShardsPerNode' => 2,
			'collection.configName' => 'default'
		);

		list($code, $response) = CollectionAPI::create($params,	'noHost:8983');

		$response = json_decode($response);

		$this->assertEquals(500, $code, $response);
	}

	public function testCreateFailedCollecitonExists()
	{
		$params = array(
			'name' => 'phpunit',
			'numShards' => 2,
			'replicationFactor' => 1,
			'maxShardsPerNode' => 2,
			'collection.configName' => 'default'
		);

		CollectionAPI::create($params, $_ENV['node']);

		list($code, $response) = CollectionAPI::create($params, $_ENV['node']);

		$response = json_decode($response);

		$this->assertEquals(400, $code, $response);
	}

	public function testReload()
	{
		$params = array(
			'name' => 'phpunit',
			'numShards' => 2,
			'replicationFactor' => 1,
			'maxShardsPerNode' => 2,
			'collection.configName' => 'default'
		);

		CollectionAPI::create($params, $_ENV['node']);

		$params = array(
			'name' => 'phpunit',
		);

		list($code, $response) = CollectionAPI::reload($params,	$_ENV['node']);

		$this->assertEquals(200, $code, $response);
	}

	public function testCreateAlias()
	{
		$params = array(
			'name' => 'phpunit',
			'numShards' => 2,
			'replicationFactor' => 1,
			'maxShardsPerNode' => 2,
			'collection.configName' => 'default'
		);

		CollectionAPI::create($params, $_ENV['node']);

		$params = array(
			'name' => 'alias-phpunit',
			'collections' => 'phpunit'
		);

		list($code, $response) = CollectionAPI::createAlias($params, $_ENV['node']);

		$this->assertEquals(200, $code, $response);
	}

	public function testDeleteAlias()
	{
		$params = array(
			'name' => 'phpunit',
			'numShards' => 2,
			'replicationFactor' => 1,
			'maxShardsPerNode' => 2,
			'collection.configName' => 'default'
		);

		CollectionAPI::create($params, $_ENV['node']);

		$params = array(
			'name' => 'alias-phpunit',
			'collections' => 'phpunit'
		);

		list($code, $response) = CollectionAPI::createAlias($params, $_ENV['node']);

		$params = array(
			'name' => 'alias-phpunit',
		);

		list($code, $response) = CollectionAPI::deleteAlias($params, $_ENV['node']);

		$this->assertEquals(200, $code, $response);
	}

	public function testDelete()
	{
		$params = array(
			'name' => 'phpunit',
			'numShards' => 2,
			'replicationFactor' => 1,
			'maxShardsPerNode' => 2,
			'collection.configName' => 'default'
		);

		CollectionAPI::create($params, $_ENV['node']);

		$params = array(
			'name' => 'phpunit',
		);

		list($code, $response) = CollectionAPI::delete($params, $_ENV['node']);

		$this->assertEquals(200, $code, $response);
	}

	public function testDeleteFailedNotValidHost()
	{
		$params = array(
			'name' => 'phpunit',
		);

		list($code, $response) = CollectionAPI::delete($params,	'noHost:8983');

		$this->assertEquals(500, $code, $response);
	}

	public function testPing()
	{
		$params = array(
			'name' => 'phpunit',
			'numShards' => 2,
			'replicationFactor' => 1,
			'maxShardsPerNode' => 2,
			'collection.configName' => 'default'
		);

		CollectionAPI::create($params, $_ENV['node']);

		list($code, $response) = CollectionAPI::ping('phpunit', $_ENV['node']);

		$this->assertEquals(200, $code, $response);
	}

}
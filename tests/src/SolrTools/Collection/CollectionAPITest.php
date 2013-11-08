<?php
namespace tests\src\SolrTools\Collection;


use Exception;
use \SolrTools\Collection\CollectionAPI;
use \PHPUnit_Framework_TestCase;


class CollectionAPITest extends PHPUnit_Framework_TestCase
{
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

		$response = json_decode($response);

		$this->assertEquals(200, $code);
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

		$this->assertEquals(500, $code);
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

		list($code, $response) = CollectionAPI::create($params,	$_ENV['node']);

		$response = json_decode($response);

		$this->assertEquals(400, $code);
	}

	public function testReload()
	{
		$params = array(
			'name' => 'phpunit',
		);

		list($code, $response) = CollectionAPI::reload($params,	$_ENV['node']);

		$this->assertEquals(200, $code);
	}

	public function testCreateAlias()
	{
		$params = array(
			'name' => 'alias-phpunit',
			'collections' => 'phpunit'
		);

		list($code, $response) = CollectionAPI::createAlias($params, $_ENV['node']);

		$this->assertEquals(200, $code);
	}

	public function testDeleteAlias()
	{
		$params = array(
			'name' => 'alias-phpunit',
		);

		list($code, $response) = CollectionAPI::deleteAlias($params, $_ENV['node']);

		$this->assertEquals(200, $code);
	}

	public function testDelete()
	{
		$params = array(
			'name' => 'phpunit',
		);

		list($code, $response) = CollectionAPI::delete($params, $_ENV['node']);

		$this->assertEquals(200, $code);
	}

	public function testDeleteFailed()
	{
		$params = array(
			'name' => 'phpunit',
		);

		list($code, $response) = CollectionAPI::delete($params,	'noHost:8983');

		$response = json_decode($response);

		$this->assertEquals(500, $code);
	}
}
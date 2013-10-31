<?php

namespace tests\src\SolrTools\Collection;

use Exception;
use \SolrTools\Collection\Collection;
use \SolrTools\Cluster\ClusterState;
use \PHPUnit_Framework_TestCase;

class CollectionTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->cs = new ClusterState(array('localhost:8983'));
	}

	public function testCreateCollection()
	{
		$data = $this->cs->fetch();
		$collections = json_decode($data->znode->data);

		foreach ($collections as $name => $data) {
			$this->assertInstanceOf('\SolrTools\Cluster\Collection', new Collection($name, $data));
		}
	}

	public function testGetBaseUrl()
	{
		$data = $this->cs->fetch();
		$collections = json_decode($data->znode->data);

		foreach ($collections as $name => $data) {
			$collection = new Collection($name, $data);
			$this->assertRegExp("/http:\/\/.*\/$name/", $collection->getBaseUrl());
		}
	}

	public function testGetUpdateEndpoint()
	{
		$data = $this->cs->fetch();
		$collections = json_decode($data->znode->data);

		foreach ($collections as $name => $data) {
			$collection = new Collection($name, $data);
			$this->assertRegExp('/http:\/\/.*\/update/', $collection->getUpdateUrl());
		}
	}

	public function testGetSelectEndpoint()
	{
		$data = $this->cs->fetch();
		$collections = json_decode($data->znode->data);

		foreach ($collections as $name => $data) {
			$collection = new Collection($name, $data);
			$this->assertRegExp('/http:\/\/.*\/select/', $collection->getSelectUrl());
		}
	}

    /**
     * @expectedException Exception
     */
	public function testNoDataAboutCollection()
	{
		$data = $this->cs->fetch();
		$collections = json_decode($data->znode->data);

		foreach ($collections as $name => $data) {
			$data = null;
			$collection = new Collection($name, $data);
		}
	}
}
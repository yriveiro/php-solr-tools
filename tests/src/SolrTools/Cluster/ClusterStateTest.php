<?php

namespace tests\src\SolrTools\Cluster;

use Exception;
use \SolrTools\Cluster\ClusterState;
use \PHPUnit_Framework_TestCase;

class ClusterStateTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->cs = new ClusterState(array($_ENV['node']));
	}

	public function testGetNodeUrl()
	{
		$nodes = array('http://' . $_ENV['node']);

		$this->assertContains($this->cs->getNodeUrl(), $nodes);
	}

	public function testFetchClusterState()
	{
		$this->assertInstanceOf('StdClass', $this->cs->fetch());
	}

	public function testRead()
	{
		$this->cs->read($this->cs->fetch());

		$this->assertGreaterThan(0, count($this->cs->getCollections()));
	}

	public function testCollectionExists()
	{
		$this->cs->init();
		$this->assertTrue($this->cs->collectionExists('collection1'));
	}

	public function testInit()
	{
		$this->cs->init();

		$this->assertGreaterThan(0, count($this->cs->getCollections()));
	}

	public function testGetCollection()
	{
		$this->cs->init();
		$collection = $this->cs->getCollection('collection1');
		$this->assertInstanceOf('\SolrTools\Collection\Collection', $collection);
	}

    /**
     * @expectedException Exception
     */
	public function testGetCollectionNotExists()
	{
		$this->cs->init();
		$collection = $this->cs->getCollection('collectionNotExists');
	}

	public function testGetCollections()
	{
		$this->cs->init();
		$collections = $this->cs->getCollections();
		$this->assertContainsOnlyInstancesOf('\SolrTools\Collection\Collection', $collections);
	}

	public function testRefresClusterState()
	{
		$this->cs->init();
		$collections = $this->cs->getCollections();
		$this->assertContainsOnlyInstancesOf('\SolrTools\Collection\Collection', $collections);

		$this->cs->refresh();
		$collections = $this->cs->getCollections();
		$this->assertContainsOnlyInstancesOf('\SolrTools\Collection\Collection', $collections);

	}
}
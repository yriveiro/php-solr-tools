<?php

namespace tests\src\SolrTools;

use Exception;
use \SolrTools\Cluster\ClusterState;
use \PHPUnit_Framework_TestCase;

class ClusterStateTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->cs = new ClusterState(array('localhost:8983'));
	}

	public function testGetNodeUrl()
	{
		$this->assertContains($this->cs->getNodeUrl(), $nodes);
	}

	public function testFetchClusterState()
	{
		$this->assertInstanceOf('StdClass', $this->cs->fetchClusterState());
	}
}
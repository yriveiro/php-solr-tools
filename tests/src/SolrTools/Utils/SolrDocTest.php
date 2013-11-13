<?php
namespace tests\src\SolrTools\Utils;

use Exception;
use \SolrTools\Utils\SolrDoc;
use \PHPUnit_Framework_TestCase;

class SolrDocTest extends PHPUnit_Framework_TestCase
{
	public function testCreateDocWithAutoID()
	{
		$doc = new SolrDoc(true);

		$this->assertInstanceOf('\SolrTools\Utils\SolrDoc', $doc);
		$this->assertTrue(!empty($doc->id));
	}

	public function testCreateDocWithoutAutoID()
	{
		$doc = new SolrDoc();

		$this->assertInstanceOf('\SolrTools\Utils\SolrDoc', $doc);
		$this->assertTrue(empty($doc->id));
	}

	public function testCreateDocWithAutoIDAndPrefix()
	{
		$doc = new SolrDoc(true, 'shardID!');

		$this->assertInstanceOf('\SolrTools\Utils\SolrDoc', $doc);
		$this->assertStringMatchesFormat('%a!%a', $doc->id);
	}

	/**
	 * @expectedException Exception
	 */
	public function testCreateDocWithAutoIDRewrite()
	{
		$doc = new SolrDoc(true);

		$this->assertInstanceOf('\SolrTools\Utils\SolrDoc', $doc);
		$doc->id = 1234;
	}

	public function testGetAndSetField()
	{
		$doc = new SolrDoc(true);

		$doc->fieldA = 1;

		$this->assertEquals(1, $doc->fieldA);
	}

	/**
	 * @expectedException Exception
	 */
	public function testGetNotExistingField()
	{
		$doc = new SolrDoc(true);

		$this->assertEquals(1, $doc->fieldA);
	}

}
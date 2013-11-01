<?php
namespace tests\src\SolrTools\Utils;

use Exception;
use \SolrTools\Utils\SolrResponse;
use \PHPUnit_Framework_TestCase;

class SolrResponseTest extends PHPUnit_Framework_TestCase
{
	public function testParseResponse()
	{
		$response = new SolrResponse(200, '{"key": "value"}');

		$this->assertInstanceOf('\SolrTools\Utils\SolrResponse', $response);
		$this->assertEquals(200, $response->getHTTPStatusCode());
		$this->assertInstanceOf('\StdClass', $response->json());
		$this->assertEquals('{"key": "value"}', $response->getRawResponse());
	}
}
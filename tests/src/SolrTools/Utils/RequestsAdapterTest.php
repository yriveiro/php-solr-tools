<?php
namespace tests\src\SolrTools\Utils;


use Exception;
use \SolrTools\Utils\RequestsAdapter;
use \PHPUnit_Framework_TestCase;


class RequestsdapterTest extends PHPUnit_Framework_TestCase
{
	public function testCreateHTTPAdapterInstance()
	{
		$http = new RequestsAdapter(array('retries' => 5, 'timeout' => 60));

		$this->assertInstanceOf('\SolrTools\Utils\RequestsAdapter', $http);
		$this->assertEquals(5, $http->getRetries());
		$this->assertEquals(60, $http->getTimeout());
	}

	public function testGet()
	{
		$http = new RequestsAdapter(array('retries' => 5, 'timeout' => 5));

		list($code, $response) = $http->get('http://httpbin.org/delay/1');

		$this->assertEquals(200, $code);
	}

	public function testPost()
	{
		$http = new RequestsAdapter(array('retries' => 5, 'timeout' => 5));

		list($code, $response) = $http->post('http://httpbin.org/post', '{"test": 1}');

		$this->assertEquals(200, $code);
	}

	public function testTimeout()
	{
		$http = new RequestsAdapter(array('retries' => 5, 'timeout' => 1));

		list($code, $response) = $http->get('http://httpbin.org/delay/2');

		$this->assertEquals(500, $code);
	}

	public function testGetDefaultOptions()
	{
		$http = new RequestsAdapter(array('retries' => 5, 'timeout' => 1));
		$options = $http->getDefaultOptions();

		$this->assertEquals(array('accept' => 'application/json'), $options['headers']);
	}
}
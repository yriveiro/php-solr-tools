<?php
namespace tests\src\SolrTools\Utils;


use Exception;
use \SolrTools\Utils\RequestsAdapter;
use \PHPUnit_Framework_TestCase;


class RequestsdapterTest extends PHPUnit_Framework_TestCase
{
	public function testGet()
	{
		$options = RequestsAdapter::getDefaultOptions();

		$options['retries'] = 5;
		$options['timeout'] = 5;

		list($code, $response) = RequestsAdapter::get('http://httpbin.org/delay/1');

		$this->assertEquals(200, $code);
	}

	public function testPost()
	{
		$options = RequestsAdapter::getDefaultOptions();

		$options['retries'] = 5;
		$options['timeout'] = 5;

		list($code, $response) = RequestsAdapter::post('http://httpbin.org/post', '{"test": 1}');

		$this->assertEquals(200, $code);
	}

	public function testTimeout()
	{
		$options = RequestsAdapter::getDefaultOptions();

		$options['retries'] = 5;
		$options['timeout'] = 1;

		list($code, $response) = RequestsAdapter::get('http://httpbin.org/delay/2');

		$this->assertEquals(500, $code);
	}

	public function testGetDefaultOptions()
	{
		$options = RequestsAdapter::getDefaultOptions();

		$this->assertEquals(array('Accept' => 'application/json'), $options['headers']);
	}
}
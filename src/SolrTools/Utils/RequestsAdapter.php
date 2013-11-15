<?php
namespace SolrTools\Utils;


use \Exception;
use \SolrTools\API\Adapter;
use \Requests;


class RequestsAdapter implements Adapter
{
	const RETRIES = 10;
	const TIMEOUT = 30;

	const METHOD_GET = 0;
	const METHOD_POST = 1;

	private $retries;
	private $timeout;

	public function __construct($options = array())
	{
		$this->retries = self::RETRIES;
		$this->timeout = self::TIMEOUT;

		$this->parseOptions($options);
	}

	public function getRetries()
	{
		return $this->retries;
	}

	public function getTimeout()
	{
		return $this->timeout;
	}

	public function parseOptions(array $options)
	{
		if (array_key_exists('retries', $options)) {
			$this->retries = $options['retries'];
		}

		if (array_key_exists('timeout', $options)) {
			$this->timeout = $options['timeout'];
		}
	}

	public function post($url, $data, $options = array())
	{
		try {
			if (empty($options)) {
				$options = $this->getDefaultOptions();
			}

			$response = self::retry(
				self::wrap(self::METHOD_POST, $url, $data, $options),
				$this->retries
			);

			return array($response->status_code, $response->body);
		} catch (Exception $e) {
			return array(500, $e->getMessage());
		}
	}

	public function get($url, $options = array())
	{
		try {
			if (empty($options)) {
				$options = $this->getDefaultOptions();
			}

			$response = self::retry(
				self::wrap(self::METHOD_GET, $url, null, $options),
				$this->retries
			);

			return array($response->status_code, $response->body);
		} catch (Exception $e) {
			return array(500, $e->getMessage());
		}
	}

	public static function wrap($method, $url, $data = null, $options = array())
	{
		return function() use ($method, $url, $data, $options) {
			if ($method == RequestsAdapter::METHOD_POST) {
				return Requests::post($url, $options['headers'], $data, $options['options']);
			}

			if ($method == RequestsAdapter::METHOD_GET) {
				return Requests::get($url, $options['headers'], $options['options']);
			}
		};
	}

	public static function retry($f, $retries)
	{
		$counter = 1;
		$response = null;
		$error = null;

		while(true) {
			try {
				$response = $f();
			} catch (Exception $e) {
				$error = $e->getMessage();
			}

			if (!is_null($response)) {
				if ($response->status_code == 200) {
					break;
				}
			}

			if ($counter >= $retries) {
				if (is_null($error)) {
					$error = sprintf(
						"[%d] - %s",
						$response->status_code,
						$response->body
					);
				}

				throw new Exception("Max retries exceeded: " . $error);
			}

			$counter++;
		}

		return $response;
	}

	public function getDefaultOptions()
	{
		return array(
			'headers' => array('accept' => 'application/json'),
			'options' => array(
				'timeout' => $this->timeout
			)
		);
	}
}
<?php
namespace SolrTools\Utils;


use \Exception;
use \SolrTools\API\Adapter;
use \Requests;


class RequestsAdapter implements Adapter
{
	const RETRIES = 1;
	const TIMEOUT = 60;
	const METHOD_GET = 0;
	const METHOD_POST = 1;


	public static function post($url, $data, $options = array())
	{
		try {
			if (empty($options)) {
				$options = self::getDefaultOptions();
			}

			$response = self::retry(
				self::wrap(self::METHOD_POST, $url, $data, $options),
				array_key_exists('retries', $options) ? $options['retries'] : 1
			);

			return array($response->status_code, $response->body);
		} catch (Exception $e) {
			return array(500, $e->getMessage());
		}
	}

	public static function get($url, $options = array())
	{
		try {
			if (empty($options)) {
				$options = self::getDefaultOptions();
			}

			$response = self::retry(
				self::wrap(self::METHOD_GET, $url, null, $options),
				array_key_exists('retries', $options) ? $options['retries'] : 1
			);

			return array($response->status_code, $response->body);
		} catch (Exception $e) {
			return array($e->getCode(), $e->getMessage());
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
				$code = 500;

				if (is_null($error)) {
					$error = sprintf(
						"[%d] - %s",
						$response->status_code,
						$response->body
					);

					$code = $response->status_code;
				}

				throw new Exception("Max retries exceeded: " . $error, $code);
			}

			$counter++;
		}

		return $response;
	}

	public static function getDefaultOptions()
	{
		return array(
			'headers' => array('Accept' => 'application/json'),
			'options' => array(
				'timeout' => self::TIMEOUT
			),
			'retries' => self::RETRIES
		);
	}
}
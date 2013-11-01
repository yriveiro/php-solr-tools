<?php
namespace SolrTools\Utils;


use \Exception;


class SolrResponse
{
	protected $statusCode;
	protected $body;

	public function __construct($statusCode, $body)
	{
		$this->statusCode = $statusCode;
		$this->body = $body;
	}

	public function json()
	{
		$response = json_decode($this->body);

		if (json_last_error() != JSON_ERROR_NONE) {
			throw new Exception('The body response is not a valid json.');
		}

		return $response;
	}

	public function getHTTPStatusCode()
	{
		return $this->statusCode;
	}

	public function getRawResponse()
	{
		return $this->body;
	}
}
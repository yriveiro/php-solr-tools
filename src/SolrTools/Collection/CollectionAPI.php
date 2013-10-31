<?php
namespace SolrTools\Collection;


use \Exception;
use \Requests;


class CollectionAPI
{
	const DEFAULT_HTTP_TIMEOUT = 30;
	const CREATE = 'CREATE';
	const DELETE = 'DELETE';
	const RELOAD = 'RELOAD';
	const CREATEALIAS = 'CREATEALIAS';
	const DELETEALIAS = 'DELETEALIAS';
	const CMD_TEMPLATE = 'http://%s/solr/admin/collections?action=%s&%s&wt=json';


	protected static $headers = array('Accept' => 'application/json');


	public static function createCollection(
		array $properties,
		$node,
		$timeout = self::DEFAULT_HTTP_TIMEOUT
	)
	{
		$cmd = sprintf(
			self::CMD_TEMPLATE,
			$node,
			self::CREATE,
			self::buildQueryString($properties)
		);

		return self::execute($cmd, $timeout);
	}

	public static function deleteCollection(
		array $properties,
		$node,
	   	$timeout = self::DEFAULT_HTTP_TIMEOUT)
	{
		$cmd = sprintf(
			self::CMD_TEMPLATE,
			$node,
			self::DELETE,
			self::buildQueryString($properties)
		);

		return self::execute($cmd, $timeout);
	}

	public static function reloadCollection(
		array $properties,
		$node,
		$timeout = self::DEFAULT_HTTP_TIMEOUT
	)
	{
		$cmd = sprintf(
			self::CMD_TEMPLATE,
			$node,
			self::RELOAD,
			self::buildQueryString($properties)
		);

		return self::execute($cmd, $timeout);
	}

	public static function createAlias(
		array $properties,
		$node,
		$timeout = self::DEFAULT_HTTP_TIMEOUT
	)
	{
		$cmd = sprintf(
			self::CMD_TEMPLATE,
			$node,
			self::CREATEALIAS,
			self::buildQueryString($properties)
		);

		return self::execute($cmd, $timeout);
	}

	public static function deleteAlias(
		array $properties,
		$node,
		$timeout = self::DEFAULT_HTTP_TIMEOUT
	)
	{
		$cmd = sprintf(
			self::CMD_TEMPLATE,
			$node,
			self::DELETEALIAS,
			self::buildQueryString($properties)
		);

		return self::execute($cmd, $timeout);
	}

	private static function buildQueryString(array $parameters)
	{
		return http_build_query($parameters);
	}

	private static function execute($url, $timeout)
	{
		$response = null;

		try {
			$response = Requests::get(
				$url,
				self::$headers,
			   	array('timeout' => $timeout)
			);
		} catch (Exception $e) {
			// pass
		}

		if (!is_null($response)) {

			return array($response->status_code, $response->body);
		}

		return array(500, json_encode($e->getMessage()));
	}
}
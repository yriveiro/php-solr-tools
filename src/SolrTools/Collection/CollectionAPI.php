<?php
namespace SolrTools\Collection;


use \Exception;
use \SolrTools\API\Action;
use \SolrTools\Utils\RequestsAdapter;


class CollectionAPI implements Action
{
	const HTTP_TIMEOUT = 30;
	const CREATE = 'CREATE';
	const DELETE = 'DELETE';
	const RELOAD = 'RELOAD';
	const CREATEALIAS = 'CREATEALIAS';
	const DELETEALIAS = 'DELETEALIAS';
	const CMD_TPL = 'http://%s/solr/admin/collections?action=%s&%s&wt=json';
	const PING = 'http://%s/solr/%s/admin/ping?wt=json';


	public static function create(array $params, $node, $timeout = self::HTTP_TIMEOUT)
	{
		$cmd = sprintf(self::CMD_TPL, $node, self::CREATE, self::buildQuery($params));

		return self::execute($cmd, $timeout);
	}

	public static function delete(array $params, $node, $timeout = self::HTTP_TIMEOUT)
	{
		$cmd = sprintf(self::CMD_TPL, $node, self::DELETE, self::buildQuery($params));

		return self::execute($cmd, $timeout);
	}

	public static function reload(array $params, $node, $timeout = self::HTTP_TIMEOUT)
	{
		$cmd = sprintf(self::CMD_TPL, $node, self::RELOAD, self::buildQuery($params));

		return self::execute($cmd, $timeout);
	}

	public static function createAlias(array $params, $node, $timeout = self::HTTP_TIMEOUT)
	{
		$cmd = sprintf(self::CMD_TPL, $node, self::CREATEALIAS, self::buildQuery($params));

		return self::execute($cmd, $timeout);
	}

	public static function deleteAlias(array $params, $node, $timeout = self::HTTP_TIMEOUT)
	{
		$cmd = sprintf(self::CMD_TPL, $node, self::DELETEALIAS, self::buildQuery($params));

		return self::execute($cmd, $timeout);
	}

	public static function ping($collection, $node, $timeout = self::HTTP_TIMEOUT)
	{
		$cmd = sprintf(self::PING, $node, $collection);

		return self::execute($cmd, $timeout);
	}

	private static function buildQuery(array $parameters)
	{
		return http_build_query($parameters);
	}

	private static function execute($cmd, $timeout)
	{
		return RequestsAdapter::get($cmd);
	}
}
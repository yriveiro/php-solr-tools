<?php
namespace SolrTools\API;

/**
 * @codeCoverageIgnore
 */
interface Action
{
	public static function create(array $params, $node, $timeout = null);
	public static function delete(array $params, $node, $timeout = null);
	public static function reload(array $params, $node, $timeout = null);
}
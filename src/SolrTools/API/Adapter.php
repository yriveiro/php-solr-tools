<?php
namespace SolrTools\API;


/**
 * @codeCoverageIgnore
 */
interface Adapter
{
	public function post($url, $data, $options = array());
	public function get($url, $options = array());
}
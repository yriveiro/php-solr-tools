<?php
namespace SolrTools\API;


interface Adapter
{
	public static function post($url, $data, $options = array());
	public static function get($url, $options = array());
}
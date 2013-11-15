<?php
namespace SolrTools\API;


interface Adapter
{
	public function post($url, $data, $options = array());
	public function get($url, $options = array());
}
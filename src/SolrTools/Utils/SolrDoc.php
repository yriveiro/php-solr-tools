<?php
namespace SolrTools\Utils;


use \Exception;


class SolrDoc
{
	private $autoID = false;
	private $doc = array();

	public function __construct($autoID = false, $autoShardPrefix = '')
	{
		if ($autoID) {
			$this->autoID = $autoID;
			$this->doc['id'] = self::uuid($autoShardPrefix);
		}
	}

	public function __set($key, $value)
	{
		if ($this->autoID && $key == 'id') {
			throw new Exception("You can set field 'id' with autoID set to true.");
		}

		$this->doc[$key] = $value;
	}

	public function __get($key)
	{
		if (array_key_exists($key, $this->doc)) {
			return $this->doc[$key];
		}

		throw new Exception("Field $key doesn't exists in this document.");
	}

	public function __isset($key)
	{
		return array_key_exists($key, $this->doc);
	}

	public static function uuid($prefix = '')
	{
		return str_replace('.', '', uniqid ($prefix, true));
	}
}
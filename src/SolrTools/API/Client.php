<?php
namespace SolrTools\API;


interface Client
{
	public function add(SolrDoc $doc, $collection);
	public function delete();
	public function deleteByQuery();
	public function search();
}
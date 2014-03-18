<?php
namespace MNA\Beanstalkapp;

class Directory extends Node {
	protected $nodes;
	
	protected $mutableProperties = [
		"path"
	];
	
	public function fill(array $properties) {
		if (isset($properties["nodes"])) {
			if (!is_array($properties["nodes"])) {
				$properties["nodes"] = [];
			}
			
			foreach ($properties["nodes"] as &$node) {
				$node = Node::dispatch($node, $this->api);
			}
		}
		
		return parent::fill($properties);
	}
}
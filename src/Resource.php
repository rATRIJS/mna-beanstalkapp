<?php
namespace MNA\Beanstalkapp;

abstract class Resource {
	protected $api;
	
	protected $mutableProperties = [];
	
	public function __construct(array $properties = [], API $api = null) {
		$this->api = isset($api) ? $api : API::main();
		
		$this->fill($properties);
	}
	
	public function __get($property) {
		return isset($this->{$property}) ? $this->{$property} : null;
	}
	
	public function __set($property, $value) {
		if ($this->isPropertyMutable($property)) {
			$this->{$property} = $value;
		}
		else {
			// TODO: throw Exception
		}
	}
	
	public function __isset($property) {
		return isset($this->{$property});
	}
	
	public function __unset($property) {
		if ($this->isPropertyMutable($property)) {
			unset($this->{$property});
		}
		else {
			// TODO: throw Exception
		}
	}
	
	public function fill(array $properties) {
		foreach ($properties as $property => $value) {
			$property = \ICanBoogie\camelize($property, true);
			
			if (property_exists($this, $property)) {
				$this->{$property} = $value;
			}
		}
		
		return $this;
	}
	
	public function exportForApi() {
		$properties = [];
		
		foreach ($this->mutableProperties as $property) {
			$value = $this->__get($property);
			
			if (isset($value)) {
				$properties[\ICanBoogie\underscore($property)] = $value;
			}
		}
		
		return $properties;
	}
	
	public function isPropertyMutable($property) {
		return in_array($property, $this->mutableProperties);
	}
}
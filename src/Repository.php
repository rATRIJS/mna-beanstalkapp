<?php
namespace MNA\Beanstalkapp;

class Repository extends Resource {
	const TYPE_ID_GIT = 'git';
	const TYPE_ID_SUBVERSION = 'subversion';
	
	const TYPE_GIT = 'GitRepository';
	const TYPE_SUBVERSION = 'SubversionRepository';
	
	const LABEL_WHITE = 'label-white';
	const LABEL_PINK = 'label-pink';
	const LABEL_RED = 'label-red';
	const LABEL_RED_ORANGE = 'label-red-orange';
	const LABEL_ORANGE = 'label-orange';
	const LABEL_YELLOW = 'label-yellow';
	const LABEL_YELLOW_GREEN = 'label-yellow-green';
	const LABEL_AQUA_GREEN = 'label-aqua-green';
	const LABEL_GREEN = 'label-green';
	const LABEL_GREEN_BLUE = 'label-green-blue';
	const LABEL_SKY_BLUE = 'label-sky-blue';
	const LABEL_LIGHT_BLUE = 'label-light-blue';
	const LABEL_BLUE = 'label-blue';
	const LABEL_ORCHID = 'label-orchid';
	const LABEL_VIOLET = 'label-violet';
	const LABEL_BROWN = 'label-brown';
	const LABEL_BLACK = 'label-black';
	const LABEL_GREY = 'label-grey';
	
	protected $id;
	protected $accountId;
	protected $title;
	protected $name;
	protected $colorLabel;
	protected $defaultBranch;
	protected $type;
	protected $vcs;
	protected $repositoryUrl;
	protected $lastCommitAt;
	protected $updatedAt;
	protected $createdAt;
	
	protected $createStructure;
	
	protected $mutableProperties = [
		'title',
		'name',
		'colorLabel',
		'defaultBranch',
		'createStructure'
	];
	
	public static function find(array $parameters = [], API $api = null) {
		if (!isset($api)) {
			$api = API::main();
		}
		
		$repositories = [];
		$response = $api->request("/repositories", $parameters);
		
		foreach ($response as $repository) {
			if (!isset($repository["repository"])) {
				throw new Exceptions\MalformedResponseException("`repository` key not available in API response.");
			}
			
			$repositories[] = new static($repository["repository"], $api);
		}
		
		return $repositories;
	}
	
	public static function get($id, API $api = null) {
		if (!isset($api)) {
			$api = API::main();
		}
		
		$response = $api->request("/repositories/{$id}");
		if (!isset($response["repository"])) {
			throw new Exceptions\MalformedResponseException("`repository` key not available in API response.");
		}
		
		return new static($response["repository"], $api);
	}
	
	public function create($typeId) {
		$parameters = $this->exportForApi();
		
		$parameters["type_id"] = $typeId;
		if (empty($parameters["color_label"])) {
			unset($parameters["color_label"]);
		}
		if ($typeId === static::TYPE_ID_SUBVERSION || empty($parameters["default_branch"])) {
			unset($parameters["default_branch"]);
		}
		if ($typeId === static::TYPE_ID_GIT || !isset($parameters["create_structure"])) {
			unset($parameters["create_structure"]);
		}
		
		$response = $this->api->request("/repositories", $parameters, API::REQUEST_METHOD_POST);
		if (!isset($response["repository"])) {
			throw new Exceptions\MalformedResponseException("`repository` key not available in API response.");
		}
		
		$this->fill($response["repository"]);
		
		return $this;
	}
	
	public function createGitRepository() {
		return $this->create(static::TYPE_ID_GIT);
	}
	
	public function createSubversionRepository() {
		return $this->create(static::TYPE_ID_SUBVERSION);
	}
	
	public function update() {
		$parameters = $this->exportForApi();
		if (!$this->isGit()) {
			unset($parameters["default_branch"]);
		}
		if (!$this->isSubversion()) {
			unset($parameters["create_structure"]);
		}
		
		$response = $this->api->request("/repositories/{$this->id}", $parameters, API::REQUEST_METHOD_PUT);
		if (!isset($response["repository"])) {
			throw new Exceptions\MalformedResponseException("`repository` key not available in API response.");
		}
		
		$this->fill($response["repository"]);
		
		return $this;
	}
	
	public function isGit() {
		return ($this->type === static::TYPE_GIT);
	}
	
	public function isSubversion() {
		return ($this->type === static::TYPE_SUBVERSION);
	}
}
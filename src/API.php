<?php
namespace MNA\Beanstalkapp;

class API {
	const API_URL = "https://%s.beanstalkapp.com/api%s";
	
	const REQUEST_METHOD_GET = "GET";
	const REQUEST_METHOD_PUT = "PUT";
	const REQUEST_METHOD_POST = "POST";
	const REQUEST_METHOD_DELETE = "DELETE";
	
	const PARAMETER_PAGE = "page";
	const PARAMETER_PER_PAGE = "per_page";
	
	protected static $main;
	
	protected $accountName;
	protected $userName;
	protected $accessToken;
	protected $appName;
	
	protected $supportedRequestMethods = [
		self::REQUEST_METHOD_GET,
		self::REQUEST_METHOD_PUT,
		self::REQUEST_METHOD_POST,
		self::REQUEST_METHOD_DELETE
	];
	
	public static function main(API $api = null) {
		if (isset($api)) {
			static::$main = $api;
			
			return $api;
		}
		else {
			if (!isset(static::$main)) {
				throw new Exceptions\NoMainApiException("Main \\MNA\\Beanstalkapp\\API object not set.");
			}
			
			return static::$main;
		}
	}
	
	public function __construct($accountName, $userName, $accessToken, $appName = "MNA-BeanstalkappAPI") {
		if (!isset(static::$main)) {
			static::$main = $this;
		}
		
		$this->accountName = $accountName;
		$this->userName = $userName;
		$this->accessToken = $accessToken;
		$this->appName = $appName;
	}
	
	public function request($path, array $parameters = [], $method = self::REQUEST_METHOD_GET) {
		if (!in_array($method, $this->supportedRequestMethods)) {
			throw new Exceptions\InvalidRequestMethodException("Invalid request method `{$method}`.");
		}
		
		$url = "/" . trim($path, "/") . ".json";
		$url = sprintf(static::API_URL, $this->accountName, $url);
		
		if ($method === static::REQUEST_METHOD_GET && !empty($parameters)) {
			$url .= "?" . http_build_query($parameters);
		}
		
		$curlHandle = curl_init($url);
		
		curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curlHandle, CURLOPT_USERPWD, "{$this->userName}:{$this->accessToken}");
		curl_setopt($curlHandle, CURLOPT_HTTPHEADER, [
			"Content-Type" => "application/json",
			"User-Agent" => $this->appName
		]);
		curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, $method);
		if ($method !== static::REQUEST_METHOD_GET && !empty($parameters)) {
			curl_setopt($curlHandle, CURLOPT_POSTFIELDS, http_build_query($parameters));
		}
		
		$response = curl_exec($curlHandle);
		if ($response === false) {
			throw new Exceptions\RequestException("Couldn't make `{$method}` request to `{$url}`.");
		}
		
		$response = empty($response) ? [] : json_decode($response, true);
		if (!is_array($response)) {
			$response = [];
		}
		
		$responseErrors = empty($response["errors"]) ? [] : $response["errors"];
		$responseCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
		
		if (empty($responseErrors)) {
			$responseErrors = false;
		}
		else {
			$responseErrors = "`" . implode("`, `", $responseErrors) . "`";
		}
		
		if ($responseCode === 401) {
			throw new Exceptions\UnauthorisedAccessException(
				"Unauthorised access to `{$url}`. Got errors: {$responseErrors}"
			);
		}
		elseif (intval(floor($responseCode / 100)) !== 2) {
			throw new Exceptions\RequestException(
				"Request to `{$url}` returned a non 200 response. Got errors: {$responseErrors}"
			);
		}
		
		return $response;
	}
	
	// Accounts
	
	public function account() {
		return Account::get($this);
	}
	
	// Plans
	
	public function plans() {
		return Plan::find($this);
	}
	
	// Users
	
	public function users(array $parameters = []) {
		return User::find($parameters, $this);
	}
	
	public function user($userId) {
		return User::get($userId, $this);
	}
	
	public function currentUser() {
		return User::current($this);
	}
	
	public function createUser() {
		return User::create($login, $email, $name, $password, $parameters, $this);
	}
	
	// Invitations
	
	public function invitations() {
		return Invitation::find($this);
	}
	
	public function invitation($invitationId) {
		return Invitation::get($invitationId, $this);
	}
	
	public function createInvitation($email, $name) {
		return Invitation::create($email, $name, $this);
	}
	
	public function resendInvitation($userId) {
		return Invitation::resend($userId, $this);
	}
	
	// Public Keys
	
	public function publicKeys($userId = null) {
		return PublicKey::find($userId, $this);
	}
	
	public function publicKey($publicKeyId) {
		return PublicKey::get($publicKeyId, $this);
	}
	
	public function createPublicKey($content, array $parameters = []) {
		return PublicKey::create($content, $parameters, $this);
	}
	
	// Feed Keys
	
	public function feedKey() {
		return FeedKey::get($this);
	}
	
	// Repositories
	
	public function repositories(array $parameters = []) {
		return Repository::find($parameters, $this);
	}
	
	public function repository($repositoryId) {
		return Repository::get($repositoryId, $this);
	}
	
	public function repositoryBranches($repositoryId) {
		return RepositoryBranch::find($repositoryId, $this);
	}
	
	public function repositoryTags($repositoryId) {
		return RepositoryTag::find($repositoryId, $this);
	}
	
	public function createGitRepository($title, $name, array $parameters = []) {
		return Repository::createGitRepository($title, $name, $parameters, $this);
	}
	
	public function createSubversionRepository($title, $name, array $parameters = []) {
		return Repository::createSubversionRepository($title, $name, $parameters, $this);
	}
	
	// Repository Imports
	
	public function repositoryImports() {
		return RepositoryImport::find($this);
	}
	
	public function repositoryImport($repositoryImportId) {
		return RepositoryImport::get($repositoryImportId, $this);
	}
	
	public function createRepositoryImport($repositoryId, $uri) {
		return RepositoryImport::create($repositoryId, $uri, $this);
	}
	
	// Permissions
	
	public function permissions($userId) {
		return Permission::find($userId, $this);
	}
	
	public function createPermission($repositoryId, $userId, array $parameters = []) {
		return Permission::create($repositoryId, $userId, $parameters, $this);
	}
	
	// TODO: Integrations
	
	// Changesets
	
	public function changesets(array $parameters = []) {
		return ChangeSet::find($parameters, $this);
	}
	
	public function changesetsForRepository($repositoryId, array $parameters = []) {
		return ChangeSet::findForRepository($repositoryId, $parameters, $this);
	}
	
	public function changeset($repositoryId, $revision) {
		return ChangeSet::get($repositoryId, $revision, $this);
	}
	
	public function changesetDiffs($repositoryId, $revision) {
		return ChangeSetDiff::find($repositoryId, $revision, $this);
	}
	
	// TODO: Comments
	
	// Nodes
	
	public function node($repositoryId, array $parameters = []) {
		return Node::get($repositoryId, $parameters, $this);
	}
	
	public function blob($repositoryId, $id, array $parameters = []) {
		return Blob::get($repositoryId, $id, $parameters, $this);
	}
	
	public function createFile($repositoryId, $path, $contents, array $parameters = []) {
		return File::create($repositoryId, $path, $contents, $parameters, $this);
	}
	
	// TODO: Server Environments
	
	// TODO: Release Servers
	
	// TODO: Releases
	
	// TODO: Teams
}
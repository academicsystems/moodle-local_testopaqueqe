<?php
	
class Route {
	
	private $match = false;
	private $mode = 'regex';
	private $method;
	private $path;
	
	public function __construct($method,$path) {
		$this->match = false;
		$this->set_method($method);
		$this->set_path($path);
	}
	
	/*** getters & setters */
	
	public function set_method($method) {
		$umethod = strtoupper($method);
		switch($umethod) {
			case "DELETE":
			case "GET":
			case "HEAD":
			case "OPTIONS":
			case "PATCH":
			case "POST":
			case "PUT":
			case "TRACE":
				$this->method = $umethod;
				break;
			default:
				throw new Exception("Unsupported method passed to set_method(). $method was passed.");
		}
	}
	
	public function get_method() {
		return $this->method;
	}
	
	public function set_mode($mode) {
		$lower_mode = strtolower($mode);
		switch($lower_mode) {
			case 'glob':
			case 'globbing':
			case 'wildcards':
				$this->mode = 'glob'; break;
			case 'regex':
				$this->mode = 'regex'; break;
			default:
				throw new Exception("Unsupported mode passed to set_mode(). Must be glob|regex.");
		}
	}
	
	public function set_path($path) {
		if(!is_string($path)) {
			throw new Exception("Invalid type passed to set_path(). Must be string.");
		}
		
		$this->path = $path;
	}
	
	public function get_path() {
		return $this->path;
	}
	
	/*** helpers ***/
	
	private function matchPath($pathRegex) {
		if(strlen($pathRegex) === 0)
				return 1;
		
		if($this->mode === 'glob')
		{
			// match against globbing pattern
			return fnmatch($pathRegex, $this->path);
		}
		else
		{
			// default to regex pattern matching
			$pathPrepared = '/^' . preg_replace("/(?<=[^\\\\])(\/)|^\//","\/",$pathRegex) . '$/';
			return preg_match($pathPrepared, $this->path);
		}
	}
	
	/** *routes ***/
	
	public function all($pathRegex,$route) {
		if($this->match) { return; }
		if($this->matchPath($pathRegex) === 1) {
			$this->match = true; $route();
		}
	}
	
	public function delete($pathRegex,$route) {
		if($this->match) { return; }
		if($this->method === 'DELETE' && $this->matchPath($pathRegex) === 1) {
			$this->match = true; $route();
		}
	}
	
	public function get($pathRegex,$route) {
		if($this->match) { return; }
		if($this->method === 'GET' && $this->matchPath($pathRegex) === 1) {
			$this->match = true; $route();
		}
	}
	
	public function head($pathRegex,$route) {
		if($this->match) { return; }
		if($this->method === 'HEAD' && $this->matchPath($pathRegex) === 1) {
			$this->match = true; $route();
		}
	}
	
	public function options($pathRegex,$route) {
		if($this->match) { return; }
		if($this->method === 'OPTIONS' && $this->matchPath($pathRegex) === 1) {
			$this->match = true; $route();
		}
	}
	
	public function patch($pathRegex,$route) {
		if($this->match) { return; }
		if($this->method === 'PATCH' && $this->matchPath($pathRegex) === 1) {
			$this->match = true; $route();
		}
	}
	
	public function post($pathRegex,$route) {
		if($this->match) { return; }
		if($this->method === 'POST' && $this->matchPath($pathRegex) === 1) {
			$this->match = true; $route();
		}
	}
	
	public function put($pathRegex,$route) {
		if($this->match) { return; }
		if($this->method === 'PUT' && $this->matchPath($pathRegex) === 1) {
			$this->match = true; $route();
		}
	}
	
	public function trace($pathRegex,$route) {
		if($this->match) { return; }
		if($this->method === 'TRACE' && $this->matchPath($pathRegex) === 1) {
			$this->match = true; $route();
		}
	}
}

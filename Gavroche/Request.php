<?php

namespace Gavroche;

class Request
{
	protected $type;

	protected $headers = array();

	protected $host;
	protected $instance;
	protected $path;

	public function __construct(array $params)
	{
		$this->host = $params['host'];
		$this->instance = $params['instance'];
		$this->path = $params['path'];
		
		$this->headers['User-Agent'] = 'yandex-tank/1.1.1';
		$this->headers['Host'] = $params['host'];
		$this->headers['Connection'] = 'Close';
		if (!empty($params['session'])) {
			$this->headers['Cookie'] = "PHPSESSID={$params['session']}";
		}

	}

	public function __toString()
	{
		return $this->getConnection() . $this->getHeaders() . "\r\n";
	}

	public static function create($params)
	{
		$type = isset($params['type']) ? $params['type'] : 'get';

		switch ($type) {
			case 'post':
				return new PostRequest($params);
			case 'get':
			default:
				return new GetRequest($params);
		}
	}

	protected function getConnection()
	{
		return strtoupper($this->type) . " /$this->instance/$this->path HTTP/1.1\r\n";
	}

	protected function getHeaders()
	{
		$headers = '';

		foreach ($this->headers as $key => $value) {
			$headers .= "$key: $value\r\n";
		}

		return $headers;
	}
}

class PostRequest extends Request
{
	protected $type = 'post';

	protected $data = '';

	public function __construct(array $params) {
		parent::__construct($params);

		if (isset($params['data'])) {
			$this->data = is_array($params['data']) ? http_build_query($params['data']) : $params['data'];
			$this->headers['Content-Type'] = 'application/x-www-form-urlencoded';
		}

		if (isset($params['payload'])) {
			$this->data = trim($params['payload']);
			$this->headers['Content-Type'] = 'Content-Type: multipart/form-data; boundary=' . $this->getBoundary($this->data);	
		}
		$this->headers['Content-Length'] = strlen($this->data);
	}

	public function __toString()
	{
		return parent::__toString() . "$this->data\r\n\r\n";
	}

	protected function getBoundary($payload)
	{
		list($head, $tail) = explode("\n", $payload, 2);

		return substr(trim($head), 2);
	}
}

class GetRequest extends Request
{
	protected $type = 'get';
}
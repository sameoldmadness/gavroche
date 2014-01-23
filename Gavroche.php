<?php

class Gavroche
{
	private $host;
	private $instance;
	public $sessions;

	public function __construct($host, $instance, $session = null)
	{
		$this->host = $host;
		$this->instance = $instance;
		$this->sessions = $session ? array($session) : array();
	}

	public function __call($name, $args)
	{
		switch ($name) {
			case 'get':
			case 'post':
				$path = $args[0];
				$params = isset($args[1]) ? $args[1] : array();
				$label = isset($args[2]) ? $args[2] : null;

				if (!is_array($params)) {
					$label = $params;
					$params = array();
				}

				$params['type'] = $name;

				return $this->request($path, $params, $label);
		}
	}

	public function request($path, array $params, $label = '')
	{
		$params['path'] = $path;

		if (!isset($params['host'])) {
			$params['host'] = $this->host;
		}

		if (!isset($params['instance'])) {
			$params['instance'] = $this->instance;
		}

		if (!isset($params['session'])) {
			$params['session'] = $this->getRandomSession();
		}

		$request = Request::create($params);
		$label .= '.' . $params['host'];

		return strlen($request) . $label . "\n" . $request;
	}

	public function grabSession(array $credentials)
	{
		foreach ($credentials as $record) {
			$client = new SoapClient(
				"http://$this->host/$this->instance/soap.php?wsdl",
				array('cache_wsdl' => WSDL_CACHE_NONE)
			);
			$auth = array(
				'user_name' => $record['user'],
				'password' => $record['password'],
				'version' => null
			);
			$this->sessions[] = $client->login($auth)->id;			
		}
	}

	public function fromAccessLogContent($log, $limit = -1) {
		$res = array();

		foreach ($this->parseAccessLog($log) as $path) {
			if ($limit !== -1) {
				if ($limit === 0) {
					break;
				}

				$limit -=1;
			}

			$res[] = $this->get($path);
		}

		return $res;
	}

	public function fromaccessLog($filename, $limit = -1) {
		return $this->fromAccessLogContent(file_get_contents($filename), $limit);
	}

	protected function parseAccessLog($log) {
		$rows = explode("\n", trim($log));
		$res = array();

		foreach ($rows as $row) {
			$row = trim($row);
			
			if (preg_match('/GET (\S+) HTTP/', $row, $matches)) {
				$res[] = $matches[1];
			}

		}

		return $res;
	}

	public function getRandomSession() {
		return $this->sessions[array_rand($this->sessions)];
	}
}

<?php

namespace Gavroche;

class Application
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
			default:
				throw new \Exception("Unknown method '$name'");
				
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

	public function fromAccessLog($filename) {
		return array_filter(array_map(function ($request) {
			if (preg_match('/(GET|POST) (\S+) HTTP/', $request[LogReader::PARAM_REQUEST], $matches)) {
				list(, $type, $path) = $matches;

				switch ($type) {
					case 'GET':
						return $this->get($path);
					case 'POST':
						return $this->post($path, array('data' => $request[LogReader::PARAM_BODY]));
				}
			}
		}, $this->logReader->parse(file_get_contents($filename))));
	}

	public function getRandomSession() {
		return empty($this->sessions) ? '' : $this->sessions[array_rand($this->sessions)];
	}

	public function setLogReader(LogReader $logReader) {
		$this->logReader = $logReader;
	}
}

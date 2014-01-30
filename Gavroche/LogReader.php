<?php

namespace Gavroche;

class LogReader
{
	const PARAM_REQUEST = 'request';
	const PARAM_BODY    = 'body';

	public $glue;
	public $schema;

	public function __construct(array $schema, $glue = ' ') {
		$this->schema = $schema;
		$this->glue = $glue;
	}

	public function parse($content) {
		$that = $this;

		$rows = array_filter(array_map(function ($row) use ($that) {
			$row = explode($that->glue, trim($that->decodeJavascriptEscapeSequences($row)));

			return count($that->schema) === count($row) ? array_combine($that->schema, $row) : null;
		}, explode("\n", trim($content))));

		return $rows;
	}

	public function decodeJavascriptEscapeSequences($string)
	{
		return preg_replace_callback(
			"/\\\\x([0-9a-d]{2})/i",
			function ($a) { return chr(hexdec($a[1])); },
			$string
		);
	}
}
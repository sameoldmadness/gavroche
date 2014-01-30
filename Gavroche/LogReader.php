<?php

namespace Gavroche;

class LogReader
{
	const PARAM_REQUEST = 'request';
	const PARAM_BODY    = 'body';

	protected $glue;
	protected $schema;

	public function __construct(array $schema, $glue = ' ') {
		$this->schema = $schema;
		$this->glue = $glue;
	}

	public function parse($content) {
		$rows = array_map(function ($row) {
			$row = explode($this->glue, trim($row));

			return array_combine($this->schema, $row);
		}, explode("\n", trim($content)));

		return $rows;
	}
}
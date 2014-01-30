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
		$glue   = $this->glue;
		$schema = $this->schema;

		$rows = array_filter(array_map(function ($row) use ($glue, $schema) {
			$row = explode($glue, trim($row));

			return count($schema) === count($row) ? array_combine($schema, $row) : null;
		}, explode("\n", trim($content))));

		return $rows;
	}
}
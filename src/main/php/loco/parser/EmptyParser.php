<?php

namespace loco\parser;

// Match the empty string
class EmptyParser extends StaticParser {
	// default callback returns null
	public function defaultCallback() {
		return null;
	}

	// Always match successfully, pass no args to callback
	public function getResult($string, $i = 0) {
		return array(
			"j" => $i,
			"args" => array()
		);
	}

	// emptyparser is nullable.
	public function evaluateNullability() {
		return true;
	}
}
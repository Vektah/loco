<?php

namespace loco\exception;

use Exception;

// Occurs when any parser fails to parse what it's supposed to
// parse. Usually non-fatal and almost always caught

class ParseFailureException extends Exception {
	public function __construct($message, $i, $string, $code = 0, Exception $previous = null) {
		$message .= " at position ".var_export($i, true)." in string ".var_export($string, true);
		parent::__construct($message, $code);
	}

	/**
	 * Workaround until php 5.5's class operator is implemented.
	 *
	 * @see http://www.php.net/manual/en/language.oop5.basic.php#language.oop5.basic.class.class
	 */
	const _CLASS = __CLASS__;
}
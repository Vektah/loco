<?php

namespace loco\test\parser;

use loco\exception\GrammarException;
use loco\exception\ParseFailureException;
use loco\parser\LazyAltParser;
use loco\parser\StringParser;
use \PHPUnit_Framework_TestCase as TestCase;

class LazyAltParserTest extends TestCase {
	/** @var StringParser */
	private $parser;

	public function setUp() {
		$this->parser = new LazyAltParser(array(
			new StringParser("abc"),
			new StringParser("ab"),
			new StringParser("a")
		));
	}

	public function testNonMatchingString() {
		$this->setExpectedException(ParseFailureException::_CLASS);
		$this->parser->match('0', 1);
	}

	public function testMatchingStrings() {
		$this->assertEquals(array("j" => 2, "value" => "a"  ), $this->parser->match("0a",    1));
		$this->assertEquals(array("j" => 3, "value" => "ab" ), $this->parser->match("0ab",   1));
		$this->assertEquals(array("j" => 4, "value" => "abc"), $this->parser->match("0abc",  1));
		$this->assertEquals(array("j" => 4, "value" => "abc"), $this->parser->match("0abcd", 1));
	}

	public function test_empty_parser(){
		$this->setExpectedException(GrammarException::_CLASS);
		new LazyAltParser(array());
	}
}
 
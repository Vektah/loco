<?php
	{
		print("7A\n");
		$parser = new LazyAltParser(
			array(
				new StringParser("abc"),
				new StringParser("ab"),
				new StringParser("a")
			)
		);
		try {
			$parser->match("0", 1);
			var_dump(false);
		} catch(ParseFailureException $e) {
			var_dump(true);
		}
		var_dump($parser->match("0a",    1) === array("j" => 2, "value" => "a"  ));
		var_dump($parser->match("0ab",   1) === array("j" => 3, "value" => "ab" ));
		var_dump($parser->match("0abc",  1) === array("j" => 4, "value" => "abc"));
		var_dump($parser->match("0abcd", 1) === array("j" => 4, "value" => "abc"));
		
		print("7B\n");
		try {
			new LazyAltParser(array());
			var_dump(false);
		} catch(GrammarException $e) {
			var_dump(true);
		}
	}

	{
		print("8B\n");
		$parser = new ConcParser(
			array(
				new RegexParser("#^a*#"),
				new RegexParser("#^b+#"),
				new RegexParser("#^c*#")
			)
		);
		try {
			$parser->match("", 0);
			var_dump(false);
		} catch(ParseFailureException $e) {
			var_dump(true);
		}
		try {
			$parser->match("aaa", 0);
			var_dump(false);
		} catch(ParseFailureException $e) {
			var_dump(true);
		}
		var_dump($parser->match("b",       0) === array("j" => 1, "value" => array("", "b", "")));
		var_dump($parser->match("aaab",    0) === array("j" => 4, "value" => array("aaa", "b", "")));
		var_dump($parser->match("aaabb",   0) === array("j" => 5, "value" => array("aaa", "bb", "")));
		var_dump($parser->match("aaabbbc", 0) === array("j" => 7, "value" => array("aaa", "bbb", "c")));
	}

	{
		print("10B\n");
		$parser = new GreedyMultiParser(
			new StringParser("a"), 0, null
		);
		var_dump($parser->match("",    0) === array("j" => 0, "value" => array()));
		var_dump($parser->match("a",   0) === array("j" => 1, "value" => array("a")));
		var_dump($parser->match("aa",  0) === array("j" => 2, "value" => array("a", "a")));
		var_dump($parser->match("aaa", 0) === array("j" => 3, "value" => array("a", "a", "a"))); 
	}

	// Test behaviour when given ambiguous inner parser
	{
		print("10C\n");
		$parser = new GreedyMultiParser(
			new LazyAltParser(
				array(
					new StringParser("ab"),
					new StringParser("a")
				)
			),
			0,
			null
		);
		var_dump($parser->match("",   0) === array("j" => 0, "value" => array()));
		var_dump($parser->match("a",  0) === array("j" => 1, "value" => array("a")));
		var_dump($parser->match("aa", 0) === array("j" => 2, "value" => array("a", "a")));
		var_dump($parser->match("ab", 0) === array("j" => 2, "value" => array("ab")));
	}
	
	{
		print("10D\n");
		$parser = new GreedyMultiParser(
			new LazyAltParser(
				array(
					new StringParser("aa"),
					new StringParser("a")
				)
			),
			0,
			null
		);
		var_dump($parser->match("",   0) === array("j" => 0, "value" => array()));
		var_dump($parser->match("a",  0) === array("j" => 1, "value" => array("a")));
		var_dump($parser->match("aa", 0) === array("j" => 2, "value" => array("aa")));
	}

	{
		print("10E\n");
		$parser = new GreedyMultiParser(
			new StringParser("a"), 0, 1
		);
		var_dump($parser->match("", 0) === array("j" => 0, "value" => array()));
		var_dump($parser->match("a", 0) === array("j" => 1, "value" => array("a")));
	}
	
	{
		print("10F\n");
		$parser = new GreedyMultiParser(new StringParser("f"), 0, 0);
		var_dump($parser->match("", 0) === array("j" => 0, "value" => array()));
		var_dump($parser->match("f", 0) === array("j" => 0, "value" => array()));
		$parser = new GreedyMultiParser(new StringParser("f"), 0, 1);
		var_dump($parser->match("", 0) === array("j" => 0, "value" => array()));
		var_dump($parser->match("f", 0) === array("j" => 1, "value" => array("f")));
		var_dump($parser->match("ff", 0) === array("j" => 1, "value" => array("f")));
		$parser = new GreedyMultiParser(new StringParser("f"), 1, 2);
		try { $parser->match("", 0); var_dump(false); } catch(ParseFailureException $e) { var_dump(true); }
		var_dump($parser->match("f", 0) === array("j" => 1, "value" => array("f")));
		var_dump($parser->match("ff", 0) === array("j" => 2, "value" => array("f", "f")));
		var_dump($parser->match("fff", 0) === array("j" => 2, "value" => array("f", "f")));
		$parser = new GreedyMultiParser(new StringParser("f"),	1, null);
		try { $parser->match("", 0); var_dump(false); } catch(ParseFailureException $e) { var_dump(true); }
		var_dump($parser->match("f", 0) === array("j" => 1, "value" => array("f")));
		var_dump($parser->match("ff", 0) === array("j" => 2, "value" => array("f", "f")));
		var_dump($parser->match("fff", 0) === array("j" => 3, "value" => array("f", "f", "f")));
		var_dump($parser->match("ffg", 0) === array("j" => 2, "value" => array("f", "f")));
	}
	
	// regular Grammar
	{
		print("11\n");
		$grammar = new Grammar(
			"<A>",
			array(
				"<A>" => new EmptyParser()
			)
		);
		try {
			$grammar->parse("a");
			var_dump(false);
		} catch(ParseFailureException $e) {
			var_dump(true);
		}
		var_dump($grammar->parse("") === null);
	}

	// disallow GreedyMultiParsers with unbounded limits which can consume ""
	{
		print("12A\n");
		try {
			$grammar = new Grammar(
				"<S>",
				array(
					"<S>" => new GreedyMultiParser("<A>", 7, null),
					"<A>" => new EmptyParser()
				)
			);
			var_dump(false);
		} catch(GrammarException $e) {
			var_dump(true);
		}
		try {
			$grammar = new Grammar(
				"<S>",
				array(
					"<S>" => new GreedyStarParser("<A>"),
					"<A>" => new GreedyStarParser("<B>"),
					"<B>" => new EmptyParser()
				)
			);
			var_dump(false);
		} catch(GrammarException $e) {
			var_dump(true);
		}
	}

	// no parser for the root
	{
		print("13B\n");
		try {
			$grammar = new Grammar("<A>", array());
			var_dump(false);
		} catch(GrammarException $e) {
			var_dump(true);
		}
	}
	
	// left-recursion
	{
		print("13G\n");

		// obvious
		try {
			$grammar = new Grammar(
				"<S>",
				array(
					"<S>" => new ConcParser(array("<S>"))
				)
			);
			var_dump(false);
		} catch(GrammarException $e) {
			var_dump(true);
		}

		// more advanced (only left-recursive because <B> is nullable)
		try {
			$grammar = new Grammar(
				"<A>",
				array(
					"<A>" => new LazyAltParser(
						array(
							new StringParser("Y"),
							new ConcParser(
								array("<B>", "<A>")
							)
						)
					),
					"<B>" => new EmptyParser()
				)
			);
			var_dump(false);
		} catch(GrammarException $e) {
			var_dump(true);
		}
		
		// Even more complex (this specifically checks for a bug in the
		// original Loco left-recursion check).
		// This grammar is left-recursive in A -> B -> D -> A
		try {
			$grammar = new Grammar(
				"<A>",
				array(
					"<A>" => new ConcParser(array("<B>")),
					"<B>" => new LazyAltParser(array("<C>", "<D>")),
					"<C>" => new ConcParser(array(new StringParser("C"))),
					"<D>" => new LazyAltParser(array("<C>",  "<A>"))
				)
			);
			var_dump(false);
		} catch(GrammarException $e) {
			var_dump(true);
		}
	}
?> 
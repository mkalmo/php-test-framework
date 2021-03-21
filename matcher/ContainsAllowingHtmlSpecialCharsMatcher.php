<?php

namespace stf\matcher;

class ContainsAllowingHtmlSpecialCharsMatcher extends AbstractMatcher {

    private string $needle;

    public function __construct(string $needle) {
        $this->needle = $needle;
    }

    public function matches($actual) : bool {
        $needle1 = $this->needle;
        $needle2 = htmlspecialchars($needle1, ENT_NOQUOTES);
        $needle3 = htmlspecialchars($needle1, ENT_QUOTES | ENT_HTML5);
        $needle4 = htmlspecialchars($needle1, ENT_QUOTES | ENT_HTML401);

        return $this->contains($actual, $needle1)
            || $this->contains($actual, $needle2)
            || $this->contains($actual, $needle3)
            || $this->contains($actual, $needle4);
    }

    private function contains(string $text, string $needle) : bool {
        return strpos($text, $needle) !== false;
    }

    public function getError(
        $actual, ?string $message = null) : MatcherError {

        return new MatcherError(ERROR_C06,
            sprintf("Should contain string '%s' but did not found it",
                $this->needle));
    }
}


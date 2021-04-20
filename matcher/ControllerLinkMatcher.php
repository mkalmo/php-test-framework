<?php

namespace stf\matcher;

class ControllerLinkMatcher extends AbstractMatcher {

    public function matches($actual) : bool {

        $pattern = '/^(index\.php)?\??[-=&\w]*$/';

        return preg_match($pattern, $actual);
    }

    public function getError(
        $actual, ?string $message = null) : MatcherError {

        $message = 'Front Controller pattern expects all links '
            . 'to be in ?key1=value1&key2=... format. But this link was: ' . $actual;

        return new MatcherError(ERROR_W20, $message);
    }
}


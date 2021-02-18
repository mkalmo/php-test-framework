<?php

namespace stf;

abstract class AbstractMatcher {

    public abstract function matches($actual) : bool;

    public abstract function getError(
        $actual, ?string $message = null) : MatcherError;

}


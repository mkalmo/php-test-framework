<?php

namespace stf\browser;

use RuntimeException;

class Path2 {

    private array $parts;
    private bool $isAbsolute;
    private bool $endsWithSlash;

    public function __construct(string $path) {
        $this->isAbsolute = !!preg_match('/^\//', $path);
        $this->parts = array_filter(explode('/', $path));
        $this->endsWithSlash = !!preg_match('/\/$/', $path);
    }

    public function isAbsolute() : bool {
        return $this->isAbsolute;
    }

    public function asString() : string {
        return ($this->isAbsolute() ? '/' : '')
             . implode('/', $this->parts)
             . ($this->endsWithSlash && !$this->isRoot() ? '/' : '');
    }

    public function cd(Path2 $other) : Path2 {
        if ($other->isAbsolute()) {
            return $this->normalize($other);
        }

        $result = new Path2('');
        $result->isAbsolute = $this->isAbsolute;
        $result->parts = array_merge($this->parts, $other->parts);

        return $this->normalize($result);
    }

    private static function normalize(Path2 $path) : Path2 {
        $newParts = [];
        foreach ($path->parts as $part) {
            if ($part === '.') {
                continue;
            } else if ($part === '..') {
                array_pop($newParts);
            } else {
                array_push($newParts, $part);
            }
        }

        $result = new Path2('');
        $result->isAbsolute = $path->isAbsolute;
        $result->parts = $newParts;
        return $result;
    }

    private function clone() : Path2 {
        $path = new Path2('');
        $path->isAbsolute = true;
        $path->parts = $this->parts;
        return $path;
    }

    public function asAbsolute() : Path2 {
        $path = $this->clone();
        $path->isAbsolute = true;
        return $path;
    }

    public function isRoot() : bool {
        return $this->isAbsolute()
            && empty($this->parts);
    }

    public function isEmpty() : bool {
        return empty($this->parts);
    }

}




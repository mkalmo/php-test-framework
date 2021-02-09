<?php

namespace stf;

use RuntimeException;

class Path {

    private array $parts = [];
    private bool $isAbsolute = false;
    private bool $isDirectory = false;

    public function __construct(?string $path) {
        if (empty($path)) {
            return;
        }

        $this->isAbsolute = preg_match('/^\//', $path) ? true : false;
        $this->isDirectory = preg_match('/[.\/]$/', $path) ? true : false;

        $this->parts = array_filter(explode('/', $path));
    }

    public function isAbsolute() : bool {
        return $this->isAbsolute;
    }

    public function isDirectory() : bool {
        return $this->isDirectory;
    }

    public function asString() : string {
        $result = '';
        if ($this->isAbsolute()) {
            $result .= '/';
        }

        $result .= implode('/', $this->parts);

        if ($this->isDirectory() && !empty($this->parts)) {
            $result .= '/';
        }

        return $result;
    }

    public function removeFilePart() : Path {
        if (empty($this->parts) || $this->isDirectory) {
            return $this;
        }

        $parts = $this->parts;
        array_pop($parts);
        $newPath = new Path(null);
        $newPath->isDirectory = true;
        $newPath->isAbsolute = $this->isAbsolute;
        $newPath->parts = $parts;

        return $newPath;
    }

    public function extend(Path $other) : Path {
        if ($other->isAbsolute()) {
            return $other;
        }

        $result = new Path(null);
        $result->isAbsolute = $this->isAbsolute;
        $result->parts = array_merge($this->parts, $other->parts);
        $result->isDirectory = $other->isBlank()
            ? $this->isDirectory
            : $other->isDirectory;

        return $result;
    }

    public function asAbsolute() : Path {
        if ($this->isBlank()) {
            return new Path(null);
        }

        $path = new Path(null);
        $path->isAbsolute = true;
        $path->isDirectory = $this->isDirectory;
        $path->parts = $this->parts;
        return $path;
    }

    public function normalize() : Path {
        if (!$this->isAbsolute() && !$this->isBlank()) {
            throw new RuntimeException("must be absolute or blank to normalize");
        }

        $newParts = [];
        foreach ($this->parts as $part) {
            if ($part === '.') {
                continue;
            } else if ($part === '..') {
                array_pop($newParts);
            } else {
                array_push($newParts, $part);
            }
        }

        $path = new Path(null);
        $path->isAbsolute = $this->isAbsolute;
        $path->isDirectory = $this->isDirectory;
        $path->parts = $newParts;
        return $path;
    }

    public function isRoot() : bool {
        return $this->isAbsolute()
            && $this->isDirectory()
            && empty($this->parts);
    }

    private function isBlank() : bool {
        return !$this->isAbsolute()
            && !$this->isDirectory()
            && empty($this->parts);
    }

}




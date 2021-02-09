<?php

namespace stf;

use RuntimeException;

require_once 'Path.php';

class Url {

    private ?string $host;
    private Path $path;
    private string $queryString = '';

    public function __construct(string $url) {
        $this->host = $this->getHost($url);
        if (empty($this->host)) {
            throw new RuntimeException('no host part in url: ' . $url);
        }
        $this->path = new Path($this->getPath($url));
    }

    public function asString() : string {
        return $this->host . $this->path->asAbsolute()->asString();
    }

    private function isAbsolute(?string $url) {
        return !empty($this->getHost($url));
    }

    public function navigateTo(?string $destination) : Url {
        if ($this->isAbsolute($destination)) {
            return new Url($destination);
        }

        $thisPath = $this->path;

        if (!empty($destination)) {
            $thisPath = $thisPath->removeFilePart();
        }

        $newPath = $thisPath->extend(new Path($destination));

        $newUrl = new Url($this->host . $newPath->asAbsolute()->asString());

        return $newUrl->normalize();
    }

    public function normalize() : Url {

        $newUrl = new Url($this->host);

        $newUrl->path = $this->path->normalize();

        if ($newUrl->path->isRoot()) {
            $newUrl->path = new Path('');
        }

        $newUrl->queryString = $this->queryString;

        return $newUrl;
    }

    private function getHost($fullUrl) : ?string {
        $hostRegex = '/^https?:\/\/\w+(:\d+)?/';
        preg_match($hostRegex, $fullUrl, $matches);
        return $matches[0] ?? '';
    }

    private function getPath($fullUrl) : ?string {
        $host = $this->getHost($fullUrl);

        if ($host === '') {
            return $fullUrl;
        }

        return str_replace($host, '', $fullUrl);
    }

}



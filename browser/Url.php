<?php

namespace stf\browser;

use \RuntimeException;

class Url {

    private ?string $host;
    private Path $path;
    private string $queryString;

    public function __construct(string $url) {
        $this->host = $this->getHost($url);
        if (empty($this->host)) {
            throw new RuntimeException('no host part in url: ' . $url);
        }

        $parts = explode('?', $url, 2);
        $this->queryString = $parts[1] ?? '';

        $this->path = new Path($this->getPath($url));
    }

    public function asString() : string {
        $result = $this->host . $this->path->asAbsolute()->asString();

        return $this->queryString
            ? $result . '?' . $this->queryString
            : $result;
    }

    private function isAbsolute(?string $url) : bool {
        return !empty($this->getHost($url));
    }

    public function navigateTo(?string $destination) : Url {
        if (empty($destination)) {
            return $this->normalize();
        } else if ($this->isAbsolute($destination)) {
            return new Url($destination);
        }

        $path = $this->path->removeFilePart();

        $newPath = $path->extend(new Path($destination));

        $newUrl = new Url($this->host . $newPath->asAbsolute()->asString());

        return $newUrl->normalize();
    }

    public function normalize() : Url {

        $newUrl = new Url($this->host);

        $newUrl->path = $this->path->normalize();

        if ($newUrl->path->isRoot() && empty($this->getQueryString())) {
            $newUrl->path = new Path('');
        }

        $newUrl->queryString = $this->queryString;

        return $newUrl;
    }

    private function getHost($fullUrl) : ?string {
        $hostRegex = '/^https?:\/\/[\w.]+(:\d+)?/';
        preg_match($hostRegex, $fullUrl, $matches);
        return $matches[0] ?? '';
    }

    private function getPath($fullUrl) : ?string {
        $host = $this->getHost($fullUrl);

        if ($host === '') {
            return $fullUrl;
        }

        $fullUrl = str_replace($host, '', $fullUrl);

        $parts = explode('?', $fullUrl, 2);

        return $parts[0] ?? '';
    }

    public function getQueryString() : string {
        return $this->queryString;
    }

    public function addRequestParameter(string $key, string $value) {
        if ($this->queryString) {
            $this->queryString .= '&';
        }

        $this->queryString .= "$key=$value";
    }
}

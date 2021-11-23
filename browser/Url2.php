<?php

namespace stf\browser;

use RuntimeException;

class Url2 {

    private string $host;
    private Path2 $path;
    private string $file;
    private string $queryString;

    public function __construct(string $url) {
        $this->extractHost($url);
        $this->extractPath($url);
        $this->extractQueryString($url);
    }

    private function extractHost($url) : void {
        $scheme = parse_url($url, PHP_URL_SCHEME) ?? '';
        $host = parse_url($url, PHP_URL_HOST) ?? '';
        $port = parse_url($url, PHP_URL_PORT) ?? '';

        $this->host = $scheme
                    . ($scheme ? '://' : '')
                    . $host
                    . ($port ? ':' : '')
                    . $port;
    }

    private function extractPath($url) : void {
        $path = parse_url($url, PHP_URL_PATH) ?? '';

        $pathRegex = '/(.*\/)?(.*)/';
        preg_match($pathRegex, $path, $matches);

        $this->path = new Path2($matches[1]);
        $this->file = $matches[2] === '.' ? '' : $matches[2];
    }

    private function extractQueryString($url) : void {
        $query = parse_url($url, PHP_URL_QUERY) ?? '';
        $fragment = parse_url($url, PHP_URL_FRAGMENT) ?? '';

        $this->queryString =
               ($query ? '?' . $query : '')
             . ($fragment ? '#' . $fragment : '');
    }

    public function asString() : string {
        if ($this->host !== ''
            && $this->path->isRoot()
            && $this->file === ''
            && $this->queryString === '') {

            return $this->host;
        }

        return $this->host
             . $this->path->asString()
             . $this->file
             . $this->queryString;
    }

    private function fromParts(string $host, Path2 $path, string $queryString) : string {
        $path = $path->isEmpty() && empty($queryString)
            ? ''
            : $path->asAbsolute()->asString();

        return $host . $path . ($queryString ? '?' : '') . $queryString;
    }

    private function withHost() : bool {
        return $this->host !== '';
    }

    private function isEmpty() : bool {
        return $this->host === ''
            && $this->path->isEmpty()
            && $this->queryString === '';
    }

    private function isParametersOnly(?string $url) : bool {
        return substr(trim($url), 0, 1) === '?';
    }

    public function navigateTo(string $destination) : Url2 {
        $dest = new Url2($destination);

        if ($dest->withHost()) {
            return new Url2($destination);
        } else if ($dest->isEmpty()) {
            return $this;
        }


//        else if ($this->isParametersOnly($destination)) {
//            return new Url2($this->fromParts(
//                $this->host, $this->path, substr($destination, 1)));
//        }

        // $path = $this->path->removeFilePart();

        // $newPath = $path->extend(new Path2($destination));

        //$newUrl = new Url2($this->host . $newPath->asAbsolute()->asString());

        //return $newUrl->normalize();

        $newUrl = new Url2('');
        $newUrl->host = $this->host;
        $newUrl->path = $this->path->cd($dest->path);
        $newUrl->file = $dest->file ?: $this->file;
        $newUrl->queryString = $dest->queryString;

        return $newUrl;
    }

    public function normalize() : Url2 {

//        $newUrl = new Url2($this->host);
//
//        $newUrl->path = $this->path->normalize();
//
//        if ($newUrl->path->isRoot() && empty($this->getQueryString())) {
//            $newUrl->path = new Path('');
//        }
//
//        $newUrl->queryString = $this->queryString;

        return '';
    }

    public function getQueryString() : string {
        return $this->queryString;
    }

    public function addRequestParameter(string $key, string $value) {
        if ($this->queryString) {
            $this->queryString .= '&';
        } else {
            $this->queryString .= '?';
        }

        $this->queryString .= "$key=$value";
    }
}

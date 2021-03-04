<?php

spl_autoload_register(function ($className) {
    $parts = explode('\\', $className);

    $basePath = $parts[0] === 'tplLib'
        ? __DIR__ . '/parser'
        : __DIR__ . '';

    unset($parts[0]);

    $filePath = sprintf('%s/%s.php', $basePath, implode('/', $parts));

    require_once $filePath;
});

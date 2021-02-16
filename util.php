<?php

namespace stf;

function mapAsString(array $map) : string {
    $parts = [];
    foreach ($map as $key => $value) {
        $parts[] = sprintf("%s='%s'", $key, $value);
    }

    return implode(', ', $parts);
}

function asString($value) : string {
    if (gettype($value) === 'boolean') {
        return $value ? 'true' : 'false';
    } else if (gettype($value) === 'NULL') {
        return 'NULL';
    } else if ($value === '') {
        return '<EMPTY STRING>';
    }

    return $value;
}

<?php

namespace stf;

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

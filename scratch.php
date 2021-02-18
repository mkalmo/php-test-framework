<?php

namespace stf;

require_once 'browser/page/TextField.php';

$type = TextField::class;
$type2 = AbstractInput::class;
$field = new TextField('t1', '1');
var_dump(get_class($field) === $type);
var_dump(is_subclass_of($field, $type2));


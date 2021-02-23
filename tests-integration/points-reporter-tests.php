<?php

include_once '../public-api.php';

function test1() {
    fail('', '');
}
function test2() {
    fail('', '');
}
function test3() {
    fail('', '');
}
function test4() {}

stf\runTests(new stf\PointsReporter([2 => 1, 3 => 2, 4 => 5]));
<?php
set_include_path('../../' . PATH_SEPARATOR . get_include_path());

require 'Services/PostDanmark.php';

$service = new Services_PostDanmark('TS123456789DK');
$r = $service->query();
foreach ($r as $re) {
    print($re['date'] . $re['time'] . $re['text']);
};


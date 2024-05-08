<?php
require_once(__DIR__ . "/vendor/autoload.php");


use App\ComagicVisitors;

$input = file_get_contents("php://input");
$cv = new ComagicVisitors($input);
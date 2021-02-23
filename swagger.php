<?php
require("vendor/autoload.php");
$openapi = \OpenApi\scan('/var/www/html/app');
header('Content-Type: application/x-yaml');
echo $openapi->toYaml();

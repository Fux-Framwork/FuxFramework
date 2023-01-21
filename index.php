<?php
require_once __DIR__ . '/php/FuxFramework/bootstrap.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Accept, Authorization, X-Requested-With, Application");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, HEAD");

include_once './php/FuxFramework/Routing/Request.php';
include_once './php/FuxFramework/Routing/Route.php';
include_once './php/FuxFramework/Routing/Router.php';
include_once './php/FuxFramework/helpers.php';


sanitize_get();
sanitize_post();
sanitize_request();

use Fux\Request;
use Fux\Router;

EmptyRequestFixService::fix();

$router = new Router(new Request());

/* Load external routes file */
foreach (rglob(__DIR__ . "/routes/*.php") as $filename) {
    include_once($filename);
}

/**
 * Load packages route files with the defined depth.
 * Default depth = 1
 * For example depth = 3 means that will be loaded all route files that match these following paths:
 * /app/Packages/{Folder}/Routes/*.php
 * /app/Packages/{Folder1}/{Folder2}/Routes/*.php
 * /app/Packages/{Folder1}/{Folder2}/{Folder3}/Routes/*.php
 */
for ($depth = 1; $depth <= 2; $depth++) {
    $pathSegments = str_repeat("/*", $depth);
    foreach (rglob(__DIR__ . "/app/Packages$pathSegments/Routes/*.php") as $filename) {
        include_once($filename);
    }
}
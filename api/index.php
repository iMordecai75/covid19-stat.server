<?php
/*METHOD GET AND GET WITH GET ID*/

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT,DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
header("Content-Type: application/json; charset=UTF-8");
require_once 'classes/ApiResponse.php';
require_once 'utilities/connection.php';

$response = new ApiResponse();
$endpoint = '/dpc-covid19-ita-andamento-nazionale.json';

if (isset($_GET['task']) && $_GET['task'] == 'latest') {
    $endpoint = '/dpc-covid19-ita-andamento-nazionale-latest.json';
}
try {
    $result = Connection::cURLdownload($endpoint);

    $response->status = 'OK';
    $response->items = json_decode($result);

    echo $response->toJson();
} catch (\Throwable $th) {
    $response->status = 'KO';
    $response->msg = $th->getMessage();

    echo $response->toJson();
}
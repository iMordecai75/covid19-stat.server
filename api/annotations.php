<?php
/*METHOD GET AND GET WITH GET ID*/

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT,DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
header("Content-Type: application/json; charset=UTF-8");
require_once 'connection.php';

try {
    $result = Connection::cURLdownload('/annotations.json', 'http://www.federicomasci.com/angular/covid19/data');

    echo $result;
} catch (\Throwable $th) {
    $result = array(
        'status' => 'KO',
        'error' => $th->getMessage()
    );
    echo json_encode($result);
}

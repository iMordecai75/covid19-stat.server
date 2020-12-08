<?php
/*METHOD GET AND GET WITH GET ID*/

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT,DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
header("Content-Type: application/json; charset=UTF-8");
require_once 'connection.php';
require_once 'dbconn.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    $dbh = new PDO("mysql:host = $host;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $result = array(
        'status' => 'KO',
        'error' => $e->getMessage()
    );
    echo json_encode($result);
    die();
}

switch($method) {
    case 'POST':
        $value = $_POST['Annotation_sValue'];
        $content = $_POST['Annotation_sContent'];        
        try {
            $query = "INSERT INTO tblAnnotations (Annotation_sValue, Annotation_sContent) VALUES (?, ?)";
            $stmt = $dbh->prepare($query);
            $stmt->execute([$value, $content]);
            $id = $dbh->lastInsertId();
            $tmp = array();
            $tmp['Annotation_iId'] = $id;
            $tmp['Annotation_sValue'] = $value;
            $tmp['Annotation_sContent'] = $content;

            echo json_encode($tmp);
        } catch (PDOException $th) {
            $result = array(
                'status' => 'KO',
                'error' => $th->getMessage()
            );
            echo json_encode($result);            
        }
    break;
    case 'PATCH':
    break;
    case 'DELETE':
    break;
    default:
        if (isset($_GET['task']) && $_GET['task'] == 'backend') {
            try {
                $query = "SELECT Annotation_iId, Annotation_sValue, Annotation_sContent FROM tblAnnotations";

                $stmt = $dbh->prepare($query);
                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode($rows);
            } catch (PDOException $th) {
                $result = array(
                    'status' => 'KO',
                    'error' => $th->getMessage()
                );
                echo json_encode($result);
            }
        } else {
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
        }

    break;
}
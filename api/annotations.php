<?php
/*METHOD GET AND GET WITH GET ID*/

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT,DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
header("Content-Type: application/json; charset=UTF-8");
require_once 'utilities/connection.php';
require_once 'utilities/dbconn.php';
require_once 'utilities/utilities.php';

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
        $token = getBearerToken();
        try {
            $query = "SELECT User_iId FROM tblUsers WHERE User_sToken = ?";
            $stmt = $dbh->prepare($query);
            $stmt->execute([$token]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            $result = array(
                'status' => 'KO',
                'error' => $th->getMessage()
            );
            echo json_encode($result);
        }
        if($row['User_iId'] > 0) {
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
        } else {
            $result = array(
                'status' => 'KO',
                'error' => 'Token errato o mancante'
            );
            echo json_encode($result);
        }
    break;
    case 'PATCH':
    break;
    case 'DELETE':
    break;
    case 'GET':
        try {
            $query = "SELECT Annotation_iId, Annotation_sValue, Annotation_sContent FROM tblAnnotations";

            $stmt = $dbh->prepare($query);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $th) {
            $result = array(
                'status' => 'KO',
                'error' => $th->getMessage()
            );
            echo json_encode($result);
        }
        if (isset($_GET['task']) && $_GET['task'] == 'backend') {
            echo json_encode($rows);
        } else {
            $result = array();
            foreach($rows as $row) {
                $tmp = new stdClass();
                $tmp->type = "line";
                $tmp->mode = "vertical";
                $tmp->scaleID = "x-axis-0";
                $tmp->value = $row['Annotation_sValue'];
                $tmp->borderColor = "black";
                $tmp->borderWidth = 2;
                $tmp->label = new stdClass();
                $tmp->label->enabled = true;
                $tmp->label->fontColor = "orange";
                $tmp->label->content = $row['Annotation_sContent'];

                $result[] = $tmp;
            }

            echo json_encode($result);
        }

    break;
}
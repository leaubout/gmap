<?php 

require_once '../../vendor/autoload.php';
use \ForceUTF8\Encoding;

$action = $_REQUEST['action'];
switch ($action){
    case "upload" : 
         uploadCsv();
         break;
    case "loadAddresses" :
        loadAddresses();
        break;
    case "delete":
        $id = (int)$_GET['id'];
        deleteAddress($id);
        break;
    case "save":
        saveAddress();
        break;
    case "loadAddress":
        $id = (int)$_GET['id'];
        loadAddress($id);
        break;        
}
/**
 * structure CSV : 
 * [0] => Nom
 * [1] => Description
 * [2] => Adresse
 * [3] => URL 
 */
function uploadCsv(){
    
    $pdo = dbConnect();
    
    // récupération et formatage du fichier .csv
    $data = $_POST['data'];
    $data = str_replace("\r\n", PHP_EOL, $data);
    //$charset = mb_detect_encoding($data);
    //echo $charset;exit;
    $data = Encoding::fixUTF8($data);
    
    $data = explode(PHP_EOL, $data);
    
    $i = 0;
    
    $sql = "INSERT INTO coords
        (coords_nom, coords_desc, coords_adresse, coords_url)
        VALUES (:nom, :desc, :adresse, :url)";
    $stmt = $pdo->prepare($sql);
    
    foreach($data as $line){
        $entry = str_getcsv($line,";");
        if (count($entry) != 4){
            continue;
        }
        $stmt->bindParam(':nom',$entry[0]);
        $stmt->bindParam(':desc',$entry[1]);
        $stmt->bindParam(':adresse',$entry[2]);
        $stmt->bindParam(':url',$entry[3]);
        try{
            $stmt->execute();
            $i++;
        }catch(Exception $e){
            continue;
        }
    }
    echo $i;
}

function loadAddresses(){
    $pdo = dbConnect();
    
    $sql = " SELECT coords_id, coords_nom, coords_desc, coords_adresse, coords_url
        FROM coords";
    $stmt = $pdo->query($sql);
    $result = $stmt->fetchAll();
    //print_r($result);
    foreach ($result as &$line){
        $id = $line[0];
        $line[] = '<a href="#" data-id="' . $id . '" data-action="edit">Editer</a> '.
        '- <a href="#" data-id = "' . $id . '" data-action="delete">Supprimer</a>';
        array_shift($line);
    }
    $response = array('data' => $result);
    echo json_encode($response);
}

function deleteAddress($id){
    $pdo = dbConnect();
    $sql = "DELETE FROM coords WHERE coords_id = ?";
    $stmt = $pdo->prepare($sql);
    echo $stmt->execute(array($id));
};

function saveAddress(){
    $data['id'] = (int) $_POST['id'];
    $data['nom'] = (string) $_POST['nom'];
    $data['desc'] = (string) $_POST['description'];
    $data['adresse'] = (string) $_POST['adresse'];
    $data['url'] = (string) $_POST['url'];
    
    $pdo = dbConnect();
    
    if ($data['id'] === 0){
        $sql = "INSERT INTO coords (coords_nom, coords_desc, coords_adresse, coords_url) "
            ."VALUES (:nom, :desc, :adresse, :url)";
        $stmt = $pdo->prepare($sql);
        unset($data['id']);
        try {
            $result = $stmt->execute($data);
            echo $result;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    } else {
        $sql = "UPDATE coords 
                SET coords_nom = :nom,
                    coords_desc = :desc, 
                    coords_adresse = :adresse, 
                    coords_url = :url
                WHERE coords_id = :id";
                $stmt = $pdo->prepare($sql);
                $result = $stmt->execute($data);    
                echo $result;
    }    

}

function loadAddress($id){
    $pdo = dbConnect();
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    $sql = "SELECT *
        FROM coords
        WHERE coords_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $result = $stmt->fetch();
    echo json_encode($result);
}    

function dbConnect(){
    $dsn = 'mysql:dbname=project;host=localhost';
    $user = 'project';
    $password = '0000';
   
    $pdo = new PDO(
        $dsn, 
        $user, 
        $password,
        array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES UTF8"));
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_NUM);
    return $pdo;
}

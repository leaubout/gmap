<?php 

require '../../vendor/autoload.php';
use \ForceUTF8\Encoding;

require_once 'app/Request.php';
require_once 'app/Db.php';
require_once 'app/Address.php';
try{
    $request = new Request;
    $db = new Db('mysql', 'localhost', 'project', 'root', '0000');
    $address = new Address($db);
    // Vérifie AJAX 
    if (!$request->isXhr()) {
        echo 'BAD METHOD';
        exit(0);
    }
    $action = $request->getParam('action');
    
    switch($action) {
        case "upload" :
            echo $address->uploadCsv(
                $request->getParam('data')
            );
            break;
        case "loadAddresses" :
            echo $address->fetchAll();
            break;
        case "loadAddress" :
            echo $address->find($request->getParam('id'));
            break;
        case "delete" :
            echo $address->delete($request->getParam('id'));  
            break;
        case "save" :
            echo $address->save(array(
                'id' => (int) $request->getParam('id'),
                'nom' => (string) $request->getParam('nom'),
                'desc' => (string) $request->getParam('description'),
                'adresse' => (string) $request->getParam('adresse'),
                'url' => (string) $request->getParam('url')    
            ));
            break;
    }
    
} catch(Exception $e) {
    echo $e->getMessage();
}



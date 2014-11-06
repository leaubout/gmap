<?php 

require '../../vendor/autoload.php';
use \ForceUTF8\Encoding;

require_once 'app/Request.php';
require_once 'app/Db.php';
require_once 'app/AddressMapper.php';
require_once 'app/AddressService.php';
try{
    $request = new Request;
    $db = new Db('mysql', 'localhost', 'project', 'root', '0000');
    $addressMapper = new AddressMapper($db);    // composition
    $addressService = new AddressService($addressMapper);
    // Vérifie AJAX 
    if (!$request->isXhr()) {
        echo 'BAD METHOD';
        exit(0);
    }
    $action = $request->getParam('action');
    
    switch($action) {
        case "upload" :
            echo $addressService->upload(
                $request->getParam('data')
            );
            break;
        case "loadAddresses" :
            echo $addressService->fetchAll();
            break;
        case "loadAddress" :
            echo $addressService->find($request->getParam('id'));
            break;
        case "delete" :
            echo $addressService->delete($request->getParam('id'));  
            break;
        case "save" :
            echo $addressService->save(
                (string) $request->getParam('nom'),
                (string) $request->getParam('description'),
                (string) $request->getParam('adresse'),
                (string) $request->getParam('url'),
                (int) $request->getParam('id')
            );
            break;
    }
    
} catch(Exception $e) {
    echo $e->getMessage();
}



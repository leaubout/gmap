<?php

require_once 'Address.php';

 class AddressMapper
 {

     /**
      * @var Db
      */
     private $dbAdapter;

     public function __construct($db){
         $this->dbAdapter = $db->getConnexion();
     }
     
     /**
      * @param number $id of address to find
      * @return Address
      */
     public function find($id){
         $this->dbAdapter->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
         $sql = "SELECT * FROM coords WHERE coords_id = :id";
         $stm = $this->dbAdapter->prepare($sql);
         $stm->bindParam(':id', $id);
         $req = $stm->execute();
         $row = $stm->fetch();

         if (!$row)return FALSE;
         
         return $this->rowToObject($row);
     }     
     
     /**
      * @return multitype: array of Address 
      */
     public function fetchAll(){
         $this->dbAdapter->setAttribute(
             PDO::ATTR_DEFAULT_FETCH_MODE,
             PDO::FETCH_ASSOC
         );
         $sql = "SELECT * FROM coords WHERE 1";
         $req = $this->dbAdapter->query($sql);
         $rowSet = $req->fetchAll();
         $addresses = array();
         foreach($rowSet as $row){
             $addresses[] = $this->rowToObject($row);
         }
         return $addresses;
     }
     
     /**
      * @param Address $address to be saved (insert or update)
      * @return boolean
      */
     public function save(Address $address){ // type hinting / typage objet
         // nouvel enregistrement ?
         if (0 === (int) $address->getId()){
             $sql = "INSERT INTO coords
               (coords_nom, coords_desc, coords_adresse, coords_url)
               VALUES (:coords_nom, :coords_desc, :coords_adresse, :coords_url)";
         // ou enregistrement existant
         } else {
             $sql = "UPDATE coords
               SET coords_nom = :coords_nom,
                   coords_desc = :coords_desc,
                   coords_adresse = :coords_adresse,
                   coords_url = :coords_url
               WHERE coords_id = :coords_id";             
         }
         $stm = $this->dbAdapter->prepare($sql);
         $row = $this->objectToRow($address);
         return (bool) $stm->execute($row);         
     }
     
     /**
      * @param number $id of address to delete
      */
     public function delete($id){
         $sql = "DELETE FROM coords WHERE coords_id = ?";
         $stm = $this->dbAdapter->prepare($sql);
         return $stm->execute(array($id));         
     }
     
     /**
      * @param multitype:array $row
      * @return Address
      */
     private function rowToObject($row){
         $address = new Address();
         // fluent interface // interface fluide pour les setters
         $address->setId($row['coords_id'])
            ->setNom($row['coords_nom'])
            ->setDescription($row['coords_desc'])
            ->setAdresse($row['coords_adresse'])
            ->setUrl($row['coords_url']);
         
         return $address;
     }
     
    /**
     * @param Address $address
     * @return multitype:array 
     */
    private function objectToRow(Address $address){
        $row = array();
        if (0 !== (int) $address->getId()){
            $row['coords_id'] = $address->getId();
        }
        $row['coords_nom'] = $address->getNom();
        $row['coords_desc'] = $address->getDescription();
        $row['coords_adresse'] = $address->getAdresse();
        $row['coords_url'] = $address->getUrl();
         
        return $row;
    }

     
 }
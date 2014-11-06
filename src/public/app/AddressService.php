<?php 

require_once 'Address.php';
class AddressService
{

    /**
     * @var AddressMapper
     */
    private $addressMapper;
    
    public function __construct(AddressMapper $addressMapper){
      $this->addressMapper = $addressMapper;
    }
    
    /**
     * inserts in Db addresses stored in a flat CSV file
     * @param string $csv CSV formatted addresses
     * @return number of lines inserted
     */
    public function upload($csv){
        
        // Transformation des fins de ligne au format LF
        $csv = str_replace("\r\n", PHP_EOL, $csv);
        $csv = explode(PHP_EOL, $csv);
        $i = 0;
        
        foreach($csv as $line) {
            
            $entry = str_getcsv($line, ";");
            if(count($entry) != 4) {
                continue;
            }
            
            $address = new Address();
            $address->setNom($entry[0])
                ->setDescription($entry[1])
                ->setAdresse($entry[2])
                ->setUrl($entry[3]);
            
            $this->addressMapper->save($address);
            $i++;
        }
        return $i;        
    }
    
    /**
     * fetches all addresses stored in Db
     * @return string JSON encoded addresses
     */
    public function fetchAll(){
        $addresses = $this->addressMapper->fetchAll();

        $response = array('data' =>array());
        foreach($addresses as $address){
            $response['data'][] = array(
                $line[] = $address->getNom(),
                $line[] = $address->getDescription(),
                $line[] = $address->getAdresse(),
                $line[] = $address->getUrl(),
                $line[] = '<a href="#" data-id="' . $address->getId() . '" data-action="edit"><i class="glyphicon glyphicon-pencil"></i></a>' .
                    ' <a href="#" data-id="' . $address->getId() . '" data-action="delete"><i class="glyphicon glyphicon-remove-circle"></i></a>'
            );
        }
        return json_encode($response);        
    }
    

    /**
     * fetches an address from Db for a given id
     * @param number $id
     * @return string JSON encoded address
     */
    public function find($id){
        $address = $this->addressMapper->find($id);
        $result = array(
          'coords_id' => $address->getId(),
          'coords_nom' => $address->getNom(),
          'coords_desc' => $address->getDescription(),
          'coords_adresse' => $address->getAdresse(),
          'coords_url' => $address->getUrl()
        );
        return json_encode($result);
    }
    
    /**
     * Inserts or updates an address in Db
     * @param string $nom
     * @param string $description
     * @param string $url
     * @param string $adresse
     * @param number $id
     * @return boolean
     */
    public function save($nom, $description, $adresse, $url, $id = 0){
        $address = new Address();
        if (0 !== (int) $id){
            $address->setId($id);
        }
        $address->setNom($nom)
            ->setDescription($description)
            ->setAdresse($adresse)
            ->setUrl($url);
        
        return $this->addressMapper->save($address);
    }
    
    /**
     * Deletes
     * @param unknown $id
     * @return boolean
     */
    public function delete($id){
        return $this->addressMapper->delete($id);
    }
    
    
}
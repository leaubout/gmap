<?php 
// super objet adresse
// envoyer les résultats
// et gérer la persistance dans la BD
// on le redécoupera ensuite
// bonnes pratiques objet : moins un objet fait de choses, mieux c'est.
class Address
{
    // injonction de dépendance ou composition
    private $dbAdapter;
    
    public function __construct($db)
    {
        if (!$db instanceof Db) {
            throw new InvalidArgumentException(
                "Le 1er parametre doit etre une instance de Db"
            );
        }
        $this->dbAdapter = $db->getConnexion();
    }
    
    /**
     * 
     * @param string $csvData CSV formated addresses
     * @return number inserted lines
     */
    public function uploadCsv($csvData)
    {
        // Transformation des fins de ligne au format LF
        $csvData = str_replace("\r\n", PHP_EOL, $csvData);
        $csvData = explode(PHP_EOL, $csvData);
        
        $sql = "INSERT INTO coords
           (coords_nom, coords_desc, coords_adresse, coords_url)
           VALUES (:nom, :desc, :adresse, :url)";
        
        $stm = $this->dbAdapter->prepare($sql);
        $i = 0;
        foreach($csvData as $line) {
            $entry = str_getcsv($line, ";");
            if(count($entry) != 4) {
                continue;
            }
            $stm->bindParam(':nom', $entry[0]);
            $stm->bindParam(':desc', $entry[1]);
            $stm->bindParam(':adresse', $entry[2]);
            $stm->bindParam(':url', $entry[3]);
            try {
                $stm->execute();
                $i++;
            } catch(Exception $e) {
                continue;
            }
        }
        return $i;
    }
    
    /**
     * 
     * @return string
     */
    public function fetchAll()
    {
        $this->dbAdapter->setAttribute(
            PDO::ATTR_DEFAULT_FETCH_MODE, 
            PDO::FETCH_NUM
        );
        $sql = "SELECT * FROM coords WHERE 1";
        $req = $this->dbAdapter->query($sql);
        $result = $req->fetchAll();
    
        foreach($result as &$line) {
            $id = $line[0];
            $line[] = '<a href="#" data-id="' . $id . '" data-action="edit"><i class="glyphicon glyphicon-pencil"></i></a>' .
                ' <a href="#" data-id="' . $id . '" data-action="delete"><i class="glyphicon glyphicon-remove-circle"></i></a>';
            array_shift($line);
        }
    
        $response = array('data' => $result);
        return json_encode($response);
    }
    
    /**
     * 
     * @param number $id of address to fetch
     * @return string JSON formated address
     */
    public function find($id)
    {
        $this->dbAdapter->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $sql = "SELECT *
            FROM coords
            WHERE coords_id = :id";
        $stm = $this->dbAdapter->prepare($sql);
        $stm->bindParam(':id', $id);
        $req = $stm->execute();
        $result = $stm->fetch();
        return json_encode($result);
    }

    /**
     * 
     * @param number $id of address to delete
     * @return boolean
     */
    function delete($id)
    {
        $sql = "DELETE FROM coords WHERE coords_id = ?";
        $stm = $this->dbAdapter->prepare($sql);
        return $stm->execute(array($id));
    }
    
    
    public function save($data)
    {
        if ($data['id'] === 0) {
            $sql = "INSERT INTO coords
               (coords_nom, coords_desc, coords_adresse, coords_url)
               VALUES (:nom, :desc, :adresse, :url)";
            $stm = $this->dbAdapter->prepare($sql);
    
            unset($data['id']);
            try {
                $stm->execute($data);
                return 'saved';
            } catch(Exception $e) {
                return $e->getMessage();
            }
        } else {
            $sql = "UPDATE coords
               SET coords_nom = :nom,
                   coords_desc = :desc,
                   coords_adresse = :adresse,
                   coords_url = :url
               WHERE coords_id = :id";
            $stm = $this->dbAdapter->prepare($sql);
            try {
                $stm->execute($data);
                return 'saved';
            } catch(Exception $e) {
                return $e->getMessage();
            }
        }
    }
}
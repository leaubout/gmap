<?php 

class Db
{
    /**
     * @var string
     */
    private $driver;
    /**
     * @var string
     */
    private $host;
    /**
     * @var string
     */
    private $dbname;
    /**
     * @var string
     */
    private $username;
    /**
     * @var string
     */
    private $password;
    /**
     * @var PDO
     */
    private $connexion;
    /**
     * @var number
     */
    private $port = 3306;
    
    static private $allowedDrivers = array('mysql');
    
    public function __construct(
    $driver, $host, $dbname, 
    $username, $password, $port = 3306)
    {
        if(!in_array($driver, self::$allowedDrivers)) {
            throw new InvalidArgumentException(
                "$driver n'est pas un driver PDO valide"
            );
        }
        $this->driver = $driver; 
        if ($host != "localhost" && !filter_var($host, FILTER_VALIDATE_IP)) {
            throw new InvalidArgumentException(
                "$host n'est pas un nom d'hôte PDO valide"
            );
        }
        $this->host = $host;
        $this->dbname = $dbname;
        $this->username = $username;
        $this->password = $password;
   }
   
   private function connect()
   {
       $dsn = $this->driver . ':dbname=' . $this->dbname . ';' .
              'host=' . $this->host;
       try {
        $this->connexion = new PDO($dsn, $this->username, $this->password);
       } catch(PDOException $e) {
           throw $e;
       }
   }
   
   private function isConnected()
   {
       return (bool) ($this->connexion instanceof PDO);
   }
   
   //Lazy loading = chargement tardif
   // pour ne pas surcharger la phase d'initialisation
   public function getConnexion()
   {
       if (!$this->isConnected()) {
           $this->connect();
       }
       return $this->connexion;
   }
}
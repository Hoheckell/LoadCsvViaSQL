<?php


class Loadfile{

    public $filepath = __DIR__."/csv_alterado.csv";
    public $connection;
    public $sql;
    private $username="root";
    private $passwd="123";
    private $database="loadfile";
    private $table="csvdata";

    function __construct()
    {
        $this->connection = new PDO("mysql:host=localhost;dbname=".$this->database, $this->username, $this->passwd, array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
            PDO::MYSQL_ATTR_LOCAL_INFILE => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ));
        $this->sql = <<<SQL
LOAD DATA LOCAL INFILE '{$this->filepath}' INTO TABLE {$this->table} FIELDS TERMINATED BY ';' ENCLOSED BY '"' LINES TERMINATED BY '\\n' IGNORE 1 ROWS (id,nome,telefone,@created_at,updated_at) 
SET created_at = IF(CHAR_LENGTH(TRIM(@created_at)) = 0
                          OR
                          TRIM(@created_at) = NULL, NOW(), @created_at);
SQL;

    }

    public function loadCsvData(){
        //die(var_dump($this->sql));
        $stmt = $this->connection->prepare($this->sql);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            unlink($this->filepath);
            return true;
        }else{
            return false;
        }
    }

    public function alterarCsv($arquivo,$array=NULL)
    {
            $linha='';
            $hashes=array();
            if (($handle = fopen($arquivo, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 20000, ";")) !== FALSE) {
                    //Aqui se necessário inserir dados no $array
                    //$array com campos adicionais que não estão no .csv
                    if(is_array($array) && !empty($array)) {
                        $linha .= ";" . implode(";", $data) . ";" . $array[0] . ";" . $array[1] . ";" . $array[2] . ";" . $array[3] . ";" . "\n";
                    }else{
                        $linha .= ";" . implode(";", $data) . ";" . "\n";
                    }
                }
                fclose($handle);
                $pf = fopen($this->filepath, 'a+');
                fputs($pf, $linha);
                fclose($pf);
                @chmod($this->filepath,0777);
                return true;

            } else {
                return false;
            }
    }


}

<?php 
ini_set('display_errors', 1);
class DB{

    const PASS_IS_FAILD = 10;
    const NOT_FOUND = 11;
    const TYPE_PHP = 12;
    const TYPE_JAVA = 13;
    const TYPE_YAML = 14;

    const FILE_DIR = "./dataFolder/files/";
    
    public function __construct(){
        
        if(!file_exists(__DIR__ . "dataFolder/my_data.db")){
            @mkdir("./dataFolder");
            @mkdir("./dataFolder/files");
            $pdo = new PDO("sqlite:" . self::getDBFullPath());
            $sql = "create table codes(id integer, pass text, title text, type integer)";
            $pdo->query($sql);
            $pdo = NULL;
        }

    }

    public function saveCode($code, $title, $type){
        $pass = self::makePass();
        $id = self::getHash();
        $type = $this->type2int($type);
        try {
            $pdo = new PDO("sqlite:" . self::getDBFullPath());
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $stmt = $pdo->prepare("INSERT INTO codes(id, pass, title, type) VALUES (?, ?, ?, ?)");
            $stmt->execute([$id, password_hash($pass, PASSWORD_DEFAULT), $title, $type]);
            @file_put_contents(self::FILE_DIR.$id.".txt", $code);
            @chmod(self::FILE_DIR.$id.".txt", 0600);
            //file_put_contents("log.txt", "[id => " . $id . ", type => " . $type . "]", FILE_APPEND); 
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
        $pdo = NULL;
        return ["id"=>$id, "pass"=>$pass];
    }


    public function getCode(string $id){
        $result = false;
        if(!file_exists(self::FILE_DIR.$id.".txt")) return self::NOT_FOUND;
        try {
            $pdo = new PDO("sqlite:" . self::getDBFullPath());
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $stmt = $pdo->prepare("SELECT * FROM codes WHERE id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            $result["code"] = file_get_contents(self::FILE_DIR.$id.".txt");
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
        return $result;
    }

    public function removeCode($id = "", $pass = ""){
        if($data = $this->getCode($id)){
            if(password_verify($pass, $data["pass"])){
                try {
                    $pdo = new PDO("sqlite:" . self::getDBFullPath());
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                    $stmt = $pdo->prepare("DELETE FROM codes WHERE id = ?");
                    $stmt->execute([$id]);
                    @unlink(self::FILE_DIR.$id.".txt");
                } catch (PDOException $e) {
                    echo $e->getMessage();
                    return false;
                }
            }else{
                return self::PASS_IS_FAILD;
            }
        }
        return true;
    }


    public static function getHash() {
        $algo = 'CRC32';
        $uniq = uniqid();
        return strtr(rtrim(base64_encode(pack('H*', $algo($uniq))), '='), '+/', '-_');
    }


    public function getType($int){
        $type = "";
        switch($int){
            case self::TYPE_PHP:
                $type = "php";
                break;
            case self::TYPE_JAVA:
                $type = "java";
                break;
            case self::TYPE_YAML:
                $type = "yaml";
                break;
            default:
                $type = "php";
        }
        return $type;
    }


    public function type2int($type){
        switch ($type) {
            case "php":
                $type = self::TYPE_PHP;
                break;
            case "java":
                $type = self::TYPE_JAVA;
                break;
            case "yaml":
                $type = self::TYPE_YAML;
                break;
            default:
                $type = self::TYPE_PHP;
                break;
        }
        return $type;
    }

    public static function makePass(){
        return substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ$;/=&`][-+_{}!#@', 8)), 0, 8);
    } 


    public static function getDBFullPath(){
    	return __DIR__ . DIRECTORY_SEPARATOR . "dataFolder" . DIRECTORY_SEPARATOR . "my_data.db";
    }
    
}


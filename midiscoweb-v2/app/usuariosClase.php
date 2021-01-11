<?php
include_once "config.php";

/* Clase usuarios miDiscoWebv2
 *
 * Acceso a datos con BD Usuarios y Patrón Singleton 
 * Un único objeto para la clase
 */

class Usuarios {
    
    private static $modelo = null;
    private $dbh = null;
    private $stmt_usuarios = null;
    private $stmt_usuario  = null;
    private $stmt_altauser  = null;
    private $stmt_boruser  = null;
    private $stmt_moduser  = null;
    

    public static function getModelo(){
        if (self::$modelo == null){
            self::$modelo = new Usuarios();
        }
        return self::$modelo;
    }


   // Constructor privado Patron singleton
   
   private function __construct(){
        
        try {
          $dsn = "mysql:host=".SERVER_DB.";dbname=Usuarios;charset=utf8";
          $this->dbh = new PDO($dsn, "root", "root");
          $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e){
          echo "Error de conexión ".$e->getMessage();
            exit();
        }

    // Construyo las consultas
    $this->stmt_usuarios  = $this->dbh->prepare("select * from Usuarios");
    $this->stmt_usuario   = $this->dbh->prepare("select * from Usuarios where id=:id");
    $this->stmt_altauser  = $this->dbh->prepare("INSERT INTO Usuarios VALUES(?,?,?,?,?,?)");
    $this->stmt_boruser   = $this->dbh->prepare("DELETE FROM Usuarios WHERE id = ?");
    $this->stmt_moduser   = $this->dbh->prepare("UPDATE Usuarios SET clave =?, nombre=?, email=?, plan=?, estado=? WHERE id=?");
    

    }

    // Cierro la conexión anulando todos los objectos relacioanado con la conexión PDO (stmt)
    public static function closeModelo(){
        if (self::$modelo != null){
            $this->stmt_usuarios = null;
            $this->stmt_usuario  = null;
            $this->stmt_altauser  = null;
            $this->stmt_boruser  = null;
            $this->stmt_moduser  = null;
            $this->dbh = null;
            self::$modelo = null; // Borro el objeto.
        }
    }

        // Devuelvo la lista de Usuarios
        public function getUsuarios ():array {
            $tuser = [];
            $this->stmt_usuarios->setFetchMode(PDO::FETCH_CLASS, 'Usuario');
            
            if ( $this->stmt_usuarios->execute() ){
                while ( $user = $this->stmt_usuarios->fetch()){
                   $tuser[]= $user;
                }
            }
            return $tuser;
        }
        
        // Devuelvo un usuario o false
        public function getUsuario (String $login) {
            $user = false;
            
            $this->stmt_usuario->setFetchMode(PDO::FETCH_CLASS, 'Usuario');
            $this->stmt_usuario->bindParam(':login', $login);
            if ( $this->stmt_usuario->execute() ){
                 if ( $obj = $this->stmt_usuario->fetch()){
                    $user= $obj;
                }
            }
            return $user;
        }


       // INSERT
       public static function UserAdd($userid, $userdat){
        $stmt = self::$dbh->prepare(self::$stmt_altauser);
        $stmt->bindValue(1,$userid);
        $stmt->bindValue(2,$userdat[0]);
        $stmt->bindValue(3,$userdat[1]);
        $stmt->bindValue(4,$userdat[2]);
        $stmt->bindValue(5,$userdat[3]);
        $stmt->bindValue(6,$userdat[4]);
        if($stmt->execute()){
            mkdir("./app/dat/".$userid, 0777);
            chmod("./app/dat/".$userid, 0777);
            return true;
        } else  {
            return false;
        }
    }

        // DELETE
        public static function UserDel($userid){
        $stmt = self::$dbh->prepare(self::$stmt_boruser);
        $stmt->bindValue(1,$userid);
        if($stmt->execute()){
            return true;
        } else {
            return false;
        }
        
    }
        // UPDATE
        public static function UserUpdate ($userid, $userdat){
        $stmt = self::$dbh->prepare(self::$stmt_moduser);
        $stmt->bindValue(1,$userdat[0]);
        $stmt->bindValue(2,$userdat[1]);
        $stmt->bindValue(3,$userdat[2]);
        $stmt->bindValue(4,$userdat[3]);
        $stmt->bindValue(5,$userdat[4]);
        $stmt->bindValue(6,$userid);
        if($stmt->execute()){
            return true;
        } else  {
            return false;
        }
     
    }

        // Evito que se pueda clonar el objeto. (SINGLETON)
        public function __clone()
        { 
            trigger_error('La clonación no permitida', E_USER_ERROR); 
        }
    }
    
?>
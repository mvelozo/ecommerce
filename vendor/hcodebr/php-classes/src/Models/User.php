<?php
namespace Hcode\Models;

use Exception;
use \Hcode\DB\Sql;
use \Hcode\Model;

class User extends Model {

    const SESSION = "User";

    public static function verifyLogin($inadmin = true)
    {
        if(
            !isset($_SESSION[User::SESSION]) 
            ||  !$_SESSION[User::SESSION] 
            ||  !(int)$_SESSION[User::SESSION]["iduser"] > 0 
            ||  (bool)$_SESSION[User::SESSION]["inadmin"] != $inadmin
        )
        {
            header("Location: /login");
            exit;
        }
    }

    public static function listAll()
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING (idperson) ORDER BY b.desperson");
    }

    public function save()
    {
        $sql = new Sql();
        $res = $sql->select("CALL sp_users_save(
            :desperson
            , :deslogin
            , :despassword
            , :desemail
            , :nrphone
            , :inadmin)", 
        array(
            ":desperson" => $this->getdesperson()
            , ":deslogin" => $this->getdeslogin()
            , ":despassword" => $this->getdespassword()
            , ":desemail" => $this->getdesemail()
            , ":nrphone" => $this->getnrphone()
            , ":inadmin" => $this->getinadmin()
        ));

        $this->setData($res[0]);
    }
    
    public function get($iduser)
    {
        $sql = new Sql();

        $res = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING (idperson) WHERE a.iduser = :iduser", array(
            ":iduser" => $iduser
        ));
        
        $this->setData($res[0]);

    }
    
    public function update()
    {
        $sql = new Sql();
        $res = $sql->select("CALL sp_usersupdate_save(
            :iduser
            , :desperson
            , :deslogin
            , :despassword
            , :desemail
            , :nrphone
            , :inadmin)", 
        array(
            ":iduser"=> $this->getiduser()
            , ":desperson" => $this->getdesperson()
            , ":deslogin" => $this->getdeslogin()
            , ":despassword" => $this->getdespassword()
            , ":desemail" => $this->getdesemail()
            , ":nrphone" => $this->getnrphone()
            , ":inadmin" => $this->getinadmin()
        ));

        $this->setData($res[0]);

    }
    
    public function delete()
    {
        $sql = new Sql();

        $res = $sql->select("CALL sp_usersdelete(:iduser)", array(":iduser"=> $this->getiduser()));
        
        $this->setData($res[0]);

    }

    public static function login($login, $password)
    {
        $sql = new Sql();
        $res = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(
            ":LOGIN" => $login
        ));

        if (count($res) === 0) 
        {
            throw new \Exception("Usuário inexistente ou senha inválida.", 1);
        }
        
        $data = $res[0];
        
        if(password_verify($password, $data["despassword"]))
        {
            $user = new User();
            
            $user->setData($data);
            
            $_SESSION[User::SESSION] = $user->getValues();

            return $user;

        } else {
            throw new \Exception("Usuário inexistente ou senha inválida.", 1);
        } 
    }
    
    public static function logout()
    {
        $_SESSION[User::SESSION] = null;
    }
}
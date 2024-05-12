<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use App\Models\User;
use PhpParser\Node\Stmt\TryCatch;

class JwtAuth{
    private $key;
    function __construct()
    {
        //ESTA ES LA LLAVE PRIVADA DE NUESTRA APP, PUEDE SER HASH O LO QUE SEA
        $this->key="aswqdfewqeddafe23ewresa";
    }

    public function getToken($email, $password) {
        $hashedPassword = hash('sha256', $password);
        $user = User::where('email', $email)->first();
    
        if ($user && hash_equals($user->password, $hashedPassword)) {
            $token = [
                'iss' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'rol' => $user->rol,
                'iat' => time(),
                'exp' => time() + 2000
            ];
    
            $data = JWT::encode($token, $this->key, 'HS256');
        } else {
            $data = [
                'status' => 401,
                'message' => 'Datos de autenticación incorrectos'
            ];
        }
    
        return $data;
    }
    

    //OBTIENE LA VERIFICACION DEL TOKEN Y SE OBTIENEN LOS DATOS DEL TOKEN CIFRADO
    public function checkToken($jwt,$getId=false){
        $authFlag=false;
        if(isset($jwt)){
            try{
                $decoded=JWT::decode($jwt,new Key($this->key,'HS256')); //SI NO SE DECIFRA PUEDE QUE YA EXP EL TOKEN O LANZAR EXEP
            }catch(\DomainException $ex){ //LA BARRA INCLINADA JALA LA EXP DE DONDE ESTE CREADO
                $authFlag=false;
            }catch(ExpiredException $ex){
                $authFlag=false;
            }
            if(!empty($decoded)&&is_object($decoded)&&isset($decoded->iss)){
                $authFlag=true;
            }
            if($getId && $authFlag){
                return $decoded;
            }
        }
        return $authFlag; //SI NO VIENE EL TOKEN SE MANDA FALSE
    }

}
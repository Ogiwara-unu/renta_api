<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
//Libreria para encriptar informacion delicada
use Illuminate\Support\Facades\Crypt;
use App\Models\User;

class UserController extends Controller
{
    public function index(){
        $users = User::all();

        return response()->json([
            'status' => 200,
            'message' => 'Todos los usuarios :)',
            'data' => $users
        ]);
    }

    public function store(Request $request){
        $data_input = $request->input('data', null);
        if ($data_input) {
            
            // Si recibimos un objeto JSON, no necesitamos decodificarlo
            if(is_array($data_input)){
                $data = array_map('trim',$data_input);
            }else{
                $data = json_decode($data_input,true);
                $data = array_map('trim',$data);
            }

            $rules = [
                'name' => 'required|alpha_num',
                'email' => 'required|email|unique:users',
                'password' => 'required|alpha_num|min:6'
            ];
            
            $isValid = \validator($data,$rules);
            if(!$isValid->fails()){
                
                //Se Registran los datos en base de datos, encriptando los que son delicados por motivo de seguridad
                $password = Crypt::encryptString($data['password']);
                
                //Creamos el objeto Usuario para proceder a guardarlo en la BD
                $user = new User();
                $user -> password = $password;
                $user -> name = $data['name'];
                $user -> email = $data['email'];
                $user->save();
                //Mensaje que indica que el Usuario se registro correctamente
                $response = array(
                    'status' => 201,
                    'message' => 'Usuario registrado :)',
                    'user' => $user
                );
            }else{
                $response=array(
                    'status'=>406,
                    'message'=>'Datos invalidos >:(',
                    'errors'=>$isValid->errors() //SOLO SE PONE PARA VALIDAR LA RESPUESTA
                );

            }
        }else{
            $response=array(
                'status'=>400, //HUBO UNA SINTAXIS INVALIDA O SEA, NO SE MANDO LA DATA
                'message'=>'No se encontro el objeto data >:('
            );
        }

        return response()->json($response, $response['status']);
    }

    public function show($email){
         //Se busca la tarjeta por medio del email ya que debe ser unico
         $data = User::where('email', $email)->first();
         //Si existe una tarjeta con esos ultimos cuatro digitos se mostrara con la informacion privada censurada
         if ($data) {
            $data=$data->load('renta');
             $response = array(
                 'status' => 200,
                 'message' => 'Datos del usuario :)',
                 'Usuario' => $data
             );
             //Si no existe o no se encontro simplemente se muestra un mensaje
         } else {
             $response = array(
                 'status' => 400,
                 'message' => 'Recurso no encontrado >:('
             );
         }
 
         return response()->json($response, $response['status']);
    }

    public function destroy($email){
        //Se verifica si se ingreso un email
        if(isset($email)){
            $deleted=User::where('email',$email)->delete();
            if($deleted){ //Si existe un usuario con ese email se elimina
                $response=array(
                    'status' => 200,
                    'message' => 'Usuario eliminado :)'
                );
            }
        }else{ //Sino se encontro ningun usuario solo se muestra un mensaje
            $response=array(
                'status' => 400,
                'message' => 'No se pudo eliminar el recurso,compruebe que exista >:('
            );  
        }

        return response()->json($response,$response['status']);
    }
}



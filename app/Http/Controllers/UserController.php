<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
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
                'password' => 'required|alpha_num|min:6',
                'rol' => 'required|alpha'
            ];
            
            $isValid = \validator($data,$rules);
            if(!$isValid->fails()){
                
                //Creamos el objeto Usuario para proceder a guardarlo en la BD
                $user = new User();
                $user -> name = $data['name'];
                $user -> email = $data['email'];
                $user -> password = hash('sha256',$data['password']); //EL METODO DE CIFRADO ES sha256
                $user -> rol = $data['rol'];
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

    public function update(Request $request, $email){
        $user = User::where('email', $email)->first();
        if ($user) {
            $data_input = $request->input('data', null);
            if ($data_input) {
                if(is_array($data_input)){
                    $data = array_map('trim', $data_input);
                } else {
                    $data = json_decode($data_input, true);
                    $data = array_map('trim', $data);
                }
                $rules = [
                    'name' => 'alpha_num',
                    'email' => 'email',
                    'password' => 'alpha_num|min:6',
                    'rol' => 'alpha'
                ];
    
                $isValid = \validator($data, $rules);
    
                if (!$isValid->fails()) {
                    // Actualizar los campos del usuario
                    if (isset($data['name'])) {
                        $user->name = $data['name'];
                    }
                    if (isset($data['email'])) {
                        $user->email = $data['email'];
                    }
                    if (isset($data['password'])) {
                        $user->password = hash('sha256', $data['password']);
                    }
                    if (isset($data['rol'])) {
                        $user->rol = $data['rol'];
                    }
    
                    // Guardar los cambios en la base de datos
                    $user->save();
    
                    $response = [
                        'status' => 200,
                        'message' => 'Usuario actualizado :)',
                        'user' => $user
                    ];
                } else {
                    $response = [
                        'status' => 406,
                        'message' => 'Datos inválidos >:(',
                        'errors' => $isValid->errors()
                    ];
                }
            } else {
                $response = [
                    'status' => 400,
                    'message' => 'No se encontraron datos para actualizar >:('
                ];
            }
        } else {
            $response = [
                'status' => 404,
                'message' => 'Usuario no encontrado >:('
            ];
        }
    
        return response()->json($response, $response['status']);
    }
    

    public function login(Request $request){
        $data_input=$request->input('data',null);
        $data=json_decode($data_input,true);
        $data=array_map('trim',$data);
        $rules=['email'=>'required','password'=>'required'];
        $isValid=\validator($data,$rules);
        if(!$isValid->fails()){
            $jwt=new JwtAuth();
            $response=$jwt->getToken($data['email'],$data['password']);
            return response()->json($response);
        }else{
            $response=array(
                'status'=>406,
                'message'=>'Error en la validación de los datos >:(',
                'errors'=>$isValid->errors(),
            );
            return response()->json($response,406);
        }

    }
    

    public function getIdentity(Request $request){ //DEVUELVE CIERTA INFO DEL USUARIO QUE ESTE LOGGEADO
        $jwt=new JwtAuth();
        $token=$request->header('bearertoken'); //ENCABEZADO
        if(isset($token)){
            $response=$jwt->checkToken($token,true);
        }else{
            $response=array(
                'status'=>404,
                'message'=>'token (bearertoken) no encontrado >:(',
            );
        }
        return response()->json($response);
    }

}



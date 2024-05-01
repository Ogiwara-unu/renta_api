<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
//Libreria para encriptar informacion delicada
use Illuminate\Support\Facades\Crypt;
use App\Models\Tarjeta;

class TarjetaController extends Controller
{
    //Metodo GET para obtener todos los registros
    public function index(){
        $data = Tarjeta::all();
        $response = array(
            'status'=>200,
            'message'=>"Todos los registros de tarjeta >:3",
            'data'=>$data
        );
        return response()->json($response,200);
    }

    //Metodo POST de tarjeta
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
                'numero_tarjeta' => 'required|alpha_num',
                'titular' => 'required|alpha',
                'fecha_vencimiento' => 'required|date',
                'cvv' => 'required|alpha_num'
            ];
            
            $isValid = \validator($data,$rules);
            if(!$isValid->fails()){
                
                //Se Registran los datos en base de datos, encriptando los que son delicados por motivo de seguridad
                $numero_tarjeta = Crypt::encryptString($data['numero_tarjeta']);
                $cvv = Crypt::encryptString($data['cvv']);
                $cuatro_digitos = substr($data['numero_tarjeta'], -4); // Obtener los últimos 4 dígitos
                
                //Creamos el objeto Tarjeta para proceder a guardarlo en la BD
                $tarjeta = new Tarjeta();
                $tarjeta -> numero_tarjeta = $numero_tarjeta;
                $tarjeta -> cvv = $cvv;
                $tarjeta -> cuatro_digitos = $cuatro_digitos;
                $tarjeta -> titular = $data['titular'];
                $tarjeta -> fecha_vencimiento = $data['fecha_vencimiento'];
                $tarjeta->save();
                //Mensaje que indica que la tarjeta se registro correctamente
                $response = array(
                    'status' => 201,
                    'message' => 'Tarjeta registrada >:3',
                    'tarjeta' => $tarjeta
                );
            }else{
                $response=array(
                    'status'=>406,
                    'message'=>'Datos invalidos :3',
                    'errors'=>$isValid->errors() //SOLO SE PONE PARA VALIDAR LA RESPUESTA
                );

            }
        }else{
            $response=array(
                'status'=>400, //HUBO UNA SINTAXIS INVALIDA O SEA, NO SE MANDO LA DATA
                'message'=>'No se encontro el objeto data :3'
            );
        }

        return response()->json($response, $response['status']);
    }

    //Metodo GET por medio del identificador

    public function show($cuatro_digitos){
        //Se busca la tarjeta por medio de sus ultimos cuatro digitos
        $data = Tarjeta::where('cuatro_digitos', $cuatro_digitos)->first();
        //Si existe una tarjeta con esos ultimos cuatro digitos se mostrara con la informacion privada censurada
        if ($data) {
            $data=$data->load('renta');
            $response = array(
                'status' => 200,
                'message' => 'Datos de la tarjeta >:3',
                'Tarjeta' => $data
            );
            //Si no existe o no se encontro simplemente se muestra un mensaje
        } else {
            $response = array(
                'status' => 400,
                'message' => 'Recurso no encontrado :3'
            );
        }

        return response()->json($response, $response['status']);

    }

    //Metodo DELETE por medio del identificador

    public function destroy($cuatro_digitos){

        if(isset($cuatro_digitos)){
            $deleted=Tarjeta::where('cuatro_digitos',$cuatro_digitos)->delete();
            if($deleted){
                $response=array(
                    'status' => 200,
                    'message' => 'Tarjeta eliminada >:3'
                );
            }
        }else{
            $response=array(
                'status' => 400,
                'message' => 'No se pudo eliminar el recurso,compruebe que exista :3'
            );  
        }

        return response()->json($response,$response['status']);

    }

    //Metodo GET con el decriptador
    /*public function show($id){
        $tarjeta = Tarjeta::findOrFail($id);
    
        // Desencriptar los datos sensibles
        $numero_tarjeta = Crypt::decryptString($tarjeta->numero_tarjeta);
        $cvv = Crypt::decryptString($tarjeta->cvv);
    
        // Crear la respuesta
        $response = [
            'status' => 200,
            'message' => 'Datos de la tarjeta desencriptados',
            'tarjeta' => [
                'numero_tarjeta' => $numero_tarjeta,
                'cvv' => $cvv,
                'cuatro_digitos' => $tarjeta->cuatro_digitos,
                'titular' => $tarjeta->titular,
                'fecha_vencimiento' => $tarjeta->fecha_vencimiento
            ]
        ];
    
        return response()->json($response, $response['status']);
    }*/
}

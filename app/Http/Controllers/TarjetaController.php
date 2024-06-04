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
                'titular' => 'required|regex:/^[a-zA-Z\sáéíóúÁÉÍÓÚñÑ]+$/',
                'fecha_vencimiento' => 'required|date',
                'cvv' => 'required|alpha_num'
            ];
            
            $isValid = \validator($data,$rules);
            if(!$isValid->fails()){
                
                //Se Registran los datos en base de datos, encriptando los que son delicados por motivo de seguridad
                $numero_tarjeta = Crypt::encryptString($data['numero_tarjeta']);
                $cvv = Crypt::encryptString($data['cvv']);
                $id = substr($data['numero_tarjeta'], -4); // Obtener los últimos 4 dígitos
                
                //Creamos el objeto Tarjeta para proceder a guardarlo en la BD
                $tarjeta = new Tarjeta();
                $tarjeta -> numero_tarjeta = $numero_tarjeta;
                $tarjeta -> cvv = $cvv;
                $tarjeta -> id = $id;
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

    public function show($id){
        //Se busca la tarjeta por medio de sus ultimos cuatro digitos
        $data = Tarjeta::where('id', $id)->first();
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

    public function destroy($id){

        if(isset($id)){
            $deleted=Tarjeta::where('id',$id)->delete();
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

    public function update(Request $request, $id){
        // Buscar la tarjeta por su identificador
        $tarjeta = Tarjeta::where('id', $id)->first();
    
        // Verificar si la tarjeta existe
        if ($tarjeta) {
            $data_input = $request->input('data', null);
    
            if ($data_input) {
                // Decodificar los datos de entrada si están en formato JSON
                if(is_array($data_input)){
                    $data = array_map('trim', $data_input);
                } else {
                    $data = json_decode($data_input, true);
                    $data = array_map('trim', $data);
                }
    
                // Validar los datos de entrada
                $rules = [
                    'numero_tarjeta' => 'alpha_num',
                    'titular' => 'alpha',
                    'fecha_vencimiento' => 'date',
                    'cvv' => 'alpha_num'
                ];
    
                $isValid = \validator($data, $rules);
    
                if (!$isValid->fails()) {
                    // Actualizar los campos de la tarjeta
                    if (isset($data['numero_tarjeta'])) {
                        $tarjeta->numero_tarjeta = Crypt::encryptString($data['numero_tarjeta']);
                    }
                    if (isset($data['cvv'])) {
                        $tarjeta->cvv = Crypt::encryptString($data['cvv']);
                    }
                    if (isset($data['titular'])) {
                        $tarjeta->titular = $data['titular'];
                    }
                    if (isset($data['fecha_vencimiento'])) {
                        $tarjeta->fecha_vencimiento = $data['fecha_vencimiento'];
                    }
    
                    // Guardar los cambios en la base de datos
                    $tarjeta->save();
    
                    $response = [
                        'status' => 200,
                        'message' => 'Tarjeta actualizada >:3',
                        'tarjeta' => $tarjeta
                    ];
                } else {
                    $response = [
                        'status' => 406,
                        'message' => 'Datos inválidos :3',
                        'errors' => $isValid->errors()
                    ];
                }
            } else {
                $response = [
                    'status' => 400,
                    'message' => 'No se encontraron datos para actualizar :3'
                ];
            }
        } else {
            $response = [
                'status' => 404,
                'message' => 'Tarjeta no encontrada :3'
            ];
        }
    
        return response()->json($response, $response['status']);
    }
    

   
}

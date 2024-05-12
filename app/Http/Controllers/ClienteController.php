<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
      //Metodo GET para obtener todos los registros
      public function index(){
        $data = Cliente::all();
        $response = array(
            'status'=>200,
            'message'=>"Todos los registros de clientes Bv",
            'data'=>$data
        );
        return response()->json($response,200);
    }

    //Metodo POST de cliente
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
                'id' => 'required|numeric',
                'nombre' => 'required|alpha',
                'primer_apellido' => 'required|alpha',
                'segundo_apellido' => 'required|alpha',
                'telefono' => 'required|alpha_num',
                'email' => 'required|email|unique:cliente',
                'direccion' => 'required|alpha_num',
                'fecha_nacimiento' => 'required|date'
            ];
            $isValid=\validator($data,$rules);
            if(!$isValid->fails()){ //SI NO FALLA
                $cliente=new Cliente();
                $cliente->id=$data['id'];
                $cliente->nombre=$data['nombre'];
                $cliente->primer_apellido=$data['primer_apellido'];
                $cliente->segundo_apellido=$data['segundo_apellido'];
                $cliente->telefono=$data['telefono'];
                $cliente->email=$data['email'];
                $cliente->direccion=$data['direccion'];
                $cliente->fecha_nacimiento=$data['fecha_nacimiento'];
                $cliente->save();
                $response=array(
                    'status'=>201, //CODIGO PARA EL EXITO
                    'message'=>'Cliente creado Bv',
                    'Vehiculo'=>$cliente
                );
            }else{
                $response=array(
                    'status'=>406,
                    'message'=>'Datos invalidos :,v',
                    'errors'=>$isValid->errors() //SOLO SE PONE PARA VALIDAR LA RESPUESTA
                );

            }
        }else{
            $response=array(
                'status'=>400, //HUBO UNA SINTAXIS INVALIDA O SEA, NO SE MANDO LA DATA
                'message'=>'No se encontro el objeto data :,v'
            );
        }
        return response()-> json($response,$response['status']);

    }

    //Metodo GET por medio del identificador

    public function show($id){
        //Se busca la tarjeta por medio de sus ultimos cuatro digitos
        $data = Cliente::where('id', $id)->first();
        //Si existe una tarjeta con esos ultimos cuatro digitos se mostrara con la informacion privada censurada
        if ($data) {
            $data=$data->load('licencia');
            $response = array(
                'status' => 200,
                'message' => 'Datos del cliente Bv',
                'Cliente' => $data
            );
            //Si no existe o no se encontro simplemente se muestra un mensaje
        } else {
            $response = array(
                'status' => 400,
                'message' => 'Recurso no encontrado :,v'
            );
        }

        return response()->json($response, $response['status']);

    }

    //Metodo DELETE por medio del identificador

    public function destroy($id){

        if(isset($id)){
            $deleted=Cliente::where('id',$id)->delete();
            if($deleted){
                $response=array(
                    'status' => 200,
                    'message' => 'Cliente eliminado Bv'
                );
            }
        }else{
            $response=array(
                'status' => 400,
                'message' => 'No se pudo eliminar el recurso,compruebe que exista :,v'
            );  
        }

        return response()->json($response,$response['status']);

    }

    public function update(Request $request, $id){
        // Buscar el cliente por su identificador
        $cliente = Cliente::find($id);
    
        // Verificar si el cliente existe
        if ($cliente) {
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
                    'nombre' => 'alpha',
                    'primer_apellido' => 'alpha',
                    'segundo_apellido' => 'alpha',
                    'telefono' => 'alpha_num',
                    'email' => 'email|unique:clientes,email,'.$cliente->id,
                    'direccion' => 'alpha_num',
                    'fecha_nacimiento' => 'date'
                ];
    
                $validator = Validator::make($data, $rules);
    
                if (!$validator->fails()) {
                    // Actualizar los campos del cliente
                    if (isset($data['nombre'])) {
                        $cliente->nombre = $data['nombre'];
                    }
                    if (isset($data['primer_apellido'])) {
                        $cliente->primer_apellido = $data['primer_apellido'];
                    }
                    if (isset($data['segundo_apellido'])) {
                        $cliente->segundo_apellido = $data['segundo_apellido'];
                    }
                    if (isset($data['telefono'])) {
                        $cliente->telefono = $data['telefono'];
                    }
                    if (isset($data['email'])) {
                        $cliente->email = $data['email'];
                    }
                    if (isset($data['direccion'])) {
                        $cliente->direccion = $data['direccion'];
                    }
                    if (isset($data['fecha_nacimiento'])) {
                        $cliente->fecha_nacimiento = $data['fecha_nacimiento'];
                    }
    
                    // Guardar los cambios en la base de datos
                    $cliente->save();
    
                    $response = [
                        'status' => 200,
                        'message' => 'Cliente actualizado Bv',
                        'cliente' => $cliente
                    ];
                } else {
                    $response = [
                        'status' => 406,
                        'message' => 'Datos inválidos :v',
                        'errors' => $validator->errors()
                    ];
                }
            } else {
                $response = [
                    'status' => 400,
                    'message' => 'No se encontraron datos para actualizar :v'
                ];
            }
        } else {
            $response = [
                'status' => 404,
                'message' => 'Cliente no encontrado :v'
            ];
        }
    
        return response()->json($response, $response['status']);
    }
    

    
}

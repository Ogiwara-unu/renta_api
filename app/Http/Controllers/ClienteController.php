<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
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
}

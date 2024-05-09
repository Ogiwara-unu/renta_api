<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response; 
use App\Models\Licencia;

class LicenciaController extends Controller
{
    //METODOS QUE VA MANEJAR EL API DENTRO DEL CONTROLADOR
    public function index(){ 
        $data=Licencia::all(); 
        $response=array(
            "status"=>200,
            "message"=>"Todos los registros de Licencia",
            "data"=>$data
        );
        return response()->json($response,200);
    }


    public function store(Request $request){
        $data_input = $request->input('data', null);
        if($data_input){

            if(is_array($data_input)){
                $data = array_map('trim',$data_input);
            }else{
                $data = json_decode($data_input,true);
                $data = array_map('trim',$data);
            }

            $rules = [
                'id' => 'required|numeric',
                'cliente_id' => 'required|numeric', 
                'fecha_vencimiento' => 'required|date',
                'tipo' => 'required|alpha',
                'img' => 'required|alpha_num', 
            ];

            $isValid = \validator($data,$rules);
            if($isValid->fails()){
                $licencia=new Licencia();
                $licencia->id=$data['id'];
                $licencia->cliente_id=$data['cliente_id'];
                $licencia->fecha_vencimiento=$data['fecha_vencimiento'];
                $licencia->tipo=$data['tipo'];
                $licencia->img=$data['img'];
                $licencia->save();
                $response=array(
                    'status'=>201, //CODIGO PARA EL EXITO
                    'message'=>'Licencia agregada.',
                    'licencia'=>$licencia
                );
            }else{
                $response=array(
                    'status'=>406,
                    'message'=>'Datos invalidos',
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


    public function show($id){
        
        $data = Licencia::where('id', $id)->first();
    
        if ($data) {
            $response = array(
                'status' => 200,
                'message' => 'Datos de la licencia: ',
                'Licencia' => $data
            );
            //Si no existe o no se encontro simplemente se muestra un mensaje
        } else {
            $response = array(
                'status' => 400,
                'message' => 'Recurso no encontrado:'
            );
        }

        return response()->json($response, $response['status']);

    }


    public function destroy($id){
        if(isset($id_licencia)){
            $deleted=Licencia::where('id',$id)->delete();
            if($deleted){
                $response=array(
                    'status'=>200,
                    'message'=>'licencia eliminada'
                );
            }
        }else{
            $response=array(
                'status'=>400,
                'message'=>'No se pudo eliminar el recurso,compruebe que exista'
            );  
        }
        return response()->json($response,$response['status']);
    }
}

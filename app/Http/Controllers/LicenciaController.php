<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response; 
use App\Models\Licencia;

class LicenciaController extends Controller
{
    //METODOS QUE VA MANEJAR EL API DENTRO DEL CONTROLADOR
    public function index(){ //UTILIZA METODO GET PARA OBTENER TODOS LOS REGISTROS
        $data=Licencia::all(); //HACE UN SELECT ALL, DEVUELVE TODO DE LA TABLA AQUI PODRIA CARGAR LA RELACION
        $response=array(
            "status"=>200,
            "message"=>"Todos los registros de Licencia",
            "data"=>$data
        );
        return response()->json($response,200);
    }

    public function store(Request $request){ //USA EL METODO POST PARA CREAR UN REGISTRO
        $data_input=$request->input('data',null); //LAS OBTIENE POR MEDIO DE UN METODO DE ENTRADA
        if($data_input){
            $data=json_decode($data_input,true); //LO DECODIFICA DE JASON Y O VUELVE UN ARREGLO
            $data=array_map('trim',$data); //SE LE APLICA UN ARRAY MAP A CADA DATO
            $rules=[
                'name'=>'required|alpha'
            ];
            $isValid=\validator($data,$rules);
            if(!$isValid->fails()){
                $licencia=new Licencia();
                $licencia->name=$data['name'];
                $licencia->save();
                $response=array(
                    'status'=>201,
                    'message'=>'Licencia creada',
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
                'status'=>400,
                'message'=>'No se encontro el objeto data'

            );
        }
        return response()-> json($response,$response['status']);

    }

    public function show($id){
        $data=Licencia::find($id);
        if(is_object($data)){ //VERIFICA SI TIENE VALORES CREADOS EN EL OBJ
            $data=$data->load('cliente');
            $response=array(
                'status'=>200,
                'message'=>'Datos de la licencia',
                'licencia'=>$data

            );
        }else{
            $response=array(
                'status'=>400,
                'message'=>'Recurso no encontrado'

            );   
        }
        return response()->json($response,$response['status']);
    }

    public function destroy($id_licencia){
        if(isset($id)){
            $deleted=Licencia::where('id_licencia',$id_licencia)->delete();
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

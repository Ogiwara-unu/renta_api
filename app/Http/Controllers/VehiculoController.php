<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response; 
use App\Models\Vehiculo;

class VehiculoController extends Controller
{
       //METODOS QUE VA MANEJAR EL API DENTRO DEL CONTROLADOR
       public function index(){ //UTILIZA METODO GET PARA OBTENER TODOS LOS REGISTROS
        $data=Vehiculo::all(); //HACE UN SELECT ALL, DEVUELVE TODO DE LA TABLA AQUI PODRIA CARGAR LA RELACION
        $response=array(
            "status"=>200,
            "message"=>"Todos los registros de Vehiculos",
            "data"=>$data
        );
        return response()->json($response,200);
    }

    public function store(Request $request){ //USA EL METODO POST PARA CREAR UN REGISTRO
        $data_input=$request->input('data',null); //LAS OBTIENE POR MEDIO DE UN METODO DE ENTRADA
        if($data_input){
            $data=json_decode($data_input,true); //LO DECODIFICA DE JASON Y LO VUELVE UN ARREGLO
            $data=array_map('trim',$data); //SE LE APLICA UN ARRAY MAP A CADA DATO. TRIM ELIMINA LOS ESPACIOS VACIOS   
            $rules=[ //ESTABLECE LAS REGLAS PARA GUARDAR EL OBJ
                'placa'=>'required|alpha_num', //TIPO REQUERIDO Y SOLO ACEPTA CAMPOS DE TEXTO
                'marca'=>'required|alpha',
                'modelo'=>'required|alpha_num',
                'transmision'=>'required|alpha',
                'precio'=>'required|numeric', //ACEPTA CAMPOS NUMERICOS
                'kilometraje'=>'required|numeric', //INT
                'anio'=>'required|alpha_num',
                'estado'=>'required|alpha',
                'img'=>'required|alpha_num'
            ];
            $isValid=\validator($data,$rules);
            if(!$isValid->fails()){ //SI NO FALLA
                $vehiculo=new Vehiculo();
                $vehiculo->placa=$data['placa'];
                $vehiculo->marca=$data['marca'];
                $vehiculo->modelo=$data['modelo'];
                $vehiculo->transmision=$data['transmision'];
                $vehiculo->precio=$data['precio'];
                $vehiculo->kilometraje=$data['kilometraje'];
                $vehiculo->anio=$data['anio'];
                $vehiculo->estado=$data['estado'];
                $vehiculo->img=$data['img'];
                $vehiculo->save();
                $response=array(
                    'status'=>201, //CODIGO PARA EL EXITO
                    'message'=>'Vehiculo creado',
                    'Vehiculo'=>$vehiculo
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
                'message'=>'No se encontro el objeto data'
            );
        }
        return response()-> json($response,$response['status']);

    }

    public function show($placa){
        $data = Vehiculo::where('placa', $placa)->first();
    if ($data) {
        $data=$data->load('renta');
        $response = array(
            'status' => 200,
            'message' => 'Datos del vehiculo',
            'Vehiculo' => $data
        );
    } else {
        $response = array(
            'status' => 400,
            'message' => 'Recurso no encontrado'
        );
    }
    return response()->json($response, $response['status']);
}

    public function destroy($placa){
        if(isset($placa)){
            $deleted=Vehiculo::where('placa',$placa)->delete();
            if($deleted){
                $response=array(
                    'status'=>200,
                    'message'=>'Vehiculo eliminado :v'
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

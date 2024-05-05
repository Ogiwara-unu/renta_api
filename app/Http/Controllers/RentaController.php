<?php

namespace App\Http\Controllers;

use App\Models\Renta;
use Illuminate\Http\Request;

class RentaController extends Controller
{
     //Metodo GET 
     public function index(){
        $data = Renta::all();
        $response = array(
            'status'=>200,
            'message'=>"Todos los registros de las rentas: ",
            'data'=>$data
        );
        return response()->json($response,200);
    }

        //Metodo POST 
        public function store(Request $request){
            $data_input = $request->input('data', null);
            if ($data_input) {
                
                if(is_array($data_input)){
                    $data = array_map('trim',$data_input);
                }else{
                    $data = json_decode($data_input,true);
                    $data = array_map('trim',$data);
                }
                $rules = ['id' => 'required|numeric',
                'user_id' => 'required|numeric',
                'cliente_id' => 'required|numeric',
                'vehiculo_id' => 'required|alpha_num',
                'tarjeta_id' => 'required|numeric',
                'tarifa_base' => 'required|numeric',
                'fecha_entrega' => 'required|date',
                'fecha_devolucion' => 'required|date',
                'total' => 'required|numeric'
            ];
              
                $isValid=\validator($data,$rules);
                if(!$isValid->fails()){ 
                    $renta=new Renta();
                    $renta->id=$data['id'];
                    $renta->user_id=$data['user_id'];
                    $renta->cliente_id=$data['cliente_id'];
                    $renta->vehiculo_id=$data['vehiculo_id'];
                    $renta->tarjeta_id=$data['tarjeta_id'];
                    $renta->tarifa_base=$data['tarifa_base'];
                    $renta->fecha_entrega=$data['fecha_entrega'];
                    $renta->fecha_devolucion=$data['fecha_devolucion'];
                    $renta->total=$data['total'];
                    $renta->save();
                    $response=array(
                        'status'=>201, 
                        'message'=>'Renta agregada',
                        'renta'=>$renta
                    );
                }else{
                    $response=array(
                        'status'=>406,
                        'message'=>'Datos invalidos:',
                        'errors'=>$isValid->errors() 
                    );
    
                }
            }else{
                $response=array(
                    'status'=>400, 
                    'message'=>'No se encontro el objeto data :,v'
                );
            }
            return response()-> json($response,$response['status']);
    
        }

        //METODO GET POR ID
        public function show($id){
            $data = Renta::where('id', $id)->first();
            
            if ($data) {
                $response = array(
                    'status' => 200,
                    'message' => 'Datos de la renta:',
                    'Renta' => $data
                );
                
            } else {
                $response = array(
                    'status' => 400,
                    'message' => 'Recurso no encontrado:'
                );
            }
    
            return response()->json($response, $response['status']);
    
        }

            //METODO DELETE POR ID
            public function destroy($id){
                if(isset($id)){
                    $deleted=Renta::where('id',$id)->delete();
                    if($deleted){
                        $response=array(
                            'status'=>200,
                            'message'=>'Renta eliminada'
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

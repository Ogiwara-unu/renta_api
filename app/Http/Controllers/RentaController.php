<?php

namespace App\Http\Controllers;

use App\Models\Renta;
use Illuminate\Http\Request;
use App\Helpers\JwtAuth;

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
                $rules = ['id' => 'required|numeric', //COMO SE LE ASIGNA EL USER REGISTRADO AL CAMPO USER_ID, NO HACE FALTA PONERLO
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
                    $jwtAuth = new JwtAuth(); // CREA INSTANCIA DE jwt
                    $renta->user_id = $jwtAuth->checkToken($request->header('bearertoken'), true)->iss; //AGARRA EL USUARIO QUE HACE LA ACCION Y LO AGREGA
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
                $data=$data->load('user','cliente','vehiculo','tarjeta');
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

            public function update(Request $request, $id){
                // Buscar la renta por su identificador
                $renta = Renta::find($id);
            
                // Verificar si la renta existe
                if ($renta) {
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
                            'cliente_id' => 'numeric',
                            'vehiculo_id' => 'alpha_num',
                            'tarjeta_id' => 'numeric',
                            'tarifa_base' => 'numeric',
                            'fecha_entrega' => 'date',
                            'fecha_devolucion' => 'date',
                            'total' => 'numeric'
                        ];
            
                        $isValid = \validator($data, $rules);
            
                        if (!$isValid->fails()) {
                            // Actualizar los campos de la renta
                            if (isset($data['cliente_id'])) {
                                $renta->cliente_id = $data['cliente_id'];
                            }
                            if (isset($data['vehiculo_id'])) {
                                $renta->vehiculo_id = $data['vehiculo_id'];
                            }
                            if (isset($data['tarjeta_id'])) {
                                $renta->tarjeta_id = $data['tarjeta_id'];
                            }
                            if (isset($data['tarifa_base'])) {
                                $renta->tarifa_base = $data['tarifa_base'];
                            }
                            if (isset($data['fecha_entrega'])) {
                                $renta->fecha_entrega = $data['fecha_entrega'];
                            }
                            if (isset($data['fecha_devolucion'])) {
                                $renta->fecha_devolucion = $data['fecha_devolucion'];
                            }
                            if (isset($data['total'])) {
                                $renta->total = $data['total'];
                            }
            
                            // Guardar los cambios en la base de datos
                            $renta->save();
            
                            $response = [
                                'status' => 200,
                                'message' => 'Renta actualizada',
                                'renta' => $renta
                            ];
                        } else {
                            $response = [
                                'status' => 406,
                                'message' => 'Datos inválidos:',
                                'errors' => $isValid->errors()
                            ];
                        }
                    } else {
                        $response = [
                            'status' => 400,
                            'message' => 'No se encontraron datos para actualizar'
                        ];
                    }
                } else {
                    $response = [
                        'status' => 404,
                        'message' => 'Renta no encontrada'
                    ];
                }
            
                return response()->json($response, $response['status']);
            }
            
    
}

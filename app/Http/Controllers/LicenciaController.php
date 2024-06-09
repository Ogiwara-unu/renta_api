<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response; 
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str; 

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
                $licencia->img=$data['img']; //HAY QUE VERIFICAR QUE LA IMG EXISTA EN LA CARPETA STORAGE PUBLIC LICENCIAS
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


    public function destroy($id) {
        $licencia = Licencia::find($id);
        if ($licencia) {
          $deleted = $licencia->delete();
          if ($deleted) {
            $response = [
              'status' => 200,
              'message' => 'Licencia eliminada con éxito.'
            ];
          } else {
            $response = [
              'status' => 500,
              'message' => 'Error al eliminar la licencia.'
            ];
          }
        } else {
          $response = [
            'status' => 400,
            'message' => 'No se pudo eliminar la licencia, compruebe que exista.'
          ];
        }
        return response()->json($response, $response['status']);
      }

    public function update(Request $request, $id){
        // Obtener los datos de la solicitud
        $data_input = $request->input('data', null);
    
        if($data_input){
            // Verificar si los datos son un arreglo o una cadena JSON
            if(is_array($data_input)){
                $data = array_map('trim', $data_input);
            }else{
                $data = json_decode($data_input, true);
                $data = array_map('trim', $data);
            }
    
            // Definir las reglas de validación
            $rules = [
                'fecha_vencimiento' => 'date',
                'tipo' => 'alpha',
                'img' => 'alpha_num',
            ];
    
            // Validar los datos recibidos
            $isValid = \validator($data, $rules);
    
            if($isValid->fails()){
                $response = [
                    'status' => 406,
                    'message' => 'Datos inválidos',
                    'errors' => $isValid->errors(),
                ];
            }else{
                // Buscar la licencia por su ID
                $licencia = Licencia::find($id);
    
                if($licencia){
                    // Actualizar los campos de la licencia
                    $licencia->fill($data);
    
                    // Guardar los cambios en la base de datos
                    $licencia->save();
    
                    $response = [
                        'status' => 200,
                        'message' => 'Licencia actualizada correctamente',
                        'licencia' => $licencia,
                    ];
                }else{
                    $response = [
                        'status' => 404,
                        'message' => 'Licencia no encontrada',
                    ];
                }
            }
        }else{
            $response = [
                'status' => 400,
                'message' => 'No se encontró el objeto data',
            ];
        }
    
        return response()->json($response, $response['status']);
    }
    

    public function uploadImage(Request $request){  
        $isValid=Validator::make($request->all(),['file0'=>'required|image|mimes:jpg,png,jpeg,svg']);
        if(!$isValid->fails()){
            $image=$request->file('file0');
            $filename=Str::uuid().".".$image->getClientOriginalExtension(); //SE CONCATENA LO QUE LA IMG TIENE POR EXTENSION
            Storage::disk('licencias')->put($filename,File::get($image));
            $response=array(
                'status'=>201,
                'message'=>'Imagen guardada',
                'filename'=>$filename,
            );
        }else{
            $response=array(
                'status'=>406,
                'message'=>'Error: no se encontro el archivo',
                'errors'=>$isValid->errors(),
            );
        }
        return response()->json($response,$response['status']);
    }

    public function getImage($filename){
        if(isset($filename)){
            $exist=Storage::disk('licencias')->exists($filename);
            if($exist){
                $file=Storage::disk('licencias')->get($filename);
                return new Response($file,200); //RETORNA LA IMG, POR ESO SE USA LA CLASE RESPONSE
            }else{
                $response=array(
                    'status'=>404,
                    'message'=>'Imagen no existe',
                );
            }
        }else{
            $response=array(
                'status'=>406,
                'message'=>'No se definió el nombre de la imagen',
            );
        }
        return response()->json($response,$response['status']);
    }
    
}

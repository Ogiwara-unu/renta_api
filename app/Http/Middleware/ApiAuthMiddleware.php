<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\JwtAuth;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response  //CAPTURA TODA PETICION ANTES DE LLEGAR A LOS CONTROLLERS
    {
        //Authorization   ESTAS SON FORMAS DE LLAMARLO SEGUN EL API
        //Bearer
        //Token 
        $jwt=new JwtAuth();
        $token=$request->header('bearertoken'); //NOMBRE PROPIO Y ASI SE LLAMARA EL ACARREADOR
        $logged=$jwt->checkToken($token); 
        if($logged){
            return $next($request);
        }else{
            $response=array(
                'status'=>401,
                'message'=>'No tiene privilegios para acceso al recurso :,v'
            );
            return response()->json($response,401);
        }
    }
}

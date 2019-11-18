<?php

require_once "../app/clases/token.php";

class Middleware
{
    public static function ValidarToken($request,$response,$next)
    {
        $token = $request->getHeader("Authorization");
        $validacionToken = Token::VerificarToken($token[0]);
        if($validacionToken["Estado"] == "OK"){
            $request = $request->withAttribute("payload", $validacionToken);
            return $next($request,$response);
        }
        else{
            $newResponse = $response->withJson($validacionToken,401);
            return $newResponse;
        }
    }

    public static function ValidarAdmin($request,$response,$next)
    {
        $payload = $request->getAttribute("payload")["Payload"];
        $data = $payload->data;

        if($data->campo3 == "administrador"){
            return $next($request,$response);
        }
        else{
            $respuesta = array("Estado" => "ERROR", "Mensaje" => "No tiene permiso para realizar esta accion.");
            $newResponse = $response->withJson($respuesta,401);
            return $newResponse;
        }
    }

    public static function ValidarAlumno($request,$response,$next)
    {
        $payload = $request->getAttribute("payload")["Payload"];
        $data = $payload->data;

        if($data->tipo == "alumno"){
            return $next($request,$response);
        }
        else{
            $respuesta = array("Estado" => "ERROR", "Mensaje" => "No tiene permiso para realizar esta accion.");
            $newResponse = $response->withJson($respuesta,401);
            return $newResponse;
        }
    }
}

?>

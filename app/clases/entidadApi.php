<?php

class EntidadApi
{
    public function Login($request, $response, $args)
    {
        try
        {
            $data = file_get_contents('php://input');
            $entidadAux = json_decode($data);
            $campo1 = $entidadAux->campo1;
            $campo2 = $entidadAux->campo2;

            $entidadDao = new App\Models\Entidad;

            $entidad =  $entidadDao->where('campo1', '=', $campo1)
                                   ->where('campo2', '=', $campo2)
                                   ->first();
                      
            if($entidad)
            {
                $datos = [
                    'id' => $entidad->id,
                    'campo1' => $campo1,
                    'campo2' => $campo2,
                    'campo3' => $entidad->campo3,
                ];   

                $token = Token::CrearToken($datos);
                $mensaje = array("Mensaje" => "Bienvenido " . $campo1, "Token" => $token);
            } 
            else
                $mensaje = array("Estado" => "Error", "Mensaje" => "Mail y/o clave incorrectos");                 
        }
        catch(Exception $e)
        {
            $error = $e->getMessage();
            $mensaje =  array("Estado" => "Error", "Mensaje" => $error); 
        }

        return $response->withJson($mensaje, 200);
    }

    public function AltaEntidad($request, $response, $args)
    {
        try
        {           
            $data = file_get_contents('php://input');
            $entidadAux = json_decode($data);
             
            $campo1 = $entidadAux->campo1;
            $campo2 = $entidadAux->campo2;
            $campo3 = strtolower($entidadAux->campo3);
            $entidad = new App\Models\Entidad;

            if($campo3  == 'alumno' || $campo3  == 'profesor' || $campo3  == 'administrador')
            {
                $entidad->campo1 = $campo1;
                $entidad->campo2 = $campo2;
                $entidad->campo3 = $campo3;
                $entidad->save();
                $mensaje = array("Estado" => "Ok", "Mensaje" => "Usuario " . $campo1 . " guardado correctamente");
            }
            else
            {   
                $mensaje = array("Estado" => "Error", "Mensaje" => "Tipo de usuario incorrecto");                
            }
        }   
        catch(Exception $e)
        {
            $error = $e->getMessage();
            $mensaje = array("Estado" => "Error", "Mensaje" => $error);
        }
        return $response->withJson($mensaje, 200);
    }

    public function ObtenerEntidades($request, $response, $args)
    {
        try
        {
            $campo3 = $args['campo3'];
            $entidadDao = new App\Models\Entidad;

            if($campo3 == 'todos')
            {
                $entidades = $entidadDao->get();
                return $response->withJson($entidades, 200);
            }
            else
            {
                $entidades = $entidadDao->where('campo3', '=', $campo3)
                                        ->get();
                return $response->withJson($entidades, 200);
            }   
        }
        catch(Exception $e)
        {
            $error = $e->getMessage();
            return array("Estado" => "Error", "Mensaje" => $error);
        }
    }
    
}
?>
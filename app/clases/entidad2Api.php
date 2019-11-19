<?php

class Entidad2Api
{

    public function AltaEntidad2($request, $response, $args)
    {
        try
        {           
            $data = file_get_contents('php://input');
            $entidad2Aux = json_decode($data);
            
            $campo1 = strtolower($entidad2Aux->campo1);
            $campo2 = $entidad2Aux->campo2;
            $campo3 = $entidad2Aux->campo3;
            $campo4 = strtolower($entidad2Aux->campo4);
            $entidad2 = new App\Models\Entidad2;

            $entidad2->campo1 = $campo1;
            $entidad2->campo2 = $campo2;
            $entidad2->campo3 = $campo3;
            $entidad2->campo4 = $campo4;
            $entidad2->save();
            $mensaje = array("Estado" => "Ok", "Mensaje" => "Materia " . $campo1 . " guardada correctamente");
        }   
        catch(Exception $e)
        {
            $error = $e->getMessage();
            $mensaje = array("Estado" => "Error", "Mensaje" => $error);
        }
        return $response->withJson($mensaje, 200);
    }

    public function ObtenerEntidades2($request, $response, $args)
    {
        try
        {
            $entidades2Dao = new App\Models\Entidad2;
            $entidades2 = $entidades2Dao->get();

            return $response->withJson($entidades2, 200);
        }
        catch(Exception $e)
        {
            $error = $e->getMessage();
            return array("Estado" => "Error", "Mensaje" => $error);
        }
    }
}

?>
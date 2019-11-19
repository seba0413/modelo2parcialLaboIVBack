<?php

class Entidad1_Entidad2Api
{
    public function AltaEntidad1_2($request, $response, $args)
    {
        try
        {           
            $data = file_get_contents('php://input');
            $entidad1_2Aux = json_decode($data);

            $entidad1_2 = new App\Models\Entidad1_Entidad2;

            $entidadValidacion = $entidad1_2->where('idEntidad1', '=', $entidad1_2Aux->idEntidad1)
                                            ->where('idEntidad2', '=', $entidad1_2Aux->idEntidad2)
                                            ->first();
            if($entidadValidacion)
            {
                $respuesta = array("Estado" => "Ok", "Mensaje" => "Ya estás inscripto a esta materia");
            }   
            else
            {
                $entidad1_2->idEntidad1 = $entidad1_2Aux->idEntidad1;
                $entidad1_2->idEntidad2 = $entidad1_2Aux->idEntidad2;
                $entidad1_2->save();
    
                $entidad2Dao = new App\Models\Entidad2;
                $entidad2 = $entidad2Dao->where('id', '=', $entidad1_2Aux->idEntidad2)->first();
                $entidad2->campo3 = $entidad2->campo3 - 1;
                $entidad2->save();
    
                $respuesta = array("Estado" => "Ok", "Mensaje" => "Inscripción realizada");
            }                      
        }
        catch (Exception $e)
        {
            $error = $e->getMessage();
            $respuesta = array("Estado" => "Error", "Mensaje" => $error);
        }

        return $response->withJson($respuesta, 200);
    }
}

?>
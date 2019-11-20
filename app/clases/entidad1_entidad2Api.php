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
                $respuesta = array("Estado" => "Alerta", "Mensaje" => "Ya estás inscripto a esta materia");
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

    public function ListarEntidad1_2($request, $response, $args)
    {
        try
        {
            $idEntidad1 = $args['idEntidad1'];
            $entidad1_2Dao = new App\Models\Entidad1_Entidad2;

            $entidades2 = $entidad1_2Dao->where('idEntidad1', '=', $idEntidad1)
                                        ->join('entidades2', 'entidades2.id', '=', 'entidad1_entidad2.idEntidad2')
                                        ->get();    
                                        
            return $response->withJson($entidades2, 200);                            
        }
        catch(Exception $e)
        {
            $error = $e->getMessage();
            $respuesta = array("Estado" => "Error", "Mensaje" => $error);
            return $response->withJson($respuesta, 200);
        }
    }

    public function ListarItem2_Item3($request, $response, $args)
    {
        try
        {
            $idEntidad2 = $args['idEntidad2'];
            $entidad1_2Dao = new App\Models\Entidad1_Entidad2;
            $entidad1_2 = $entidad1_2Dao->where('idEntidad2', '=', $idEntidad2)
                                        ->join('entidades', 'entidades.id', '=', 'entidad1_entidad2.idEntidad1')
                                        ->get();

            return $response->withJson($entidad1_2, 200);                             
        }   
        catch(Exception $e)
        {
            return $response->withJson(array("Estado" => "Error", "Mensaje" => $e->getMessage()),200);
        }
    }
}

?>
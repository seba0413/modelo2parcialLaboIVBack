<?php

class MateriaApi
{
    public function Materia($request, $response, $args)
    {
        try
        {
            $parametros = $request->getParsedBody();
            $nombre = $parametros['nombre'];
            $cuatrimestre = $parametros['cuatrimestre'];
            $cupos = $parametros['cupos'];

            if($cuatrimestre == 1 || $cuatrimestre == 2)
            {
                $materia = new App\Models\Materia;
                $materia->nombre = $nombre;
                $materia->cuatrimestre = $cuatrimestre;
                $materia->cupos = $cupos;
                $materia->save();
                $mensaje = array("Estado" => "OK", "Mensaje" => "Materia " . $nombre . " cargada correctamente");
            }
            else
            {
                $mensaje = array("Estado" => "Error", "Mensaje" => "Numero de cuatrimestre incorrecto");
            }

        }
        catch(Exception $e)
        {
            $error = $e->getMessage();
            $mensaje = array("Estado" => "Error", "Mensaje" => $error);
        }   
        return $response->withJson($mensaje, 200);
    }
}

?>
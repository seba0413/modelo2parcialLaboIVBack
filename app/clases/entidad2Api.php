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
            return $response->withJson(array("Estado" => "Error", "Mensaje" => $e->getMessage()),200);
        }
        return $response->withJson($mensaje, 200);
    }

    public function GuardarFotoMateria($request, $response, $args)
    {
        $url = 'http://localhost:80/2doParcial/public/fotosMaterias/';
        try
        {
            $foto = $_FILES['image'];
            $ruta = $foto['tmp_name'];
            $extension = explode(".",$foto['name']);
            $index = count($extension) - 1; 
            $rutafoto = "./fotosMaterias/{$extension[0]}.{$extension[$index]}";
            $fecha = date("d") . "-" . date("m") . "-" . date("Y");
            move_uploaded_file($ruta, $rutafoto); 

            $entidad2Dao = new App\Models\Entidad2;
            $entidad2 = $entidad2Dao    ->orderBy('id', 'desc')
                                        ->first();

            $entidad2->foto = $url . $extension[0] . '.' . $extension[$index];
            $entidad2->save();

            return $response->withJson(array("Estado" => "Ok", "Mensaje" => "Foto Guardada"),200);  
            
        }
        catch(Exception $e)
        {     
            return $response->withJson(array("Estado" => "Error", "Mensaje" => "Error al guardar la foto"),200);
        }
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
            return $response->withJson(array("Estado" => "Error", "Mensaje" => $e->getMessage()),200);
        }
    }

    public function ObtenerEntidades2_Item3($request, $response, $args)
    {
        try
        {
            $idEntidad1Item3 = $args['idItem3'];
            $entidad1Dao = new App\Models\Entidad;
            $entidad1 = $entidad1Dao->where('id', '=', $idEntidad1Item3)->first();

            $entidad2Dao = new App\Models\Entidad2;
            $entidades2 = $entidad2Dao->where('campo4', '=', $entidad1->campo1)->get();

            return $response->withJson($entidades2, 200);
        }
        catch(Exception $e)
        {
            return $response->withJson(array("Estado" => "Error", "Mensaje" => $e->getMessage()),200);
        }
    }
}

?>
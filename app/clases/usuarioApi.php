<?php

require_once 'token.php';
require_once "../app/models/dictada.php";
require_once "../app/models/cursada.php";

class UsuarioApi 
{
    public function Login($request, $response, $args)
    {
        try
        {
            $parametros = $request->getParsedBody();
            $nombre = $parametros['nombre'];
            $clave = $parametros['clave'];

            $usuario = new App\Models\Usuario;

            $usuarioDao =  $usuario->where('nombre', '=', $nombre)
                                   ->where('clave', '=', $clave)
                                   ->first();
                      
            if($usuarioDao)
            {
                $datos = [
                    'id' => $usuarioDao->id,
                    'nombre' => $nombre,
                    'clave' => $clave,
                    'tipo' => $usuarioDao->tipo,
                    'legajo' => $usuarioDao->legajo
                ];   

                $token = Token::CrearToken($datos);
                $mensaje = array("Mensaje" => "Bienvenido " . $nombre, "Token " => $token);
            } 
            else
                $mensaje = array("Estado" => "Error ", "Mensaje " => "Usuario y/o clave incorrectos");                 
        }
        catch(Exception $e)
        {
            $error = $e->getMessage();
            $mensaje =  array("Estado" => "Error ", "Mensaje " => $error); 
        }

        return $response->withJson($mensaje, 200);
    }

    public function ActualizarDatos($request, $response, $args)
    {
        try
        {
            $legajo = $args['legajo'];
            $parametros = $request->getParsedBody();
            $email = $parametros['email'];                 
            $usuarioDao = new App\Models\Usuario;
            $usuario = $usuarioDao->where('legajo', '=', $legajo)->first();

            if($usuario->tipo == 'alumno') 
            {
                if($_FILES)
                    $foto = $_FILES['foto']; 
                UsuarioApi::GuardarFoto($foto, $usuario->nombre, $usuario->legajo);
                $usuario->email = $email;
                $usuario->save();
                $mensaje = array("Estado" => "Ok", "Mensaje" => "El usuario " . $usuario->nombre . " actualizó sus datos");                
            }
            else if($usuario->tipo == 'profesor')
            {                
                $materias = $parametros['materias'];
                $arrayMaterias = explode(",", $materias);

                $materia = new App\Models\Materia;
                $materiasDao = $materia->all();

                $existe = false;

                for($i = 0; $i < count($arrayMaterias); $i++)
                {
                    for($j = 0; $j < count($materiasDao); $j++)
                    {
                        if($arrayMaterias[$i] == $materiasDao[$j]->nombre)
                        {
                            $existe = true;
                            break;
                        }
                    }
                    if(!$existe)
                    {
                        $mensaje = array("Estado" => "Error", "Mensaje" => "La materia " . $arrayMaterias[$i] . " no esta cargada en el sistema"); 
                        return $response->withJson($mensaje, 200);
                    }
                    $existe = false;
                }

                for($i = 0; $i < count($arrayMaterias); $i++)
                {
                    $dictada = new App\Models\Dictada;
                    $dictada->profesor = $usuario->nombre;
                    $dictada->materia = $arrayMaterias[$i];
                    $dictada->save();
                }

                $usuario->email = $email;
                $usuario->save();

                $mensaje = array("Estado" => "Ok", "Mensaje" => "El usuario " . $usuario->nombre . " actualizó sus datos");
            }
            else
            {
                if($_FILES)
                    $foto = $_FILES['foto']; 
                UsuarioApi::GuardarFoto($foto, $usuario->nombre, $usuario->legajo);
                
                $materias = $parametros['materias'];
                $arrayMaterias = explode(",", $materias);

                for($i = 0; $i < count($arrayMaterias); $i++)
                {
                    $dictada = new App\Models\Dictada;
                    $dictada->profesor = $usuario->nombre;
                    $dictada->materia = $arrayMaterias[$i];
                    $dictada->save();
                }               
                
                $usuario->email = $email;
                $usuario->save();

                $mensaje = array("Estado" => "Ok", "Mensaje" => "El usuario " . $usuario->nombre . " actualizó sus datos");
            }
        }
        catch(Exception $e)
        {
            $error = $e->getMessage();
            $mensaje = array("Estado" => "Error", "Mensaje" => $error);
        }
        return $response->withJson($mensaje, 200);
    }

    static function GuardarFoto($foto, $nombre, $legajo)
    {        
        $ruta = $foto['tmp_name'];
        $extension = explode(".",$foto['name']);
        $index = count($extension) - 1; 
        $nombreFoto = $nombre . "_" . $legajo . "." . $extension[$index];
        $rutafoto = "../app/fotos/" . $nombreFoto;
        
        move_uploaded_file($ruta, $rutafoto);     
        
        return $nombreFoto;
    }

    public function Inscripcion($request, $response, $args)
    {
        try
        {
            $idMateria = $args['id_materia'];
            $payload = $request->getAttribute("payload")["Payload"];
            $data = $payload->data;
    
            $materiaDao = new App\Models\Materia;
            $materia = $materiaDao->where('id', '=', $idMateria)->first();

            if($materia)
            {
                if($materia->cupos > 0)
                {
                    $materia->cupos = $materia->cupos - 1;
                    $materia->save();
        
                    $cursada = new App\Models\Cursada;
                    $cursada->alumno = $data->nombre;
                    $cursada->materia = $materia->nombre;
                    $cursada->save();

                    $mensaje = array("Estado" => "Ok", "Mensaje" => "El alumno " . $data->nombre . " se inscribio a la materia " . $materia->nombre);
                }
                else
                    $mensaje = array("Estado" => "Error", "Mensaje" => "No hay cupos para la materia " . $materia->nombre);
            }
            else
                $mensaje = array("Estado" => "Error", "Mensaje" => "Id de materia inexistente " . $materia->nombre);
        }
        catch(Exception $e)
        {
            $error = $e->getMessage();
            $mensaje = array("Estado" => "Error", "Mensaje" => $error);
        }

        return $response->withJson($mensaje, 200);
    }

    public function Materias($request, $response, $args)
    {
        try
        {
            $payload = $request->getAttribute("payload")["Payload"];
            $data = $payload->data;

            if($data->tipo == 'alumno')
            {
                $cursadasDao = new App\Models\Cursada;
                $cursadas = $cursadasDao->where('alumno', '=', $data->nombre)->get();

                if($cursadas->isEmpty())
                {
                    $mensaje = array("Estado" => "Error", "Mensaje" => "No esta inscripto en ninguna materia");
                    return $response->withJson($mensaje, 200);
                }
                else
                {
                    echo 'Materias inscriptas:' . "\n";

                    for($i = 0; $i < count($cursadas); $i++)
                    {
                        echo $cursadas[$i]->materia . "\n";
                    }
                }
            }
            else if($data->tipo == 'profesor')
            {
                $dictadasDao = new App\Models\Dictada;
                $dictadas = $dictadasDao->where('profesor', '=', $data->nombre)->get();

                if($dictadas->isEmpty())
                {
                    $mensaje = array("Estado" => "Error", "Mensaje" => "No tiene materias a cargo");
                    return $response->withJson($mensaje, 200);
                }
                else
                {
                    echo 'Materias a cargo:' . "\n";

                    for($i = 0; $i < count($dictadas); $i++)
                    {
                        echo $dictadas[$i]->materia . "\n";
                    }
                }
            }
            else
            {
                $dictadasDao = new App\Models\Dictada;
                $dictadas = $dictadasDao->all();

                if($dictadas->isEmpty())
                {
                    $mensaje = array("Estado" => "Error", "Mensaje" => "No se está dictando ninguna materia");
                    return $response->withJson($mensaje, 200);
                }
                else
                {
                    echo 'Materias dictadas y profesores a cargo:' . "\n";

                    for($i = 0; $i < count($dictadas); $i++)
                    {
                        echo 'Materia: ' . $dictadas[$i]->materia . '. Profesor a cargo: ' . $dictadas[$i]->profesor . "\n";
                    }
                }            
            }
        }
        catch(Exception $e)
        {
            $error = $e->getMessage();
            $mensaje = array("Estado" => "Error", "Mensaje" => $error);
            return $response->withJson($mensaje, 200);
        }
    }

    public function MateriasInscripciones($request,$response, $args)
    {
        try
        {
            $idMateria = $args['id'];
            $payload = $request->getAttribute("payload")["Payload"];
            $data = $payload->data;

            if($data->tipo == 'admin')
            {
                $materiaDao = new App\Models\Materia;
                $materia = $materiaDao->where('id', '=', $idMateria)->first();

                $cursadaDao = new App\Models\Cursada;
                $cursadas = $cursadaDao->where('materia', '=', $materia->nombre)->get();

                if($cursadas->isEmpty())
                {
                    $mensaje = array("Estado" => "Error", "Mensaje" => "La materia no tiene alumnos cursando");
                    return $response->withJson($mensaje, 200);
                }
                else
                {
                    echo 'Alumnos cursando la materia:' . "\n";

                    for($i = 0; $i < count($cursadas); $i++)
                    {
                        echo $cursadas[$i]->alumno . "\n";
                    }
                }
            }
            if($data->tipo == 'profesor')
            {
                $dictada = new App\Models\Dictada;
                $dictadaProfesor = $dictada->where('profesor', '=', $data->nombre)
                                            ->where('id', '=', $idMateria)                    
                                            ->first();

                if(!$dictadaProfesor)
                {
                    echo 'No esta a cargo de esta materia';
                }
                else
                {                
                    $cursada = new App\Models\Cursada;
                    $alumnosInscriptos = $cursada->where('materia', '=', $dictadaProfesor->materia)->get();

                    if($alumnosInscriptos->isEmpty())
                        echo 'La materia no tiene alumnos inscriptos';
                    else
                    {
                        echo 'Alumnos inscriptos' . "\n";
                        for($i = 0; $i < count($alumnosInscriptos); $i++)
                        {
                            echo $alumnosInscriptos[$i]->alumno . "\n";
                        }
                    }
                }
            }
        }
        catch(Exception $e)
        {            
            $error = $e->getMessage();
            $mensaje = array("Estado" => "Error", "Mensaje" => $error);
            return $response->withJson($mensaje, 200);
        } 
    }
}

?>
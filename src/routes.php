<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

require_once "../app/clases/usuarioApi.php";
require_once "../app/models/usuario.php";
require_once "../app/clases/materiaApi.php";
require_once "../app/models/materia.php";
require_once "middleware.php";



require_once "../app/clases/entidadApi.php";
require_once "../app/models/entidad.php";


return function (App $app) {
    $container = $app->getContainer();
    
    $app->post('/entidad/', \EntidadApi::class . ':AltaEntidad');







    //------------------------------------------ Modelo programación -------------

    $app->post('/usuario/', \UsuarioApi::class . ':AltaUsuario');
    $app->post('/login/', \UsuarioApi::class . ':Login');
    $app->post('/materia/', \MateriaApi::class . ':Materia')
    ->add(\Middleware::class . ':ValidarAdmin')
    ->add(\Middleware::class . ':ValidarToken');

    $app->post('/usuario/{legajo}', \UsuarioApi::class . ':ActualizarDatos')
    ->add(\Middleware::class . ':ValidarToken');

    $app->post('/inscripcion/{id_materia}', \UsuarioApi::class . ':Inscripcion')
    ->add(\Middleware::class . ':ValidarAlumno')
    ->add(\Middleware::class . ':ValidarToken');

    $app->get('/materias/', \UsuarioApi::class . ':materias')
    ->add(\Middleware::class . ':ValidarToken');

    $app->get('/materias/{id}', \UsuarioApi::class . ':MateriasInscripciones')
    ->add(\Middleware::class . ':ValidarToken');
};

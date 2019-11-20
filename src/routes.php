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
require_once "../app/clases/entidad2Api.php";
require_once "../app/models/entidad2.php";
require_once "../app/clases/entidad1_entidad2Api.php";
require_once "../app/models/entidad1_entidad2.php";


return function (App $app) {
    $container = $app->getContainer();

    //PARCIAL LABO IV 
    
    $app->post('/entidad/', \EntidadApi::class . ':AltaEntidad');
    $app->post('/entidad/login/', \EntidadApi::class . ':Login');
    $app->get('/entidades/{campo3}/', \EntidadApi::class . ':ObtenerEntidades');
    $app->get('/entidad/{id}', \EntidadApi::class . ':ObtenerEntidad');
    

    $app->post('/entidad2/alta/', \Entidad2Api::class . ':AltaEntidad2')
    ->add(\Middleware::class . ':ValidarAdmin')
    ->add(\Middleware::class . ':ValidarToken');    
    $app->get('/entidades2/', \Entidad2Api::class . ':ObtenerEntidades2');
    $app->get('/entidades2/{idItem3}', \Entidad2Api::class . ':ObtenerEntidades2_Item3');


    $app->post('/entidad1_2/alta/', \Entidad1_Entidad2Api::class . ':AltaEntidad1_2')
    ->add(\Middleware::class . ':ValidarAlumno')
    ->add(\Middleware::class . ':ValidarToken');

    $app->get('/entidad1_2/{idEntidad1}', \Entidad1_Entidad2Api::class . ':ListarEntidad1_2');

















    

    //PARCIAL PROGRAMACION III

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

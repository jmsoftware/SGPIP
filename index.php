<?php

require("Recursos/Loader.php");
$loader = new Loader($_GET);//se envia la URL (solicitud)
$controller = $loader->crearControlador(); //se crea el controlador basado en la solicitud
$controller->ejecutarAccion(); //se ejecuta la accion en este controlador basado en la accion solicitada por la url

?>
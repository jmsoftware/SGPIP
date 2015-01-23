<?php

require_once('Base.php');

class ControladorLogin extends ControladorBase
{    
    public function __construct($action, $id, $urlValues) {
        parent::__construct($action, $id, $urlValues);        
    }
    
    protected function Principal()
    {
        $this->Contenido = file_get_contents("Vista/Contenido/Login.html");     
        $pagina = $this->MostarElementos('',''); 
        print $pagina;  
    }

    protected function Logout()
    {
        //$this->Login->Salir();
        header("Location: http://". $_SERVER['HTTP_HOST'].Constantes::Path."/Login/Principal" );
        exit;
    }
}

?>
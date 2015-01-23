<?php

require_once('Base.php');

class ControladorError extends ControladorBase
{    
    //add to the parent constructor
    public function __construct($action, $id, $urlValues) {
        parent::__construct($action, $id, $urlValues);     
        $this->Contenido = file_get_contents("Vista/Contenido/Error.html");   
    }
    
    //bad URL request error
    protected function badURL()
    {
        $diccionario_Error = array('{icono_error}'=>'fa-exclamation-triangle','{mensaje_error}'=>'La Página a la que intenta acceder No Existe');                 
        $this->Contenido = str_replace(array_keys($diccionario_Error), array_values($diccionario_Error), $this->Contenido);
        $html = $this->MostarElementos('','Error');
        print $html;
    }

    protected function accesRestricted()
    {
        $diccionario_Error = array('{icono_error}'=>'fa-ban','{mensaje_error}'=>'Ud No Posee los Privilegios necesarios para acceder a esta Página');            
        $this->Contenido = str_replace(array_keys($diccionario_Error), array_values($diccionario_Error), $this->Contenido);
        $html = $this->MostarElementos('','Error');
        print $html;
    }
}

?>


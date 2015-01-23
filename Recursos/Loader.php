<?php

class Loader {
    
    private $NombreControlador;//almacena el nombre del controlador (del archivo) invocado por la url
    private $ClaseControlador;//almacena el controlador (la clase)
    private $Accion;//almacena la accion invocado por la url
    private $url;//almacena la url
    private $id;//almacena la url
    private $Modelo;    
    
    public function __construct($urlvalues) 
    {
        $this->url = $urlvalues;//almacenamos la url obtenida de GET
        
        //Procedemos a obtener el controlador que se esta solicitando
        if ($this->url['controlador'] == "")//si no hay controlador el controlador el de la pagina principal
        {
            $this->NombreControlador = "Inicio";
            $this->ClaseControlador = "ControladorInicio";
        } else 
        {
            $this->NombreControlador = ucfirst(strtolower($this->url['controlador']));///se el nombre del controlador solicitado (archivo.php)
            $this->ClaseControlador = "Controlador".ucfirst(strtolower($this->url['controlador'])) ;//se almacena el controlador (class archivo)
        }
        
        //procedemos a obtener la accion que este debe realizar
        if ($this->url['accion'] == "")//si no hay accion por defecto es index (pagina principal)        
            $this->Accion = "Principal"; 
        else         
            $this->Accion = $this->url['accion'];        

        if ($this->url['id'] == "")//si no hay accion por defecto es index (pagina principal)        
            $this->id = '0'; 
        else        
            $this->id = $this->url['id'];        
    }                  
 
    //Ahor transformamos la peticion del controlador en un objeto
    public function crearControlador() {
        //Validamos la existencia de los ARCHIVO de controlador
        //Si existen los invocamos, si no invocamos al controlador error.php por defecto     

        if (file_exists("Controlador/" . $this->NombreControlador . ".php") )
        {
            require("Controlador/" . $this->NombreControlador . ".php");//invocamos al controlador que se requiere  
        } 
        else 
        {
            require("Controlador/Error.php");
            return new ControladorError("badurl", $this->id, $this->url);//retornamos por defecto la accion de badurl
        }
                
        //Validamos que contenga la CLASE del controlador invocado
        if (class_exists($this->ClaseControlador)) 
        {                          
            //La Clase contiene el metodo de la accion solicitada
            if (method_exists($this->ClaseControlador,$this->Accion))
            {
                return new $this->ClaseControlador($this->Accion, $this->id, $this->url);
            } 
            else 
            {
                require("Controlador/Error.php");
                return new ControladorError("badurl",$this->id, $this->url);
            }           
        } 
        else 
        {  
            require("Controlador/Error.php");
            return new ControladorError("badurl", $this->id, $this->url);
        }
    }
}

?>
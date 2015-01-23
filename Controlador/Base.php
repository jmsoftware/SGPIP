<?php
//require_once('Modelo/Login.php');
if(class_exists('Constantes') === false or !class_exists('Constantes'))
    include_once('Recursos/Constantes.php');

abstract class ControladorBase 
{    
    protected $URL;
    protected $Accion;
    protected $Modelo;
    protected $ID;
    //Scripts
    protected $Estilo='';
    protected $Script='';
    //Vistas
    protected $Header='';
    protected $User='';
    protected $Aside='';
    protected $Contenido='';
    protected $Footer='';
    //Login
    protected $Login;

    public function __construct($action, $id, $urlValues)//obtener las funciones
    {
        //$this->Login = new ModeloLogin();
        $this->Accion = $action;
        $this->URL = $urlValues;   
        $this->ID = $id; 
    }
        
    //Ejecutamos la accion a realizar
    public function ejecutarAccion() {
        return $this->{$this->Accion}();
    }

    //PAGINA
    public function MostarElementos($js='', $css='')
    {
        //Base HTML
        $pagina = $this->Auxiliares(file_get_contents('Vista/Base.html'), $js, $css);

        //Contenido HTML
        $pagina = preg_replace('/\#HEADER\#/ms', $this->Header, $pagina);
        $pagina = preg_replace('/\#ASIDE\#/ms', $this->Aside, $pagina);
        $pagina = preg_replace('/\#USER\#/ms', $this->User, $pagina);
        $pagina = preg_replace('/\#CONTENIDO\#/ms', $this->Contenido, $pagina);
        $pagina = preg_replace('/\#FOOTER\#/ms', $this->Footer, $pagina);

        return $this->Ruta($pagina);
    }

    private function Ruta($pagina)
    {
        $diccionario_ruta = array('{elemento_ruta}'=>Constantes::Path); 
        return str_replace(array_keys($diccionario_ruta), array_values($diccionario_ruta), $pagina);        
    } 

    private function Auxiliares($html, $js, $css)
    {
        $aux = array();
        //js
        $js_aux='';
        if(is_array($js)){
            foreach ($js as $key => $value)             
                $js_aux.="<script src='{elemento_ruta}/Vista/JS/".$value.".js'></script>";            
        }
        else if(!empty($js))        
            $js_aux="<script src='{elemento_ruta}/Vista/JS/".$js.".js'></script>";
        //css
        $css_aux='';        
        if(is_array($css)){
            foreach ($css as $key => $value)             
                $css_aux.="<link href='{elemento_ruta}/Vista/CSS/".$value.".css' rel='stylesheet' />";            
        }
        else if(!empty($css))        
            $css_aux="<link href='{elemento_ruta}/Vista/CSS/".$css.".css' rel='stylesheet' />";    

        $cont = ($this->URL["controlador"] != "") ? $this->URL["controlador"] : "Inicio" ; 
        $acc = ($this->URL["accion"] != "") ? $this->URL["accion"] : "Principal" ; 

        $diccionario_elemento = array('{elemento_titulo}'=> $cont." | ".$acc,
                                      '{elemento_script}'=>$js_aux,
                                      '{elemento_estilo}'=>$css_aux,
                                      '{elemento_ruta}'=>Constantes::Path); 

        return str_replace(array_keys($diccionario_elemento), array_values($diccionario_elemento), $html);
    } 

    public function CargarHeader($fuente='')
    {
        $diccionario_elemento = array('{nombre_usuario}'=>$this->Login->GetNombre(),                                      
                                      '{elemento_ruta}'=>Constantes::Path); 
        return str_replace(array_keys($diccionario_elemento), array_values($diccionario_elemento), $fuente);
    }

    public function CargarID($vista)
    {
        $diccionario_elemento = array('{id_inquilino}'=>$this->Login->GetInquilino(),
                                        '{id_usuario}'=>$this->Login->GetCodigo()); 
        return str_replace(array_keys($diccionario_elemento), array_values($diccionario_elemento), $vista);
    }

    public function CargarTitulo($icono='', $principal='', $secundario='', $decripcion='')
    {
        $titulo = file_get_contents('Vista/Secciones/Titulo.html');
        $diccionario_titulo = array('{Titulo_Icono}'=>$icono,   
                                      '{Titulo_Princial}'=>$principal,                                   
                                      '{Titulo_Secundario}'=>$secundario,
                                      '{Titulo_Descripcion}'=>$decripcion); 
        return str_replace(array_keys($diccionario_titulo), array_values($diccionario_titulo), $titulo);
    }

    public function CargarAside($uno='', $dos='')
    {
        $aside = file_get_contents('Vista/Secciones/Aside.html');

        $datos = $this->Login->ListarPrivilegios();
        $base="<li".(($uno == "Inicio") ? " class='k-state-active el_seleccionado' ":"")."><a href='".Constantes::Path."/Inicio/Principal' ><i class='fa fa-home'></i>&nbsp;Inicio".(($uno == "Inicio") ? "<span class='prin_seleccionado'></span>":"")."</a></li>";
        $principal='';

        foreach ($datos as $fila) 
        {
            if($fila["nombre_modulo"] != $principal)
            {
                if($principal != '')
                    $base.="</ul>";

                $base.="<li".(($uno == $fila["nombre_modulo"]) ? " class='k-state-active el_seleccionado' ":"")."><span><i class='fa ".$fila["imagen"]."'></i>&nbsp;".$fila["titulo_modulo"].(($uno == $fila["nombre_modulo"]) ? "<span class='prin_seleccionado'></span>":"")."</span><ul><li><a href='".Constantes::Path."/".$fila["nombre_modulo"]."/".$fila["nombre_secundario"]."' ".(($dos == $fila["nombre_secundario"]) ? " class='sub_selected' ":"")." >".$fila["titulo_secundario"]."</a></li>";
                $principal = $fila["nombre_modulo"];
            }
            else
            {
                $base.="<li><a href='".Constantes::Path."/".$fila["nombre_modulo"]."/".$fila["nombre_secundario"]."' ".(($dos == $fila["nombre_secundario"]) ? " class='sub_selected' ":"")." >".$fila["titulo_secundario"]."</a></li>";
            }
        }

        $base.="</ul></li>";

        $diccionario_elemento = array('{lista_base}'=>$base); 
        $aside = str_replace(array_keys($diccionario_elemento), array_values($diccionario_elemento), $aside);

        return $aside;
    }

    //USUARIO
    public function ComprobarLogin()
    {
        if(!$this->Login->EstadoLogin()) 
        {
            header("Location: http://". $_SERVER['HTTP_HOST'].Constantes::Path."/Login/Principal" );
            exit;
        }
    }

    public function ComprobarPrivilegio($prin='', $sec='')
    {
        $datos = $this->Login->ValidarPrivilegio($prin, $sec);

        if(count($datos) > 0)
        {
            $fila = $datos[0];
            $this->Titulo = $this->CargarTitulo($fila["imagen"], $fila["titulo_modulo"], $fila["titulo_secundario"], $fila["descripcion"]);
        }
        else        
        {
            header("Location: http://". $_SERVER['HTTP_HOST'].Constantes::Path."/Error/accesRestricted" );
            exit;
        }
    }
}

?>

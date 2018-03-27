<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
 require_once 'Common/database.php'; 
 require_once 'Common/function.php';
 require_once 'Model/Books.php'; 
 require_once 'config.php'; 
   
$app=isset($_REQUEST['app'])?$_REQUEST['app']:"index";
$mod=isset($_REQUEST['mod'])?$_REQUEST['mod']:"index";
if(file_exists(ROOT.'/Controller/'.ucwords($app).'Controller.php'))
{
        require_once ROOT.'/Controller/'.ucwords($app).'Controller.php';
        $class=ucwords($app)."Controller";
        if(method_exists($class, $mod))
        {
            $object= new $class();  
            $object->$mod();
        }else{
            echo "error method";
        }
}else{
    echo "error function";
}

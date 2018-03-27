<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

 
 function View($view,$arg=array()){
     extract($arg); 
    if(file_exists(ROOT.'/view/'.$view.'.php'))
    { 
        require   ROOT.'/view/'.$view.'.php'; 
    } 
 }

 function dd($param) {
    var_dump($param);
    exit;
}
function Redirect($param) {
    header("location: ".$param); 
    exit;
}

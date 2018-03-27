<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 
 * @param type $dir
 * @param type $recur
 * @return boolean
 */
if (!function_exists('readdir_phpfile')) {

    function readdir_phpfile($dir, $recur = false) {
        $files = [];
        if (!is_dir($dir)) {
            return [];
        }
        $handle = opendir($dir);

        if ($handle) {
            while (($fl = readdir($handle)) !== false) {
                $temp = $dir . DIRECTORY_SEPARATOR . $fl;
                //如果不加  $fl!='.' && $fl != '..'  则会造成把$dir的父级目录也读取出来
                if (is_dir($temp) && $recur && $fl != '.' && $fl != '..') {
                    if ($recur) {
                        $files = array_merge($files, readdir_phpfile($temp));
                    }
                } else if (is_file($temp) && $fl != '.' && $fl != '..') {
                    $path = pathinfo($temp);
                    !empty($path['extension']) && $path['extension'] == 'php' ? $files[] = $temp : null;
//                    echo 'require_once \''.$temp.'\';<br/>';
                }
            }
        }
        return $files;
    }

}

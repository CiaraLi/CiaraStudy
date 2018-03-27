<?php
define('DEFAULT_PIC','./img/default.png');
define('FIND_PIC','./img/none1.jpg;./img/none2.gif');

define('DIRPATH','../R-xuexi/');  

 
$path=[];
listDir(DIRPATH,$path);  

foreach($path as $key=>$val){
 		$same=comparePicture($val[0].'/'.$val[1]);
 		if($same){ 
 			 echo ' replace:-"' .$val[0].'/'.$val[1]."\"  \r\n<br/>";
			 copy(DEFAULT_PIC,$val[0].'/'.$val[1]) ;
 		}else{
 			//echo ' different:-"' .$val[0].'/'.$val[1].'"  <br/>';
 	  } 
}


function comparePicture($pic){ 
	$nolist=explode(';',FIND_PIC); 
	foreach($nolist as  $key=>$val){
			$size1=filesize($pic); 
			$size2=filesize($val);
			 if($size1==$size2){
				$fp1 = fopen($pic,'rb');
				$image1=bin2hex(fread($fp1, $size1)); 
			
				$fp2 = fopen($val,'rb');
				$image2=bin2hex(fread($fp2, $size2));
				 
				if($image1==$image2){ 
					 return true;
				}
			 }
	}
	return false;
}




function listDir($dir,&$path)
{
    if(is_dir($dir))
       {
         	if ($dh = opendir($dir))
        	{
            	while (($file = readdir($dh)) !== false)
            	{
                 	if((is_dir($dir."/".$file)) && $file!="." && $file!="..")
                	{
                     	listDir($dir."/".$file,$path);
                 	}
                	else
                	{
                     	if($file!="." && $file!="..")
                    	{
                         $path[]=[$dir,$file];
                      }
                 	}
            	}
            	closedir($dh);
         	}
       }
}
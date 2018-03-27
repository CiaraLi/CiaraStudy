<?php
$options=[0=>'北京大学',1=>'北京外国语学院',2=>'天津大学',3=>'北京戏剧学院',4=>'河北大学',5=>'河北工业学院',6=>'上海大学',7=>'南开大学',
        		8=>'北京交通大学',9=>'北京航天航空大学',10=>'河北经济学院'];
$search=isset($_POST['name'])? $_POST['name'] :"";

$return=[];
foreach($options as $key=>$val){
	if(strpos($val,trim($search))!==false){
		 $return[$key]=$val;
  }	
}

echo json_encode($return);
exit;

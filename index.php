<html>
<body>
<style>
	a{
		text-decoration: none;
	 }
	 .panle{
	 	 width: 29%;
	 	 margin: 1px;
     float: left;
	 	}
	.box {
    border: 1px solid #c1bebe;
    padding: 5px 10px;
    background: antiquewhite;
    opacity: .8;
    width: 80%;
    height: 100px;
    overflow: auto;
}
	</style>
<?php
include 'config.php';  
include 'menu.php';  
define('_ROOT',__DIR__);

$num=1;
foreach($apps as $key=>$app){
	echo '<div class="panle">';
	if(is_dir(_ROOT.'/'.$app['path'])){
		$index=_HOST.'/'.$app['path'].'/'.$app['index'];
		$readme=_ROOT.'/'.$app['path'].'/'.$app['readme'];
		$content=is_file(_ROOT.'/'.$app['path'].'/'.$app['readme'])?file_get_contents($readme):"暂无简介";
		 echo '<div class="app">
						<div><h4><a href="'.$index.'">'.$num.':'.$app['title'].'</a></h4>	</div>
						<div  class="box"><span>'.nl2br($content).'</span></div>
					</div>';
	   $num++;
	} 
	echo '</div>';
}
?> 
</body>
</html>
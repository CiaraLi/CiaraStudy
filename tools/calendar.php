﻿<meta charset="utf-8" />
<?php
//世纪万年历  
//这是唯一的设置-请输入php文件的位置  
$file="";  
$minyear=1901;
$maxyear=2020;
//农历每月的天数  
$everymonth=array(0=>array(8,0,0,0,0,0,0,0,0,0,0,0,29,30,7,1),  
1=>array(0,29,30,29,29,30,29,30,29,30,30,30,29,0,8,2),  
2=>array(0,30,29,30,29,29,30,29,30,29,30,30,30,0,9,3),  
3=>array(5,29,30,29,30,29,29,30,29,29,30,30,29,30,10,4),  
4=>array(0,30,30,29,30,29,29,30,29,29,30,30,29,0,1,5),  
5=>array(0,30,30,29,30,30,29,29,30,29,30,29,30,0,2,6),  
6=>array(4,29,30,30,29,30,29,30,29,30,29,30,29,30,3,7),  
7=>array(0,29,30,29,30,29,30,30,29,30,29,30,29,0,4,8),  
8=>array(0,30,29,29,30,30,29,30,29,30,30,29,30,0,5,9),  
9=>array(2,29,30,29,29,30,29,30,29,30,30,30,29,30,6,10),  
10=>array(0,29,30,29,29,30,29,30,29,30,30,30,29,0,7,11),  
11=>array(6,30,29,30,29,29,30,29,29,30,30,29,30,30,8,12),  
12=>array(0,30,29,30,29,29,30,29,29,30,30,29,30,0,9,1),  
13=>array(0,30,30,29,30,29,29,30,29,29,30,29,30,0,10,2),  
14=>array(5,30,30,29,30,29,30,29,30,29,30,29,29,30,1,3),  
15=>array(0,30,29,30,30,29,30,29,30,29,30,29,30,0,2,4),  
16=>array(0,29,30,29,30,29,30,30,29,30,29,30,29,0,3,5),  
17=>array(2,30,29,29,30,29,30,30,29,30,30,29,30,29,4,6),  
18=>array(0,30,29,29,30,29,30,29,30,30,29,30,30,0,5,7),  
19=>array(7,29,30,29,29,30,29,29,30,30,29,30,30,30,6,8),  
20=>array(0,29,30,29,29,30,29,29,30,30,29,30,30,0,7,9),  
21=>array(0,30,29,30,29,29,30,29,29,30,29,30,30,0,8,10),  
22=>array(5,30,29,30,30,29,29,30,29,29,30,29,30,30,9,11),  
23=>array(0,29,30,30,29,30,29,30,29,29,30,29,30,0,10,12),  
24=>array(0,29,30,30,29,30,30,29,30,29,30,29,29,0,1,1),  
25=>array(4,30,29,30,29,30,30,29,30,30,29,30,29,30,2,2),  
26=>array(0,29,29,30,29,30,29,30,30,29,30,30,29,0,3,3),  
27=>array(0,30,29,29,30,29,30,29,30,29,30,30,30,0,4,4),  
28=>array(2,29,30,29,29,30,29,29,30,29,30,30,30,30,5,5),  
29=>array(0,29,30,29,29,30,29,29,30,29,30,30,30,0,6,6),  
30=>array(6,29,30,30,29,29,30,29,29,30,29,30,30,29,7,7),  
31=>array(0,30,30,29,30,29,30,29,29,30,29,30,29,0,8,8),  
32=>array(0,30,30,30,29,30,29,30,29,29,30,29,30,0,9,9),  
33=>array(5,29,30,30,29,30,30,29,30,29,30,29,29,30,10,10),  
34=>array(0,29,30,29,30,30,29,30,29,30,30,29,30,0,1,11),  
35=>array(0,29,29,30,29,30,29,30,30,29,30,30,29,0,2,12),  
36=>array(3,30,29,29,30,29,29,30,30,29,30,30,30,29,3,1),  
37=>array(0,30,29,29,30,29,29,30,29,30,30,30,29,0,4,2),  
38=>array(7,30,30,29,29,30,29,29,30,29,30,30,29,30,5,3),  
39=>array(0,30,30,29,29,30,29,29,30,29,30,29,30,0,6,4),  
40=>array(0,30,30,29,30,29,30,29,29,30,29,30,29,0,7,5),  
41=>array(6,30,30,29,30,30,29,30,29,29,30,29,30,29,8,6),  
42=>array(0,30,29,30,30,29,30,29,30,29,30,29,30,0,9,7),  
43=>array(0,29,30,29,30,29,30,30,29,30,29,30,29,0,10,8),  
44=>array(4,30,29,30,29,30,29,30,29,30,30,29,30,30,1,9),  
45=>array(0,29,29,30,29,29,30,29,30,30,30,29,30,0,2,10),  
46=>array(0,30,29,29,30,29,29,30,29,30,30,29,30,0,3,11),  
47=>array(2,30,30,29,29,30,29,29,30,29,30,29,30,30,4,12),  
48=>array(0,30,29,30,29,30,29,29,30,29,30,29,30,0,5,1),  
49=>array(7,30,29,30,30,29,30,29,29,30,29,30,29,30,6,2),  
50=>array(0,29,30,30,29,30,30,29,29,30,29,30,29,0,7,3),  
51=>array(0,30,29,30,30,29,30,29,30,29,30,29,30,0,8,4),  
52=>array(5,29,30,29,30,29,30,29,30,30,29,30,29,30,9,5),  
53=>array(0,29,30,29,29,30,30,29,30,30,29,30,29,0,10,6),  
54=>array(0,30,29,30,29,29,30,29,30,30,29,30,30,0,1,7),  
55=>array(3,29,30,29,30,29,29,30,29,30,29,30,30,30,2,8),  
56=>array(0,29,30,29,30,29,29,30,29,30,29,30,30,0,3,9),  
57=>array(8,30,29,30,29,30,29,29,30,29,30,29,30,29,4,10),  
58=>array(0,30,30,30,29,30,29,29,30,29,30,29,30,0,5,11),  
59=>array(0,29,30,30,29,30,29,30,29,30,29,30,29,0,6,12),  
60=>array(6,30,29,30,29,30,30,29,30,29,30,29,30,29,7,1),  
61=>array(0,30,29,30,29,30,29,30,30,29,30,29,30,0,8,2),  
62=>array(0,29,30,29,29,30,29,30,30,29,30,30,29,0,9,3),  
63=>array(4,30,29,30,29,29,30,29,30,29,30,30,30,29,10,4),  
64=>array(0,30,29,30,29,29,30,29,30,29,30,30,30,0,1,5),  
65=>array(0,29,30,29,30,29,29,30,29,29,30,30,29,0,2,6),  
66=>array(3,30,30,30,29,30,29,29,30,29,29,30,30,29,3,7),  
67=>array(0,30,30,29,30,30,29,29,30,29,30,29,30,0,4,8),  
68=>array(7,29,30,29,30,30,29,30,29,30,29,30,29,30,5,9),  
69=>array(0,29,30,29,30,29,30,30,29,30,29,30,29,0,6,10),  
70=>array(0,30,29,29,30,29,30,30,29,30,30,29,30,0,7,11),  
71=>array(5,29,30,29,29,30,29,30,29,30,30,30,29,30,8,12),  
72=>array(0,29,30,29,29,30,29,30,29,30,30,29,30,0,9,1),  
73=>array(0,30,29,30,29,29,30,29,29,30,30,29,30,0,10,2),  
74=>array(4,30,30,29,30,29,29,30,29,29,30,30,29,30,1,3),  
75=>array(0,30,30,29,30,29,29,30,29,29,30,29,30,0,2,4),  
76=>array(8,30,30,29,30,29,30,29,30,29,29,30,29,30,3,5),  
77=>array(0,30,29,30,30,29,30,29,30,29,30,29,29,0,4,6),  
78=>array(0,30,29,30,30,29,30,30,29,30,29,30,29,0,5,7),  
79=>array(6,30,29,29,30,29,30,30,29,30,30,29,30,29,6,8),  
80=>array(0,30,29,29,30,29,30,29,30,30,29,30,30,0,7,9),  
81=>array(0,29,30,29,29,30,29,29,30,30,29,30,30,0,8,10),  
82=>array(4,30,29,30,29,29,30,29,29,30,29,30,30,30,9,11),  
83=>array(0,30,29,30,29,29,30,29,29,30,29,30,30,0,10,12),  
84=>array(10,30,29,30,30,29,29,30,29,29,30,29,30,30,1,1),  
85=>array(0,29,30,30,29,30,29,30,29,29,30,29,30,0,2,2),  
86=>array(0,29,30,30,29,30,30,29,30,29,30,29,29,0,3,3),  
87=>array(6,30,29,30,29,30,30,29,30,30,29,30,29,29,4,4),  
88=>array(0,30,29,30,29,30,29,30,30,29,30,30,29,0,5,5),  
89=>array(0,30,29,29,30,29,29,30,30,29,30,30,30,0,6,6),  
90=>array(5,29,30,29,29,30,29,29,30,29,30,30,30,30,7,7),  
91=>array(0,29,30,29,29,30,29,29,30,29,30,30,30,0,8,8),  
92=>array(0,29,30,30,29,29,30,29,29,30,29,30,30,0,9,9),  
93=>array(3,29,30,30,29,30,29,30,29,29,30,29,30,29,10,10),  
94=>array(0,30,30,30,29,30,29,30,29,29,30,29,30,0,1,11),  
95=>array(8,29,30,30,29,30,29,30,30,29,29,30,29,30,2,12),  
96=>array(0,29,30,29,30,30,29,30,29,30,30,29,29,0,3,1),  
97=>array(0,30,29,30,29,30,29,30,30,29,30,30,29,0,4,2),  
98=>array(5,30,29,29,30,29,29,30,30,29,30,30,29,30,5,3),  
99=>array(0,30,29,29,30,29,29,30,29,30,30,30,29,0,6,4),  
100=>array(0,30,30,29,29,30,29,29,30,29,30,30,29,0,7,5),  
101=>array(4,30,30,29,30,29,30,29,29,30,29,30,29,30,8,6),  
102=>array(0,30,30,29,30,29,30,29,29,30,29,30,29,0,9,7),  
103=>array(0,30,30,29,30,30,29,30,29,29,30,29,30,0,10,8),  
104=>array(2,29,30,29,30,30,29,30,29,30,29,30,29,30,1,9),  
105=>array(0,29,30,29,30,29,30,30,29,30,29,30,29,0,2,10),  
106=>array(7,30,29,30,29,30,29,30,29,30,30,29,30,30,3,11),  
107=>array(0,29,29,30,29,29,30,29,30,30,30,29,30,0,4,12),  
108=>array(0,30,29,29,30,29,29,30,29,30,30,29,30,0,5,1),  
109=>array(5,30,30,29,29,30,29,29,30,29,30,29,30,30,6,2),  
110=>array(0,30,29,30,29,30,29,29,30,29,30,29,30,0,7,3),  
111=>array(0,30,29,30,30,29,30,29,29,30,29,30,29,0,8,4),  
112=>array(4,30,29,30,30,29,30,29,30,29,30,29,30,29,9,5),  
113=>array(0,30,29,30,29,30,30,29,30,29,30,29,30,0,10,6),  
114=>array(9,29,30,29,30,29,30,29,30,30,29,30,29,30,1,7),  
115=>array(0,29,30,29,29,30,29,30,30,30,29,30,29,0,2,8),  
116=>array(0,30,29,30,29,29,30,29,30,30,29,30,30,0,3,9),  
117=>array(6,29,30,29,30,29,29,30,29,30,29,30,30,30,4,10),  
118=>array(0,29,30,29,30,29,29,30,29,30,29,30,30,0,5,11),  
119=>array(0,30,29,30,29,30,29,29,30,29,29,30,30,0,6,12),  
120=>array(4,29,30,30,30,29,30,29,29,30,29,30,29,30,7,1));  
//##############################  
//#农历天干  
$mten=array("null","甲","乙","丙","丁","戊","己","庚","辛","壬","癸");  
//#农历地支  
$mtwelve=array("null","子（鼠）","丑（牛）","寅（虎）","卯（兔）","辰（龙）",  
               "巳（蛇）","午（马）","未（羊）","申（猴）","酉（鸡）","戌（狗）","亥（猪）");  
//#农历月份  
$mmonth=array("闰","正","二","三","四","五","六",  
              "七","八","九","十","十一","十二","月");  
//#农历日  
$mday=array("null","初一","初二","初三","初四","初五","初六","初七","初八","初九","初十",  
            "十一","十二","十三","十四","十五","十六","十七","十八","十九","二十",  
            "廿一","廿二","廿三","廿四","廿五","廿六","廿七","廿八","廿九","三十");  
//##############################  
//#赋给初值  
//#天干地支  
$ten=0;  
$twelve=0;  
//#星期  
$week=5;  
//#农历日  
$md=0;  
//#农历月  
$mm=0;  
//#阳历总天数 至1900年12月21日  
$total=11;  
//#阴历总天数  
$mtotal=0;  
//##############################  
//#获得当日日期  
$today=getdate(); 
$year=isset($_REQUEST['year'])?$_REQUEST['year'] :"";
$month=isset($_REQUEST['month'])?$_REQUEST['month'] :"";
//#如果没有输入，设为当日日期  
if (empty($year) or empty($month) or ($year< $minyear or $year>$maxyear)  
    or ($month<1 or $month>12)){
     $year=$today["year"];  
     $month=$today["mon"];  
   }  
//##############################  
//#计算到所求日期阳历的总天数-自1900年12月21日始  
//#先算年的和  
for ($y=1901;$y<$year;$y++){  
      $total+=365;  
      if ($y%4==0) $total ++;  
    }  
//#再加当年的几个月  
switch ($month){  
         case 12:  
              $total+=30;  
         case 11:  
              $total+=31;  
         case 10:  
              $total+=30;  
         case 9:  
              $total+=31;  
         case 8:  
              $total+=31;  
         case 7:  
              $total+=30;  
         case 6:  
              $total+=31;  
         case 5:  
              $total+=30;  
         case 4:  
              $total+=31;  
         case 3:  
              $total+=28;  
         case 2:  
              $total+=31;  
       }  
//#如果当年是闰年还要加一天  
if ($year%4==0 and $month>2){  
     $total++;  
    }  
//#顺便算出当月1日星期几  
$week=($total+$week)%7;  
//##############################  
//#用农历的天数累加来判断是否超过阳历的天数  
$flag1=0;#判断跳出循环的条件  
$j=0;  
while ($j<=120){  
      $i=1;  
      while ($i<=13){  
            $mtotal+=$everymonth[$j][$i];  
            if ($mtotal>=$total){  
                 $flag1=1;  
                 break;  
               }  
            $i++;  
          }  
      if ($flag1==1) break;  
      $j++;  
    }   



//##############################  
//#计算所求月份1号的农历日期  
$md=$everymonth[$j][$i]-($mtotal-$total);  
//#月头空开的天数  
$k=$week;  
//#是否跨越一年  
switch ($month){  
         case 1:  
         case 3:  
         case 5:  
         case 7:  
         case 8:  
         case 10:  
         case 12:  
              $dd=31;  
              break;  
         case 4:  
         case 6:  
         case 9:  
         case 11:  
              $dd=30;  
              break;  
         case 2:  
              if ($year%4==0){  
                  $dd=29;  
                 }else{  
                  $dd=28;  
                 }  
              break;  
       }  
//#是否跨越一年  
$ty=0;  
if ((($everymonth[$j][0]<>0 and $i==13) or ($everymonth[$j][0]==0 and $i==12))  
       and $mtotal-$total<$dd) $ty=1;  
?>  
<html>  
<head>  
<title>世纪万年历</title>  
<style type="text/css">  
input { font-size:9pt;}  
A:link {text-decoration: none; color:000059}  
A:visited {text-decoration: none; color:000059}  
A:active {text-decoration: none;  }  
A:hover {text-decoration:none;color:red}  
body,table {font-size: 9pt}  
tr,td{font-size:9pt}   
</style>  
</head>  
<body alink="#FF0000" link="#000099" vlink="#CC6600" topmargin="8" leftmargin="0" bgColor="#FFFFFF">  
<?php  
    //打印年月抬头  
    echo "<p align=\"center\"><font size=\"6\"><b>".$year."年".$month."月</b></font>
      <a href=\"".$file."?year=".$today["year"]."&month=".$today["mon"]."\" >
     <span style=\"   font-size: 15px; padding: 2px;width:20px; height:20px; background-color:#0F0; border-radius:50px; \">今</span></a> </p>\n";
    
    if($ty==0)  
    {  
        echo "<p align=\"center\"><b><font size=\"4\">".$mten[$everymonth[$j][14]].$mtwelve[$everymonth[$j][15]]."年</font></b></p>";  
    }  
    else  
    {  
        echo "<p align=\"center\"><b><font size=\"4\">".$mten[$everymonth[$j][14]].$mtwelve[$everymonth[$j][15]]."/".$mten[$everymonth[$j+1][14]].$mtwelve[$everymonth[$j+1][15]]."年</font></b></p>";  
    }  
?>  
<div align="center">  
  <center>  
  <table border="1" width="85%" style="border-collapse:collapse;">  
    <tr>  
      <td align="center" bgcolor="#CCCCCC"><font color="#FF0000"><b>星期日</b></font></td>  
      <td width="14%" align="center" bgcolor="#CCCCCC"><b>星期一</b></td>  
      <td width="14%" align="center" bgcolor="#CCCCCC"><b>星期二</b></td>  
      <td width="14%" align="center" bgcolor="#CCCCCC"><b>星期三</b></td>  
      <td width="14%" align="center" bgcolor="#CCCCCC"><b>星期四</b></td>  
      <td width="14%" align="center" bgcolor="#CCCCCC"><b>星期五</b></td>  
      <td width="14%" align="center" bgcolor="#CCCCCC"><b>星期六</b></td>  
    </tr>  
<?php 
$day=1;  
$line=0;  
while ($day<=$dd){  
   echo "<tr>\n";  
   for ($s=0;$s<=6;$s++){  
         if ($k<>0 or $day>$dd){  
              echo "<td width=\"14%\" align=\"center\">　</td>\n";  
              $k--;  
         }else{  
    //设置字符颜色  
               switch ($s){  
                        case 1:  
                        case 2:  
                        case 3:  
                        case 4:  
                        case 5:  
                             $color="#000000";  
                             break;  
                        case 0:  
                             $color="#FF0000";  
                             break;  
                        case 6:  
                             $color="#008000";  
                             break;  
                      }  
//#生成中文农历  
               if ($md==1){#1日打印月份  
                    if ($everymonth[$j][0]<>0 and $everymonth[$j][0]<$i){  
                        $mm=$i-1;  
                    }else{  
                        $mm=$i;  
                    }  
                    if ($i==$everymonth[$j][0]+1 and $everymonth[$j][0]<>0) $chi=$mmonth[0].$mmonth[$mm];#闰月  
                    else $chi=$mmonth[$mm].$mmonth[13];  
               }else{  
                    $chi=$mday[$md];  
               }  
               if($year==$today["year"]&&$month==$today["mon"]&&$day==$today["mday"]){
               			$istoday="2px #0a0;background-color:#0f0";
               }elseif($day==$today["mday"]){
               			$istoday="2px #0f0;"; 
               }else{$istoday="1px ;"; }
               echo "<td width=\"14%\" style=\" border: solid $istoday\"  align=\"center\" $istoday><font color=\"$color\"><b>$day </b> <b><font size=\"2\">$chi</font></b></font></td>\n";  
               $day++;  
               $md++;  
               if ($md>$everymonth[$j][$i]){  
                    $md=1;  
                    $i++;  
                  }  
               if (($i>12 and $everymonth[$j][0]==0) or ($i>13 and $everymonth[$j][0]<>0)){  
                     $i=1;  
                     $j++;  
                  }  
           }  
       }  
   echo "</tr>\n";  
   $line++;  
}  
?>  
  </table>  
  </center>  
</div>  
<?php  
//#补足空行  
for ($l=1;$l<=(6-$line);$l++){  
      echo "<table border=\"0\" width=\"100%\">\n";  
      echo "<tr>\n";  
      echo "<td width=\"100%\"><font color=\"#CCFFFF\">a</font></td>\n";  
      echo "</tr>\n";  
      echo "</table>\n";  
    }  
//#打印上一月，下一月  
$ly=$ny=$year;  
$last=$month-1;  
if ($last==0){  
     $last=12;  
     $ly--;  
   }  
$next=$month+1;  
if ($next==13){  
     $next=1;  
     $ny++;  
   }  
if ($ly>=1901)  
echo "<p align=\"center\"><a href=\"".$file."?year=".$ly."&month=".$last."\"><<上一个月</a>   \n";  
else  
echo "<p align=\"center\">";  
if ($ny<=2020)  
echo "<a href=\"".$file."?year=".$ny."&month=".$next."\">下一个月>></a></p>\n";  
?>  
 <?php  
echo "<form method=\"POST\" action=\"".$file."\">\n";  
?>  
  <p align="center"><font color="#000000">年份：</font><select size="1" name="year">  
  <?php
  for( $y = $minyear;$y <= $maxyear;$y++){
  	$selected=($y==$year?"selected":"");
  	echo "<option $selected>$y</option>"  ;
  }
  ?></select>
  <font color="#000000">年</font><font color="#000000">      
  月份：<select size="1" name="month">  
  <?php
  for( $y = 1;$y <= 12;$y++){
  	$selected=($y==$month?"selected":"");
  	echo "<option $selected>$y</option>"  ;
  }
  ?></select>月 
      </font><input type="submit" value="查询" name="B1"></p>  
</form>  
</body>  
</html>
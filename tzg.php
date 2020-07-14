<?php
//error_reporting(0);


$words=$_POST['words']??'';//笔顺字

$bglx=$_POST['types']??'tzg';//表格类型，默认田字格
$bgcolor=$_POST['bgcolor']??'black';//表格颜色

if($_POST['bgcolor']!='black'){
	$bglx=$bglx.$bgcolor;//表格颜色变化
}

$z_color=$_POST['zcolor']??'black';//主字体颜色
$f_color=$_POST['fcolor']??'5';//辅字体颜色
$title=$_POST['title']??'';//辅字体颜色
$bs=$_POST['bs']??'0';//笔顺填充

/*过滤掉非中文*/
preg_match_all('/[\x{4e00}-\x{9fff}]+/u', $words, $words);
$words = implode('', $words[0] );


//没有文字，跳转
if(!$words){
	header("Location: /");
	exit();
}

/*主字体颜色*/
$color=[
'green'=>'0,176,80',//绿色
'black'=>'0,0,0',//黑色
'red'=>'152,15,41',//红色
];

/*辅字体颜色*/
$fz_color=[
'10'=>'255,255,255',//白色

'green1'=>'136,255,136',//绿色1
'green2'=>'153,255,153',//绿色2
'green3'=>'160,255,160',//绿色3
'green4'=>'170,255,170',//绿色4
'green5'=>'184,255,184',//绿色5
'green6'=>'204,255,204',//绿色6

'black1'=>'136,136,136',//黑色1
'black2'=>'153,153,153',//黑色2
'black3'=>'160,160,160',//黑色3
'black4'=>'170,170,170',//黑色4
'black5'=>'184,184,184',//黑色5
'black6'=>'204,204,204',//黑色6

'red1'=>'255,136,136',//红色1
'red2'=>'255,153,153',//红色2
'red3'=>'255,160,160',//红色3
'red4'=>'255,170,170',//红色4
'red5'=>'255,184,184',//红色5
'red6'=>'255,204,204',//红色6
];

$color=$color[$z_color];//显示主颜色

$fcolor=$fz_color[$z_color.$f_color];//辅助颜色

if($f_color=='10'){
	$fcolor=$fz_color['10'];
}
?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>田字格字帖生成器</title>
<style>
body,div,p,ul,li{ padding:0; margin:0; list-style:none;}
div{ width:938px; margin:0 auto;padding-left:2px; }
li{display: inline-block; width:80px; height:80px; font-family:"楷体","楷体_gb2312", "Kaiti SC", STKaiti, "AR PL UKai CN", "AR PL UKai HK", "AR PL UKai TW", "AR PL UKai TW MBE", "AR PL KaitiM GB", KaiTi, KaiTi_GB2312, DFKai-SB, "TW\-Kai"; font-size:58px; text-align:center; line-height:85px; background:url(img/<?=$bglx;?>.svg); margin:5px 0px 5px -2px; color:#b8b8b8; }
li.f{color:#000;margin-left:-0px}
li.svg{line-height:84px;}
li svg{ magin:8px; vertical-align:middle;}
.afterpage{ page-break-before:always;}
.afterpage{ page-break-before:always;}
.page-head{height: 116px;line-height: 136px; font-size: 32px;text-align: center;display: none;color: #666666}
@media print{.afterpage{ page-break-before:always;}.page-head{display: block;}}
@page {size: auto;margin: 5mm 16mm 5mm 16mm;}
</style>
</head>
<body>
<div>
<ul>
<?php



preg_match_all("/./u",$words,$hz);

for($ihz=0;$ihz<count($hz['0']);$ihz++){

	$hzGBK=iconv('UTF-8', 'GB2312' ,$hz['0'][$ihz]); 

	if(file_exists("bishun_data/".$hzGBK.".json")){
		$data=file_get_contents("bishun_data/".$hzGBK.".json");
	}else{
		$data=file_get_contents("bishun_data/".$hz['0'][$ihz].".json");
	}

	$data=json_decode($data,1);
	$count=count($data['strokes']);//统计共有多少画


	/*显示完整字符*/
	echo '<li class="svg"><svg width="54" height="54" style="margin-top: -11px;"><g transform="translate(-2.9,48) scale(0.058, -0.0572)">';
	
	foreach ($data['strokes'] as $v){
		echo '<path d="'.$v.'"style="fill:rgb('.$color.');stroke:rgb('.$color.');" stroke-width = "0"></path>';
	}

	echo "</g></svg></li>";


	//按笔数显示
	for($i=0;$i<$count;$i++){
		
		echo '<li class="svg"><svg width="54" height="54" style="margin-top: -11px;"><g transform="translate(-2.9,48) scale(0.058, -0.0572)">';
		
		for($ii=0;$ii<=$i;$ii++){
			echo '<path d="'.$data['strokes'][$ii].'"style="fill:rgb('.$fcolor.');stroke:rgb('.$fcolor.');" stroke-width = "0"></path>';
		}
		

		echo '</g></svg></li>';

	}
	
	
	/*判断是否填充12个田字格*/
	$tzg12=($count+1)/12;
	$kg=0;//空格，每行剩余未填充的空格
	if(!is_int($tzg12)){
		$kg=12- (12* $tzg12);
	}
	//为负数
	if($kg<0){
		$kg= ((ceil(abs($kg)/12)+1)*12)-($count+1);
	}
	
	/*行数不够，填充*/
	//填充完整字符
	if($kg and $bs){
		for($i=0;$i<$kg;$i++){
			/*显示完整字符*/
		 echo '<li class="svg"><svg width="54" height="54" style="margin-top: -11px;"><g transform="translate(-2.9,48) scale(0.058, -0.0572)">';
	
	     foreach ($data['strokes'] as $v){
		    echo '<path d="'.$v.'"style="fill:rgb('.$fcolor.');stroke:rgb('.$fcolor.');" stroke-width = "0"></path>';
	     }
		 echo "</g></svg></li>";

		}
	}
	//填充空行
	if($kg and !$bs){
		for($i=0;$i<$kg;$i++){
			echo '<li class="svg">&nbsp;</li>';
		}
		
	}

	/*分页显示标题头部*/
	
	$tzg_hs[]= ceil($tzg12);//占用行数
	$arraytzg=intval(array_sum($tzg_hs));
	$arraytzg=$arraytzg/15;
	if(is_int($arraytzg)){
		echo "</ul></div><div class='afterpage'><ul>";
	}

}

//堆满整页
$tzg_hs=array_sum($tzg_hs);//田字格使用行数
$tzgzys=ceil($tzg_hs/15);//田字格总页数
$zhengye=($tzgzys*15-$tzg_hs)*12;

	for($i=0;$i<$zhengye;$i++){
		echo "<li>&nbsp;</li>";
	}

?>
</ul>
</div>
<div style="display: none;">

</div>
<div id="page-head-box" style="display: none;">
<div class="page-head"><?=$title;?></div>
</div>

<script src="https://ajax.aspnetcdn.com/ajax/jquery/jquery-2.1.1.min.js"></script>
<script type="text/javascript">
    $('body').prepend($('#page-head-box').html());
    $('.afterpage').prepend($('#page-head-box').html());
    window.onload = function(){
        setTimeout(function(){window.print(); }, 1000);
    }
</script>
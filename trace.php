<?php
$contentline ="";
$time = date("d-m-Y")."\t".date("H:i:s");
$contentline .= $time;
$contentline .= "\t".$_SERVER['PHP_SELF'];
$contentline .= "\t".$_SERVER['REMOTE_ADDR'];

if (isset($_SESSION['post'])){
	$form = implode("\t", $_SESSION['post']);
	$contentline .= "\t".$form;
}
if (isset($_GET)){
	$form = implode("\t", $_GET);
	$contentline .= "\t".$form;
}




$file = "./log/tracelog.txt";
$handle = fopen($file, "a");
fwrite($handle,$contentline."\n");
fclose($handle);


?>
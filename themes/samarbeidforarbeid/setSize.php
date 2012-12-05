<?php
/**
 * @package sfa
 * @subpackage sfa-theme
 */
/*
Template Name: font size selector
*/

header('Location:'.$_SERVER['HTTP_REFERER']."");

$textSize = $_REQUEST['bodySize'];

if($textSize == "sizeLarge"){
   setcookie("bodySize", "sizeLarge", time()+3600*24*30, "/");
}
elseif($textSize == "sizeVeryLarge"){
   setcookie("bodySize", "sizeVeryLarge", time()+3600*24*30, "/");
}
else{
   setcookie("bodySize", "", time()-3600,"/");
}
exit;
?>

 

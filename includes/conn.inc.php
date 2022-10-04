<?
//$con = mysql_connect("localhost","root","DN@123%");
//mysql_select_db("discoveree");
$con = mysql_connect("localhost","root","");
mysql_select_db("vishay");


function substring_between($haystack,$start,$end) {
if (strpos($haystack,$start) === false || strpos($haystack,$end) === false) {
return false;
} else {
$start_position = strpos($haystack,$start)+strlen($start);
$end_position = strpos($haystack,$end);
return substr($haystack,$start_position,$end_position-$start_position);
}
}

function manflabel($str){
$str=strtolower($str);
if($str=="aosmd" || $str=="aos")
$str="AOS";
else if($str=="st")
$str="ST";
else if($str=="onsemi")
$str="ON Semi";
else
$str=ucfirst($str);
return $str;
}
?>

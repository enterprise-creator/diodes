<?php include "../includes/config.inc.php";?>
<?php
if(($_SESSION['user_name']!="dnp1976" && $_SESSION['user_name']!="srai") && $_SESSION['test_user_id']==""){
if($_SESSION['opage_access']==""){
$_SESSION['lasturl'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] .  $_SERVER['REQUEST_URI'];

header("Location: /");
exit;
}
}

if($_SESSION['expired_user']==1 && $_COOKIE['expired_user_compare']==1){
header("Location: https://www.discoveree.io/request_upgrade.php");
exit;
}
if($_SESSION['expired_user']==1){
$expire=time()+60*60*24;//however long you want
setcookie('expired_user_compare', 1, $expire,'/');
}

if($_REQUEST['load']=="download_xlxs"){
error_reporting(0);

$filename="compare.csv";

$str=urldecode($_REQUEST['download_text']);

header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);
$fp = fopen("php://output", "w");

$str=explode("$$",$str);
foreach($str as $std){
$tdstr=explode("^^",$std);
foreach($tdstr as $td){
$th[]=str_replace("\n","",str_replace("\r","",str_replace("\t","",trim($td))));
}
fputcsv($fp, $th);
unset($th);
}
//echo "<pre>";
//print_r($th);
fclose($fp);
exit;
}



$url=$_REQUEST['url'];
$url=explode("^",$url);

if(count($url)>11){
header("Location: compare_lists.php?url=".$_REQUEST['url']);
exit;
}


$find_replace_test_conditions=(mysql_query("SELECT find1,find2,replacewith FROM srai_others.find_replace_test_conditions WHERE 1"));
while($mswrow=mysql_fetch_array($find_replace_test_conditions)){
$test_conditions_replace[$mswrow[0]."^^".$mswrow[1]]=trim($mswrow[2]);
}

$kp2credit=0;
foreach($url as $u){
if($u!=""){
$url1=explode("@",$u);
$discoveree_cat_id.=$url1[2].",";
$part_cond.="(partno='".$url1[0]."' AND manf='".$url1[1]."' AND discoveree_cat_id='".$url1[2]."') OR ";

if($kp2[0]==""){
$kp2=mysql_fetch_array(mysql_query("SELECT CONCAT(b.manf_cat,' ',b.cat1,' ',b.cat2,' ',b.cat3) FROM  srai_others.discoveree_cat_format_live_status a INNER JOIN srai_others.discoveree_and_manf_cat b ON a.cat1id=b.cat1id WHERE a.cat2id=b.cat2id AND a.id in (".$discoveree_cat_id."00) AND (CONCAT(b.manf_cat,' ',b.cat1,' ',b.cat2,' ',b.cat3) LIKE '%IGBT%' OR CONCAT(b.manf_cat,' ',b.cat1,' ',b.cat2,' ',b.cat3) LIKE '%Diode%' OR CONCAT(b.manf_cat,' ',b.cat1,' ',b.cat2,' ',b.cat3) LIKE '%Micro%' OR CONCAT(b.manf_cat,' ',b.cat1,' ',b.cat2,' ',b.cat3) LIKE '%MCU%') GROUP BY CONCAT(b.cat1,b.cat2,b.cat3)"));
if(strpos(strtolower($kp2[0]),'igbt')!==false)
$kp2_price=$_SESSION['kp2_igbt_price'];
else if(strpos(strtolower($kp2[0]),'diode')!==false)
$kp2_price=$_SESSION['kp2_diode_price'];
else if(strpos(strtolower($kp2[0]),'mcu')!==false || strpos(strtolower($kp2[0]),'micro')!==false)
$kp2_price=$_SESSION['kp2_mcu_price'];
else if(strpos(strtolower($kp2[0]),'gate')!==false && strpos(strtolower($kp2[0]),'driver')!==false)
$kp2_price=$_SESSION['kp2_gdrive_price'];
else if(strpos(strtolower($kp2[0]),'led')!==false && strpos(strtolower($kp2[0]),'driver')!==false)
$kp2_price=$_SESSION['kp2_leddrive_price'];
else if(strpos(strtolower($kp2[0]),'thyristor')!==false && strpos(strtolower($kp2[0]),'thyristor')!==false)
$kp2_price=$_SESSION['kp2_thyristor_price'];

if(abs($kp2_price)==0)
$kp2_price=10;
}


if($_REQUEST['view']==md5($_REQUEST['url']) && $_SESSION['user_kp2']!=md5($_REQUEST['url'])){
$kp2credit=$kp2credit+$kp2_price;
$_SESSION['kp2_credit_used']=$_SESSION['kp2_credit_used']+$kp2_price;

if(abs($_SESSION['kp2_credit']-$_SESSION['kp2_credit_used'])<=0 && $_SESSION['auser_type']!="Super Admin")
 $status=mysql_query("INSERT IGNORE INTO srai_others.user_parts SET cost=".$kp2_price.",added_date='".date("Y-m-d")."',username='".$_SESSION['user_name']."',part_no='".$url1[0]."',discoveree_cat_id='".$url1[2]."'");
else if($_SESSION['auser_type']!="Super Admin" && $ins_process==0)
 $status=mysql_query("INSERT IGNORE INTO srai_others.user_parts SET cost=0.01,comment='free kp2 access credit',added_date='".date("Y-m-d")."',username='".$_SESSION['user_name']."',part_no='".$url1[0]."',discoveree_cat_id='".$url1[2]."'");

}

}
}
if($kp2credit>0)
mysql_query("UPDATE admin SET kp2_credit_used=(kp2_credit_used+".$kp2credit.") WHERE username='".$_SESSION['user_name']."'");

if($_REQUEST['view']==md5($_REQUEST['url']))
$_SESSION['user_kp2_view']=md5($_REQUEST['url']);


if($_REQUEST['action']=="advance_search"){ // advance search - start

$getmanf_array=mysql_query("SELECT * FROM srai_others.manage_manf_name");
while($bh=mysql_fetch_array($getmanf_array)){
if($bh['value_array']!="" && strpos($_REQUEST['discoveree_cat_id'],$bh['discoveree_cat_id'])!==false)
$value_array=json_decode($bh['value_array'],true);
}



if($_REQUEST['discoveree_cat_id']!=""){
$manf_search_words=(mysql_query("SELECT title,save_word,no_word FROM srai_others.manf_search_words WHERE discoveree_cat_id=".$_REQUEST['discoveree_cat_id']." GROUP BY title,save_word,no_word ORDER BY save_word,no_word"));
while($mswrow=mysql_fetch_array($manf_search_words)){
$msw[$mswrow[0]]= $msw[$mswrow[0]].trim($mswrow[1])."||".trim($mswrow[2]);
}
}

$c=0;
$sql="SELECT * FROM srai_others.advance_search_setting WHERE discoveree_cat_id=".$_REQUEST['discoveree_cat_id']." ORDER BY priority";
$results = mysql_query($sql);
while($row = mysql_fetch_assoc($results))
{
if($_REQUEST[$row['link_with']."_min"]!="" || $_REQUEST[$row['link_with']."_max"]!="" || $_REQUEST[$row['link_with']]!=""){
    foreach($row as $key => $value)
    {
    $value=trim($value);
    $search[$c][$key]=$value;
    }
$c++;
}
}

$limit="";
$cond="";
foreach($search as $k=>$v){
//echo $v['link_with']."=".$v['input_type']."==".$_REQUEST[$v['link_with']]."<br />";
  $with=trim($v['link_with']);
$lmstr="";
if($v['link_value']=="least" || $v['link_value']=="maximum"){
 $getv=explode(",",$v['fieldname']);

 for($zz=0;$zz<count($getv)-1;$zz++){

 $lmstr.="COALESCE(CAST(".$getv[$zz]." as FLOAT),";
 $s=$zz+1;

 for($z=0;$z<count($getv)-2;$z++){
 if($getv[$z+$s]!=""){
  $lmstr.="CAST(".$getv[$z+$s]." AS FLOAT),";
 }
 }


 for($zzz=0;$zzz<$zz;$zzz++){
  $lmstr.="CAST(".$getv[$zzz]." AS FLOAT),";
 }
 $lmstr=substr($lmstr,0,strlen($lmstr)-1)."),";
 }

 $lmstr=substr($lmstr,0,strlen($lmstr)-1);
 if($v['link_value']=="least")
 $lmstr="LEAST(".$lmstr.")";
 if($v['link_value']=="maximum")
 $lmstr="GREATEST(".$lmstr.")";

 }

$mp=1;

if($v['multiplyby']!=0 && $v['multiplyby']!=1)
$mp= $v['multiplyby'];

if(($v['input_type']=="textboxs" || $v['input_type']=="dropdownm")){
$str="";

unset($tmp_val_arr);
if(count($value_array[$with])>0){
foreach($value_array[$with] as $kk=>$vv){
$tmp_val_arr[$vv['org_value']].="'".addslashes($kk)."',";
}
}
$rrr=0;
foreach($_REQUEST[$with] as $vv){
 if($tmp_val_arr[$vv]!=""){
 $rrr=1;
 $str.=$tmp_val_arr[$vv];
 }else{
 $str.="'".$vv."',";
 }
}

if($v['input_type']=="textboxs")
$str=$_REQUEST[$with];
if(($str!="" && $v['link_value']=="exact") || $rrr==1){
if(str_replace("'","",substr($str,1,strlen($str)-3))!="" && str_replace(",","",$v['fieldname'])=="searchwordConfig" || str_replace(",","",$v['fieldname'])=="searchwordAuto"){
$svstr=explode(",",str_replace("'","",substr($str,1,strlen($str)-3)));
$cond.= "(";
foreach($svstr as $svv)
$cond.= str_replace("Config","",str_replace("Auto","",str_replace(",","",$v['fieldname'])))." LIKE '%".$svv."%' OR ";

$cond= substr($cond,0,strlen($cond)-4).") AND ";

}else{
$cond.= str_replace(",","",$v['fieldname'])." in ('".substr($str,1,strlen($str)-3)."') AND ";
}
}else if($str!="" && $v['input_type']!="dropdownm"){
$cond.=  $lmstr."=".($str*$mp)."  AND ";
}else if($str!="" && $v['input_type']=="dropdownm"){
$cond.=  $lmstr." in (".substr($str,0,strlen($str)-1).")  AND ";
}
}

if($v['input_type']=="dropdowns" && ($v['link_value']=="least" || $v['link_value']=="maximum")){
$str=$_REQUEST[$with][0];
if($str!="")
$cond.=  $lmstr."=".($str*$mp)."  AND ";
}

if($v['link_value']=="exact" && $v['input_type']=="textboxm" && $_REQUEST[$with."_min"]!="")
$cond.= str_replace(",","",$v['fieldname'])." >= ".(abs($_REQUEST[$with."_min"])*$mp)." AND ";

if(($v['link_value']=="least" || $v['link_value']=="maximum") && $v['input_type']=="textboxm" && $_REQUEST[$with."_min"]!="")
$cond.= $lmstr." >= ".(abs($_REQUEST[$with."_min"])*$mp)." AND ";

if($v['link_value']=="exact" && $v['input_type']=="textboxm" && $_REQUEST[$with."_max"]!="")
$cond.= str_replace(",","",$v['fieldname'])." <= ".($_REQUEST[$with."_max"]*$mp)." AND ";

if(($v['link_value']=="least" || $v['link_value']=="maximum") && $v['input_type']=="textboxm" && $_REQUEST[$with."_max"]!="")
$cond.= $lmstr." <= ".($_REQUEST[$with."_max"]*$mp)." AND ";


if($v['link_value']=="exact" && $v['input_type']=="textboxs")
$cond.= str_replace(",","",$v['fieldname'])." = '".($_REQUEST[$with]*$mp)."' AND ";

if($v['link_value']=="exact" &&  $v['input_type']=="dropdowns" && strpos($with,'searchword')!==false){
$mswstr=explode("||",$msw[str_replace("searchword","",$with)]);
if(strtolower($_REQUEST[$with])=="na"){
$cond.= "searchword = '' AND ";
}else if($mswstr[0]!="" && $mswstr[1]!="" && strtolower($_REQUEST[$with])==trim(strtolower($mswstr[0]))){
$cond.= "searchword LIKE '%".($_REQUEST[$with])."%' AND ";
if(strlen($mswstr[1])>strlen($mswstr[0]))
$cond.= "NOT searchword LIKE '%".$mswstr[1]."%' AND ";
}else if($mswstr[0]!="" && $mswstr[1]!="" && strtolower($_REQUEST[$with])==trim(strtolower($mswstr[1]))){
$cond.= "searchword LIKE '%".($_REQUEST[$with])."%' AND ";
if(strlen($mswstr[0])>strlen($mswstr[1]))
$cond.= "NOT searchword LIKE '%".$mswstr[0]."%' AND ";
}else{
$cond.= "searchword LIKE '%".($_REQUEST[$with])."%' AND ";
}
}

}
if($cond!=""){
 $part_cond=substr($cond,0,strlen($cond)-1);
//$limit="LIMIT 10";
}
$discoveree_cat_id=$_REQUEST['discoveree_cat_id'].",";
}  // advance search -- end

if($_REQUEST['action']=="cross_search"){ // cross search - start

if($_REQUEST['o']=="1" && $_SESSION['prvpost'.$_SESSION['time_rec']]!=""){
  $dd=json_decode($_SESSION['prvpost'.$_SESSION['time_rec']]);
  foreach($dd as $dk=>$dv){
  if($dk=="crossreferencepartno")
  $dv=$_REQUEST['p'];
  $_POST[$dk]=$_REQUEST[$dk]=$dv;
  }
}
if($_REQUEST['discoveree_cat_id']!=""){
$manf_search_words=(mysql_query("SELECT title,save_word,no_word FROM srai_others.manf_search_words WHERE discoveree_cat_id=".$_REQUEST['discoveree_cat_id']." GROUP BY title,save_word,no_word ORDER BY save_word,no_word"));
while($mswrow=mysql_fetch_array($manf_search_words)){
$msw[$mswrow[0]]= $msw[$mswrow[0]].trim($mswrow[1])."||".trim($mswrow[2]);
}
}

$c=0;
$sql="SELECT * FROM srai_others.cross_reference_search_setting WHERE discoveree_cat_id=".$_REQUEST['discoveree_cat_id']." ORDER BY priority";
$results = mysql_query($sql);
while($row = mysql_fetch_assoc($results))
{
//if($_REQUEST[$row['link_with']."_min"]!="" || $_REQUEST[$row['link_with']."_max"]!="" || $_REQUEST[$row['link_with']]!=""){
    foreach($row as $key => $value)
    {
    $value=trim($value);
    $search[$c][$key]=$value;
    }
$c++;
//}
}

$limit="";
$cond="";
foreach($search as $k=>$v){
//echo $v['link_with']."=".$v['input_type']."==";
$with=trim($v['link_with']);
$lmstr="";
if($v['link_value']=="least" || $v['link_value']=="maximum"){

 $getv=explode(",",$v['fieldname']);

 for($zz=0;$zz<count($getv)-1;$zz++){

 $lmstr.="COALESCE(CAST(".$getv[$zz]." AS FLOAT),";
 $s=$zz+1;

 for($z=0;$z<count($getv)-2;$z++){
 if($getv[$z+$s]!=""){
  $lmstr.="CAST(".$getv[$z+$s]." AS FLOAT),";
 }
 }


 for($zzz=0;$zzz<$zz;$zzz++){
  $lmstr.="CAST(".$getv[$zzz]." AS FLOAT),";
 }
 $lmstr=substr($lmstr,0,strlen($lmstr)-1)."),";

 }

 $lmstr=substr($lmstr,0,strlen($lmstr)-1);

 /*if(($v['link_value']=="least" || $v['link_value']=="maximum") && $_SESSION['user_name']=="dnp1976"){
 $lmstr=substr($lmstr,strpos($lmstr,'(')+1,100);
   $lmstr=explode(",",substr($lmstr,0,strpos($lmstr,')')));
   $vlmstr="";
   foreach($lmstr as $vlk=>$vlm){

   }
exit;
 }
 */
 if($v['link_value']=="least")
 $lmstr="LEAST(".$lmstr.")";
 if($v['link_value']=="maximum")
 $lmstr="GREATEST(".$lmstr.")";

 }else if($v['input_type']=="" && $v['fieldname']!=""){
  $lmstr=str_replace(",","",$v['fieldname']);
 }

$mp=1;

if($v['multiplyby']!=0 && $v['multiplyby']!=1)
$mp= $v['multiplyby'];

if(($v['input_type']=="textboxs" || $v['input_type']=="dropdownm")){
$str="";
foreach($_REQUEST[$with] as $vv)
$str.="'".$vv."',";


if($v['input_type']=="textboxs")
$str=$_REQUEST[$with];
if($str!="" && $v['link_value']=="exact")
$cond.= str_replace(",","",$v['fieldname'])." in ('".substr($str,1,strlen($str)-3)."') AND ";
else if($str!="" && $v['input_type']!="dropdownm")
$cond.=  $lmstr."=".($str*$mp)."  AND ";
else if($str!="" && $v['input_type']=="dropdownm")
$cond.=  $lmstr." in (".substr($str,0,strlen($str)-1).")  AND ";
}

if($v['input_type']=="dropdowns" && ($v['link_value']=="least" || $v['link_value']=="maximum")){
$str=$_REQUEST[$with][0];
if($str!="")
$cond.=  $lmstr."=".($str*$mp)."  AND ";
}

if($v['link_value']=="exact" && $v['input_type']=="textboxm")
$ev[$with."_min"]=abs($_REQUEST[$with."_min"]);

if($v['link_value']=="maximum" && $v['input_type']=="textboxm")
$ev[$with."_minarr"]=abs($_REQUEST[$with."_min"]);

if($v['link_value']=="minimum" && $v['input_type']=="textboxm")
$ev[$with."_minarr"]=abs($_REQUEST[$with."_min"]);

//if($v['link_value']=="exact" && $v['input_type']=="textboxm" && $_REQUEST[$with."_min"]!="")
//$cond.= str_replace(",","",$v['fieldname'])." >= ".($_REQUEST[$with."_min"]*$mp)." AND ";

//if(($v['link_value']=="least" || $v['link_value']=="maximum") && $v['input_type']=="textboxm" && $_REQUEST[$with."_min"]!="")
//$cond.= $lmstr." >= ".(abs($_REQUEST[$with."_min"])*$mp)." AND ";

if($v['link_value']=="exact" && $v['input_type']=="textboxm")
$ev[$with."_max"]=$_REQUEST[$with."_max"];

if($v['link_value']=="maximum" && $v['input_type']=="textboxm")
$ev[$with."_maxarr"]=$_REQUEST[$with."_max"];

if($v['link_value']=="minimum" && $v['input_type']=="textboxm")
$ev[$with."_maxarr"]=$_REQUEST[$with."_max"];

//if($v['link_value']=="exact" && $v['input_type']=="textboxm" && $_REQUEST[$with."_max"]!="")
//$cond.= str_replace(",","",$v['fieldname'])." <= ".($_REQUEST[$with."_max"]*$mp)." AND ";

//if(($v['link_value']=="least" || $v['link_value']=="maximum") && $v['input_type']=="textboxm" && $_REQUEST[$with."_max"]!="")
//$cond.= $lmstr." <= ".($_REQUEST[$with."_max"]*$mp)." AND ";


if($v['link_value']=="exact" && $v['input_type']=="textboxs")
$cond.= str_replace(",","",$v['fieldname'])." = '".($_REQUEST[$with]*$mp)."' AND ";

//echo $v['link_value']."=".$v['input_type']."=".$with."<br />";
if($v['link_value']=="exact" && $v['input_type']=="dropdowns" && strpos($with,'searchword')!==false){

/*$mswstr=explode("||",$msw[str_replace("searchword","",$with)]);
if(strtolower($_REQUEST[$with])=="na")
$cond.= "searchword = '' AND ";
else if($mswstr[0]!="" && $mswstr[1]!="" && strtolower($_REQUEST[$with])==strtolower($mswstr[0]))
$cond.= "searchword LIKE '%".($_REQUEST[$with])."%' AND NOT searchword LIKE '%".$mswstr[1]."%' AND ";
else if($mswstr[0]!="" && $mswstr[1]!="" && strtolower($_REQUEST[$with])==strtolower($mswstr[1]))
$cond.= "searchword LIKE '%".($_REQUEST[$with])."%' AND NOT searchword LIKE '%".$mswstr[0]."%' AND ";
else
$cond.= "searchword LIKE '%".($_REQUEST[$with])."%' AND ";
*/
$mswstr=explode("||",$msw[str_replace("searchword","",$with)]);
if(strtolower($_REQUEST[$with])=="na"){
$cond.= "searchword = '' AND ";
}else if($mswstr[0]!="" && $mswstr[1]!="" && strtolower($_REQUEST[$with])==strtolower($mswstr[0])){
$cond.= "searchword LIKE '%".($_REQUEST[$with])."%' AND ";
if(strlen($mswstr[1])>strlen($mswstr[0]))
$cond.= "NOT searchword LIKE '%".$mswstr[1]."%' AND ";
}else if($mswstr[0]!="" && $mswstr[1]!="" && strtolower($_REQUEST[$with])==strtolower($mswstr[1])){
$cond.= "searchword LIKE '%".($_REQUEST[$with])."%' AND ";
if(strlen($mswstr[0])>strlen($mswstr[1]))
$cond.= "NOT searchword LIKE '%".$mswstr[0]."%' AND ";
}else{
$cond.= "searchword LIKE '%".($_REQUEST[$with])."%' AND ";
}

}

if($lmstr!=""){
$f1++;
$selectcond.= $lmstr." as f".$f1.",";
$cross_array['f'.$f1]=$lmstr;
//$cross_array_perc['f'.$f1]=$v['search_perc'];
}
}






$discoveree_cat_id=$_REQUEST['discoveree_cat_id'].",";
foreach($msw as $k=>$v)
$selectcond=str_replace($k,"",$selectcond);

$fcnt=0;
foreach($ev as $k=>$v){
//if(strpos($k,"_min")!==false){
$f=str_replace("_min","",str_replace("_max","",str_replace("_minarr","",str_replace("_maxarr","",$k))));
foreach($search as $s){
if($s['link_with']==$f){
if(strpos($s['fieldname'],",")!==false && (strpos($k,"_minarr")!==false || strpos($k,"_maxarr")!==false)){
if(strpos($k,"_minarr")!==false)
$fcnt++;
$evf[$k]="f".$fcnt;
}else{
$evf[$k]=str_replace(",","",$s['fieldname']);
}
$selectcond.=$s['fieldname'];
break;
}}
//}
}
if($_REQUEST['discoveree_package_cat_mounting']!="")
$cond.="trim(discoveree_package_cat1) LIKE '".trim($_REQUEST['discoveree_package_cat_mounting'])."%' AND ";

    if($_SESSION['user_name']=="dnp19761"){
echo "<pre>";
print_r($ev);
print_r($evf);
print_r($cross_array);
echo "</pre>";
}


   $crosssql="SELECT ".$selectcond."partno FROM srai_others.discoveree_live_".$_REQUEST['discoveree_cat_id']."_v2 WHERE partno='".$_REQUEST['crossreferencepartno']."' LIMIT 1";

 if($_SESSION['user_name']=="dnp19761"){
 echo $crosssql;
 echo "<hr />";
}
$crosspart=mysql_query($crosssql);

if(mysql_num_rows($crosspart)<=0){
$crosssql="SELECT ".$selectcond."partno FROM srai_others.discoveree_live_".$_REQUEST['discoveree_cat_id']."_v2 WHERE partno LIKE '".$_REQUEST['crossreferencepartno']."%'";
$crosspart=mysql_query($crosssql);
$firstp=0;
while($b=mysql_fetch_array($crosspart)){
if(!in_array($b['partno'],$avlp))
$avlp[]=$b['partno'];
$firstp++;
if($firstp==1)
$_REQUEST['crossreferencepartno']=$b['partno'];
}

$crosssql="SELECT ".$selectcond."partno FROM srai_others.discoveree_live_".$_REQUEST['discoveree_cat_id']."_v2 WHERE partno LIKE '".$_REQUEST['crossreferencepartno']."' LIMIT 1";
$condition_apply=2;
$crosspart=mysql_query($crosssql);
}

if(mysql_num_rows($crosspart)<=0){

$pp=$p1=$_REQUEST['crossreferencepartno'];
$totc=strlen($pp)/2;
for($i=0;$i<=$totc-1;$i++){
$pp=substr($pp,0,strlen($pp)-1);
if($pp!="")
$partsl[$pp]=$p1;
}

foreach($partsl as $k=>$v){
if($k!="")
$condl.="'".$k."',";
}
if($cond!="")
$condl=substr($condl,0,strlen($condl)-1);

$crosssql="SELECT ".$selectcond."partno FROM srai_others.discoveree_live_".$_REQUEST['discoveree_cat_id']."_v2 WHERE partno in (".$condl.") LIMIT 1";
$crosspart=mysql_query($crosssql);
while($b=mysql_fetch_array($crosspart)){
//if(!in_array($b['partno'],$avlp))
//$avlp[]=$b['partno'];
$_REQUEST['crossreferencepartno']=$b['partno'];
}
$crosssql="SELECT ".$selectcond."partno FROM srai_others.discoveree_live_".$_REQUEST['discoveree_cat_id']."_v2 WHERE partno LIKE '".$_REQUEST['crossreferencepartno']."' LIMIT 1";

$condition_apply=3;

$crosspart=mysql_query($crosssql);
}



while($row = mysql_fetch_assoc($crosspart)){
if($condition_apply==2)
  $_REQUEST['crossreferencepartno']=$row['partno'];

foreach($ev as $k=>$v){

$fld=str_replace("_min","",str_replace("_max","",$evf[$k]));
if($fld!="f1" && $fld!="f2" && $fld!="f3" && $fld!="f4" && $fld!="f5" && $fld!="f6" && $fld!="f7"){
$value=$row[$fld];
$pervaluen=$value-($value*$v/100);
$pervaluep=$value+($value*$v/100);
if(strpos($k,"_min")!==false && abs($pervaluen)>0)
$cond.=$fld.">=".$pervaluen." AND ";
if(strpos($k,"_max")!==false && abs($pervaluen)>0)
$cond.=$fld."<=".$pervaluep." AND ";
}
}

foreach($cross_array as $k=>$v){
foreach($evf as $kk=>$vv){
if($k==$vv){

$value=$row[$k];
$pervaluen=$value-($value*$ev[$kk]/100);
$pervaluep=$value+($value*$ev[$kk]/100);
if(strpos($kk,"_min")!==false && abs($pervaluen)>0)
$cond.=$v.">=".$pervaluen." AND ";
if(strpos($kk,"_max")!==false && abs($pervaluen)>0)
$cond.=$v."<=".$pervaluep." AND ";

}
}
}

foreach($row as $key => $value)
{
$checkkey=$cross_array[$key];

if($key!="partno" && abs($cross_array_perc[$key])>0 && $value!="" && $cross_array[$key]!=""){
//$pervalueneg=$value-($value*$cross_array_perc[$key]/100);
//$pervaluepos=$value+($value*$cross_array_perc[$key]/100);
//$cond.=$cross_array[$key]."<=".$pervalueneg." AND ".$cross_array[$key].">=".$pervaluepos." AND ";
//}else if(strpos($checkkey,'searchword')===false && $key!="partno" && abs($cross_array_perc[$key])==0 && $value!="" && $cross_array[$key]!=""){
//$cond.=$cross_array[$key]."='".$value."' AND ";
}else if(strpos($checkkey,'searchword')!==false && $value!=""){

$mswstr=explode("||",$msw[str_replace("searchword","",$checkkey)]);
if(strtolower($_REQUEST[$with])=="na")
$cond.= "searchword = '' AND ";
else if($mswstr[0]!="" && $mswstr[1]!="" && strpos(strtolower($value),strtolower($mswstr[0]))!==false && strpos(strtolower($value),strtolower($mswstr[1]))===false )
$cond.= "searchword LIKE '%".($mswstr[0])."%' AND NOT searchword LIKE '%".$mswstr[1]."%' AND ";
else if($mswstr[0]!="" && $mswstr[1]!="" && strpos(strtolower($value),strtolower($mswstr[1]))!==false && strpos(strtolower($value),strtolower($mswstr[0]))===false)
$cond.= "searchword LIKE '%".($mswstr[1])."%' AND NOT searchword LIKE '%".$mswstr[0]."%' AND ";
else if($mswstr[2]!="" && strpos(strtolower($value),strtolower($mswstr[2]))!==false)
$cond.= "searchword LIKE '%".($mswstr[2])."%' AND ";
else if($mswstr[3]!="" && strpos(strtolower($value),strtolower($mswstr[3]))!==false)
$cond.= "searchword LIKE '%".($mswstr[2])."%' AND ";
else if($mswstr[4]!="" && strpos(strtolower($value),strtolower($mswstr[4]))!==false)
$cond.= "searchword LIKE '%".($mswstr[2])."%' AND ";
else
$cond.= "searchword LIKE '%".($mswstr[0])."%' AND ";
}

}
}

if($cond!=""){
  $part_cond=substr($cond,0,strlen($cond)-1);

//$limit="LIMIT 10";
}
}  // cross search -- end

if(count($rr_manf)<=0){
$getmanf_array=mysql_query("SELECT * FROM srai_others.manage_manf_name");
while($bh=mysql_fetch_array($getmanf_array)){
if($bh['manf_array']!="")
$manf_array=json_decode($bh['manf_array'],true);
if($bh['value_array']!="" && strpos($discoveree_cat_id,$bh['discoveree_cat_id'])!==false)
$value_array=json_decode($bh['value_array'],true);
}

foreach($manf_array as $k=>$v)
$rr_manf[strtolower($v['source_manf'])]=$v['org_manf'];
}

$sql="SELECT * FROM srai_others.product_page_setting WHERE discoveree_cat_id in (".$discoveree_cat_id."0) ORDER BY priority";
$product_setting=(mysql_query($sql));
$cnt=0;
while($row = mysql_fetch_assoc($product_setting)){
foreach($row as $key => $value)
{
    if($key!="id" && $key!="discoveree_cat_id"){
     $product_page_setting[$row['discoveree_cat_id']][$cnt][$key]=$value;
     $product_page_setting_convert_text[strtolower(substr($row['fieldname'],0,strlen($row['fieldname'])-1))]=$row['convert_text'];
     }
}
$cnt++;
}
//  echo "<pre>";
//  print_r($product_page_setting);
//exit;




$cnt=0;
foreach($product_page_setting as $k=>$v){

if(!in_array($k,$dls)){
$nc=mysql_fetch_array(mysql_query("SELECT json FROM srai_others.dynamic_label_setup WHERE discoveree_cat_id=".$k));
$uin=json_decode($nc[0]);
$dls[]=$k;
}

if($k!="" && count($msw[$k])<=0){
$manf_search_words=(mysql_query("SELECT title,save_word,no_word FROM srai_others.manf_search_words WHERE discoveree_cat_id=".$k." GROUP BY title,save_word,no_word ORDER BY save_word,no_word"));
while($mswrow=mysql_fetch_array($manf_search_words)){
$msw[$k][strtolower($mswrow[0])].=$mswrow[1]."||".($mswrow[2]!=''?$mswrow[2].'||':'');
}
}

$dresults=mysql_query("select  * FROM srai_others.part_split_field_value WHERE discoveree_cat_id= '".$k."'    ORDER BY id ");
while($drow=mysql_fetch_array($dresults)){
$rray[$drow['discoveree_cat_id']][$drow['symbol_field']][$drow['display']]=array("inc"=>$drow['search_include'],"exc"=>$drow['search_exclude']);
}

  $live_sql="SELECT * FROM srai_others.discoveree_live_".$k."_v2 WHERE ".substr($part_cond,0,strlen($part_cond)-3).$limit;
if($_SESSION['user_name']=="dnp19761"){
echo $live_sql;
exit;
}

$result=mysql_query($live_sql);

if(($_REQUEST['action']=="advance_search" || $_REQUEST['action']=="cross_search") && mysql_num_rows($result)>0){
$part_cond=str_replace("&","@and@",$part_cond);
$part_cond=str_replace("/","@slash@",$part_cond);
$part_cond=str_replace("\\","@bslash@",$part_cond);
$part_cond=str_replace("+","@plus@",$part_cond);
$sp="";
if($_REQUEST['action']=="cross_search")
$sp="&search_part=".$_REQUEST['crossreferencepartno'];

//$_SESSION['time_rec']=$timerec="";
//$_SESSION['rec'.$timerec]="";
//if(strlen($part_cond)<200){
//header("Location: compare_lists.php?type=list".$sp."&discoveree_cat_id=".$discoveree_cat_id."&ca=".$condition_apply."&avlp=".json_encode($avlp)."&rec=".$part_cond);
//}else{
$timerec=time();
if($_REQUEST['o']==1)
{
$timerec=$_SESSION['time_rec'];
$_SESSION['rec'.$timerec]=$part_cond;
}else{
$_SESSION['time_rec']=$timerec;
$_SESSION['rec'.$timerec]=$part_cond;
$_SESSION['ca'.$timerec]=$condition_apply;
$_SESSION['avlp'.$timerec]=json_encode($avlp);
$_SESSION['prvpost'.$timerec]=json_encode(utf8ize($_POST));
}
if($_SESSION['user_name']=="dnp19761"){
echo "<pre>";
print_r($_SESSION);
exit;
}
header("Location: compare_lists.php?type=list".$sp."&discoveree_cat_id=".$discoveree_cat_id."&rec=");
//}

exit;
}
while($row = mysql_fetch_assoc($result))
{



 foreach($row as $key => $value)
{
     $value=trim($value);
     if($key!="searchword")
     $data[$cnt][strtolower($key)]= ($value);

        if($key=="searchword"){
      foreach($msw[$k] as $mswk=>$mswv){
      $valuemsw=",".strtolower($value).",";
      $mswstr=explode("||",strtolower($mswv));
      if(strpos($valuemsw,",".trim($mswstr[0]).",")!==false && strpos($valuemsw,",".trim($mswstr[1]).",")===false)
      $data[$cnt][$key.$mswk]=str_replace(",","",str_replace("(","",str_replace(")","",str_replace("[","",str_replace("]","",trim(manflabel($mswstr[0])))))));
      else if(strpos($valuemsw,",".trim($mswstr[0]).",")===false && strpos($valuemsw,",".trim($mswstr[1]).",")!==false)
      $data[$cnt][$key.$mswk]=str_replace(",","",str_replace("(","",str_replace(")","",str_replace("[","",str_replace("]","",trim(manflabel($mswstr[1])))))));
      else if(strpos($valuemsw,",".trim($mswstr[2]).",")!==false)
      $data[$cnt][$key.$mswk]=str_replace(",","",str_replace("(","",str_replace(")","",str_replace("[","",str_replace("]","",trim(manflabel($mswstr[2])))))));
      else if(strpos($valuemsw,",".trim($mswstr[3]).",")!==false)
      $data[$cnt][$key.$mswk]=str_replace(",","",str_replace("(","",str_replace(")","",str_replace("[","",str_replace("]","",trim(manflabel($mswstr[3])))))));
      else if(strpos($valuemsw,",".trim($mswstr[4]).",")!==false)
      $data[$cnt][$key.$mswk]=str_replace(",","",str_replace("(","",str_replace(")","",str_replace("[","",str_replace("]","",trim(manflabel($mswstr[4])))))));
      else
      $data[$cnt][$key.$mswk]="";
      }
     }
}
$cnt++;
}
}
// echo "<pre>";
// print_r($product_page_setting);
//   print_r($multiplyby);
//   exit;
$trrc=1;
reset($product_page_setting);

if($_SESSION['user_name']=="dnp197s6"){
  echo "<pre>";
   print_r($msw);
    print_r($data);
   }
?>
<? include("../header.php");?>
	<link href="../dist/css/jquery.multiselect.css" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
     <style>
	canvas {
		-moz-user-select: none;
		-webkit-user-select: none;
		-ms-user-select: none;
	}
	input{border: 1px solid #aaa;color:#aaa;padding:4px;}
 .fa-minus {
    content: inherit!important;
}
.fa-plus{content:inherit!important;}
	</style>
    <style>
    .ms-options-wrap{width: 100%;}
 .ms-options-wrap > .ms-options {
      left: inherit;
      text-align:left;
    }
    tr:nth-child(even) {background: #e4e1e1;}
tr:nth-child(odd) {background: #FFF;}

tr.trrc1,tr.trrc3,tr.trrc5,tr.trrc7,tr.trrc9,tr.trrc11,tr.trrc13,tr.trrc15,tr.trrc17,tr.trrc19,tr.trrc21,tr.trrc23,tr.trrc25,tr.trrc27,tr.trrc29,tr.trrc31,tr.trrc33,tr.trrc35,tr.trrc37,tr.trrc39,tr.trrc41,tr.trrc43,tr.trrc45,tr.trrc47,tr.trrc49,tr.trrc51,tr.trrc53{background: #e4e1e1;}
tr.trrc2,tr.trrc4,tr.trrc6,tr.trrc8,tr.trrc10,tr.trrc12,tr.trrc14,tr.trrc16,tr.trrc18,tr.trrc20,tr.trrc22,tr.trrc24,tr.trrc26,tr.trrc28,tr.trrc30,tr.trrc32,tr.trrc34,tr.trrc36,tr.trrc38,tr.trrc40,tr.trrc42,tr.trrc44,tr.trrc46,tr.trrc48,tr.trrc50,tr.trrc52{background: #ffffff;}
td{padding:4px;}
table#maintable, th, td {
  border: 1px solid #D1D1D1;
}

th {
    position: sticky;
    top: 0;
}
thead {
    position: sticky;
    top: 0;
}
	</style>
		<link rel="stylesheet" type="text/css" href="../admin/graph/css1/jquery.dataTables.css">
		<link rel="stylesheet" type="text/css" href="../admin/graph/css1/responsive.dataTables.min.css">


<section id="services">
<div class="container text-center" dstyle="width: 100%;">

<div class="row">
<?if($cnt<=0){?>
<br />
<div class="alert text-left alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                <h4><i class="icon fa fa-ban"></i> <b>Not Found!</b></h4>
                Part not found..
              </div>
<?}?>
<?if(strpos($_SESSION['page_access'],'discoversssssssssssss')!==false){?>
<br />
<div class="alert text-left alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                <h4><i class="icon fa fa-ban"></i> <b>Alert!</b></h4>
                You do not have permission to access this page.. Please consult to Administrator at info@discoveree.io
              </div>
<?}else if($cnt>0){ $calshow=1;?>
<center><h1 style="margin:0px;padding:0px;">Comparison</h1></center>
<div style="width:100%;">
 <table border="0" cellspacing="0" width="100%" style="width:100%;" id="maintable" class="display">
                        <thead>
				        	                             <tr style="background-color:#cbd9e0;color:#1f1e1e;">
	            				<th style="height:30px;width:250px;"><b>Parameter</b></th>
	            			 	<th style="width:100px;"><b>Value Type</b></th>
	            			 	<?foreach($data as $k1=>$v1){?>
                                 	            				<th style="text-align:center;"><b><?=checksplit('manf','',$v1['manf'],'');?></b></th>
                               <?}?>
	        				</tr>
                        		<tr <? echo "class='trrc".$trrc."'";$trrc++;?>>
                            <th style="text-align:left;width:350px;">Part No</th>
                             <th> </th>
                             <?foreach($data as $k=>$v){?>
                             <? if(file_exists("../discoveree_cat_resource/".strtolower($v['manf'])."/".$v['discoveree_cat_id']."/datasheet-pdf/".strtolower($v['partno']).".pdf")){?>
         				     <th style="text-align:center;"><a href="../discoveree_cat_resource/<?=strtolower($v['manf']);?>/<?=$v['discoveree_cat_id'];?>/datasheet-pdf/<?=strtolower($v['partno']);?>.pdf" target="_blank"><?=strtoupper($v['partno']);?></a><br></th>
                             <? } else if($v['datasheet']!="" && strpos($v['datasheet'],'digi')===false){?>
         				     <th style="text-align:center;"><a href="<?=$v['datasheet'];?>" target="_blank"><?=strtoupper($v['partno']);?></a><br></th>
                             <? } else if($v['datasheet_url']!="" && strpos($v['datasheet'],'digi')===false){?>
         				     <th style="text-align:center;"><a href="<?=$v['datasheet_url'];?>" target="_blank"><?=strtoupper($v['partno']);?></a><br></th>
                             <? } else if($v['datasheeturl']!="" && strpos($v['datasheet'],'digi')===false){?>
         				     <th style="text-align:center;"><a href="<?=$v['datasheeturl'];?>" target="_blank"><?=strtoupper($v['partno']);?></a><br></th>
                             <?}else{?>
         				     <th style="text-align:center;"><?=strtoupper($v['partno']);?><br></th>
                             <?}?>
                             <?}?>

                            </tr>
                            </thead>
                            <tbody>

                            	<tr <? echo "class='trrc".$trrc."'";$trrc++;?>>
                            <td style="text-align:left;width:350px;">Category</th>
                             <td> </td>
                             <?foreach($data as $k=>$v){
                             if($catname_lists[$v['discoveree_cat_id']]==""){
                             $getcatid=mysql_fetch_array(mysql_query("SELECT * FROM srai_others.discoveree_cat_format_live_status WHERE id=".$v['discoveree_cat_id']));
                             $catname=get_title($getcatid['cat1id'],$getcatid['cat2id'],$getcatid['cat3id']);
                             $catname_lists[$v['discoveree_cat_id']]=$catname;
                             }
                             ?>
         				     <td style="text-align:center;"><? $n=$catname_lists[$v['discoveree_cat_id']];$n=substr($n,strpos($n,'-')+1,strlen($n));echo checksplit('','',$n);?></td>
                             <?}?>

                            </tr>

                            <?foreach($product_page_setting[key($product_page_setting)] as $d=>$r){
                            $r['label']=trim($r['label']);
                            // if(strpos(strtolower($r['value_type']),'condition')===false || $_SESSION['user_name']=="" || $_SESSION['user_name']=="srai" || strpos(strtolower($_SESSION['user_name']),"ritesh")!==false){
                            ob_start();
                            ?>

                              <tr <? echo "class='trrc".$trrc."'";$trrc++;?>>


                            <td style="text-align:left;"><?=$r['label'];?><span style="display:none;"><?=$r['kp2'];?></span></td>
                            <td><?   $vtt=trim($r['value_type']);if($r['link_with']!="searchword"){echo $vtt;} $vtt=explode(",",$vtt);?></td>
                             <?foreach($data as $k=>$v){?>
                             <td>
                             <?
                             if($r['link_value']=="exact"){    // For Exact
                             $l=explode(",",strtolower($r['fieldname']));
                             $str="";
                             $swstr="";
                             for($i=0;$i<count($l)-1;$i++){
                              //echo $l[$i]."-".count($vtt)."=".$v[$l[$i]]."==".$vtt[0]."<br />";
                             if($l[$i]=="searchword" && $r['value_type']!="" && $v[$l[$i]]!=""){
                             $swlive=",".strtolower($v[$l[$i]]).",";
                             foreach($vtt as $vtt1){
                             if(strpos($swlive,",".strtolower($vtt1).",")!==false)
                             $swstr.=$vtt1.",";
                             }
//                             if($swstr!="")
                             $v[$l[$i]]=substr($swstr,0,strlen($swstr)-1);
                             }
                             if($r['multiplyby']>1)
                             $str.=($v[$l[$i]]==''?'-':str_replace(",","",number_format(($v[$l[$i]]*$r['multiplyby']),$r['decimal_place'])));
                             else if(is_numeric($v[$l[$i]]))
                             $str.=($v[$l[$i]]==''?'-':str_replace(",","",number_format($v[$l[$i]],$r['decimal_place'])));
                             else if(!is_numeric($v[$l[$i]]))
                             $str.=($v[$l[$i]]==''?'-':$v[$l[$i]]);
                             $vss=explode(",",$str);
                             if(count($vtt)-1>=count($vss))
                             $str.=",&nbsp;&nbsp;";
                             }
                             echo checksplit($l[0],$v['discoveree_cat_id'],gettc($str,$r['tc']),$r['value_type']);//substr($str,0,strlen($str)-1);
                             if(strpos(strtolower($r['value_type']),'max')!==false && $graph[$r['label']][$v['partno']]['max']=="")
                             $graph[$r['label']][$v['partno']]['max']=$str;
                             else if(strpos(strtolower($r['value_type']),'typ')!==false && $graph[$r['label']][$v['partno']]['typ']=="")
                             $graph[$r['label']][$v['partno']]['typ']=$str;
                             }
                             ?>
                             <?
                             unset($xv);
                             if($r['link_value']=="least"){    // For Least
                             $l=explode(",",strtolower($r['fieldname']));
                             $str="";
                             for($i=0;$i<count($l)-1;$i++){
                             if(abs($v[$l[$i]])!=0)
                             $xv[]=$v[$l[$i]];
                             }
                              if($r['multiplyby']>1){
                             echo getv(str_replace(",","",number_format(min($xv)*$r['multiplyby'],$r['decimal_place'])));

                             if(strpos(strtolower($r['value_type']),'max')!==false && $graph[$r['label']][$v['partno']]['max']=="")
                             $graph[$r['label']][$v['partno']]['max']=getv(str_replace(",","",number_format(min($xv)*$r['multiplyby'],$r['decimal_place'])));
                             else if(strpos(strtolower($r['value_type']),'typ')!==false && $graph[$r['label']][$v['partno']]['typ']=="")
                             $graph[$r['label']][$v['partno']]['typ']=getv(str_replace(",","",number_format(min($xv)*$r['multiplyby'],$r['decimal_place'])));

                             }else{
                             echo getv(str_replace(",","",number_format(min($xv),$r['decimal_place'])));
                             if(strpos(strtolower($r['value_type']),'max')!==false && $graph[$r['label']][$v['partno']]['max']=="")
                             $graph[$r['label']][$v['partno']]['max']=getv(str_replace(",","",number_format(min($xv),$r['decimal_place'])));
                             else if(strpos(strtolower($r['value_type']),'typ')!==false && $graph[$r['label']][$v['partno']]['typ']=="")
                             $graph[$r['label']][$v['partno']]['typ']=getv(str_replace(",","",number_format(min($xv),$r['decimal_place'])));
                             }
                             }
                             ?>
                             <?
                             unset($xv);
                             if($r['link_value']=="maximum"){    // For Least
                             $l=explode(",",strtolower($r['fieldname']));
                             $str="";
                             for($i=0;$i<count($l)-1;$i++){
                             $xv[]=$v[$l[$i]];
                             }
                             if($r['multiplyby']>1){
                             echo getv(str_replace(",","",number_format(max($xv)*$r['multiplyby'],$r['decimal_place'])));
                             if(strpos(strtolower($r['value_type']),'max')!==false && $graph[$r['label']][$v['partno']]['max']=="")
                             $graph[$r['label']][$v['partno']]['max']=getv(str_replace(",","",number_format(max($xv)*$r['multiplyby'],$r['decimal_place'])));
                             else if(strpos(strtolower($r['value_type']),'typ')!==false && $graph[$r['label']][$v['partno']]['typ']=="")
                             $graph[$r['label']][$v['partno']]['typ']=getv(str_replace(",","",number_format(max($xv)*$r['multiplyby'],$r['decimal_place'])));
                             }else{
                             echo getv(str_replace(",","",number_format(max($xv),$r['decimal_place'])));
                             if(strpos(strtolower($r['value_type']),'max')!==false && $graph[$r['label']][$v['partno']]['max']=="")
                             $graph[$r['label']][$v['partno']]['max']=getv(str_replace(",","",number_format(max($xv),$r['decimal_place'])));
                             else if(strpos(strtolower($r['value_type']),'typ')!==false && $graph[$r['label']][$v['partno']]['typ']=="")
                             $graph[$r['label']][$v['partno']]['typ']=getv(str_replace(",","",number_format(max($xv),$r['decimal_place'])));
                             }
                             }
                             if(strpos($r['fieldname'],"first_crawl_date")!==false){
                             $lastupdate=mysql_fetch_array(mysql_query("SELECT crawl_date FROM srai_others.part_number_and_product_page_others WHERE part_no='".$v['partno']."' AND  manf='".$v['manf']."' ORDER BY crawl_date LIMIT 1"));
                             echo $lastupdate[0];
                             }
                             ?>
                             </td>
                             <?}?>
	            			 </tr>
                            <?
                              $string = ob_get_contents();
ob_end_clean();
//echo $string;
$string=explode("</tr>",$string);
foreach($string as $str){

$strtd=explode("</td>",$str);
$v=0;
for($i=2;$i<=count($strtd);$i++){
$vtd=$vtds=strip_tags($strtd[$i]);
$vtd=(int) filter_var($vtd, FILTER_SANITIZE_NUMBER_INT);

$vtds=trim(str_replace(" ","",str_replace("&nbsp;"," ",$vtds)));

if(abs($vtd)>0)
$v=1;
else if(trim($vtds)!="-" && trim($vtds)!="" && trim($vtds)!="-,-,-" && trim($vtds)!="-,-")
$v=1;
}
if($v==1 && (strpos($str,'<span style="display:none;">0</span>')!==false || $_REQUEST['view']==md5($_REQUEST['url']) || $_SESSION['user_kp2_view']==md5($_REQUEST['url']))){
echo $str."</tr>";
}else if($v==1){
$one++;
?>
 <?

 if($_SESSION['showkp']==1 && $one==1 && $_REQUEST['view']!=md5($_REQUEST['url']) && $_SESSION['user_kp2_view']!=md5($_REQUEST['url'])){ $calshow=0;?>
                            <tr class="viewmore" style="background-color:#CBD9E0;"><td colspan="15" align="center"><center>
Your subscription comes with KP1 access which includes basic part parameters shown above.
<br /><br />
<a <?if(abs($_SESSION['kp2_credit']-$_SESSION['kp2_credit_used'])<0){?>onclick="if(!confirm('Please confirm that you approve $<?=$kp2_price*count($data);?> for this access?')){return false;}"<?}?> href="compare.php?url=<?=$_REQUEST['url'];?>&view=<?=md5($_REQUEST['url']);?>" style="cursor:pointer;text-align:center;font-size:14px;font-weight:bold;">Click here</a> to view full part details (KP1 + KP2)
<br /><br />
KP2 access includes access to detailed part parameters such as thermal, dynamic, switching characteristics and comparison charts.
<br />
KP2 access for your subscription is charged at $<?=$kp2_price;?> per part.

                            </center></td></tr>
                           <?}?>
<?
$tdd=explode("</td>",$str);
$tdcnt=0;
foreach($tdd as $tdk){
$tdcnt++;
if($_SESSION['showkp']==1 && $tdcnt>2 && strpos($tdk,"<td>")!==false && strpos($tdk,"</tr>")===false){
echo "<td>xxxx</td>";
}else if($_SESSION['showkp']==1 && $tdcnt>2 && strpos($tdk,"<td>")!==false && strpos($tdk,"</tr>")!==false){
echo "<td>xxxx</td></tr>";
}else{
echo $tdk;
}
}
}
}
                            }?>
<?if($one==1 && $_REQUEST['view']!=md5($_REQUEST['url']) && $_SESSION['user_kp2_view']!=md5($_REQUEST['url'])){}else{?>
<?

foreach($data as $v){
$sq=mysql_query("SELECT a.graph_title,b.* FROM srai_others.part_graph_listing a INNER JOIN srai_others.part_graph_listing_details b ON a.partno=b.partno WHERE a.graph_id=b.graph_id AND a.partno='".$v['partno']."' ORDER BY b.id");
while($rr=mysql_fetch_array($sq)){
//$rr['graph_title']=str_replace(" ","",$rr['graph_title']);
//$rr['graph_title']=str_replace(".","",$rr['graph_title']);
//$rr['graph_title']=str_replace("(","",$rr['graph_title']);
//$rr['graph_title']=strtolower(str_replace(")","",$rr['graph_title']));

if(strpos($rr['xy'],'"')===false){
$rr['xy']=str_replace('{','{"',$rr['xy']);
$rr['xy']=str_replace(':','":"',$rr['xy']);
$rr['xy']=str_replace(',y','","y',$rr['xy']);
$rr['xy']=str_replace('}','"}',$rr['xy']);
}
if(strpos($rr['xy'],'[')===false)
$rr['xy']="[".$rr['xy']."]";

$graph_capture[$v['partno']][$rr['graph_title']][]=array('graph_id'=>$rr['graph_id'],'curve_title'=>$rr['curve_title'],'tctj'=>$rr['tctj'],'xy'=>$rr['xy'],'xscale'=>json_decode($rr['xscale']),'yscale'=>$rr['yscale'],'xunit'=>$rr['xunit'],'yunit'=>$rr['yunit']);
}
}
if($_SESSION['user_name']=="dnp19761"){
//echo "<pre>";
//print_r($graph_capture);
//exit;
}

 foreach($uin as $k=>$v){
 foreach($v as $kk=>$vv){
 unset($j);
 foreach($vv->json as $uk2=>$uv2){
 $j[]=array("scientific" => $uv2->scientific,"decimal" => $uv2->decimal,"parameter" => $uv2->parameter,"value_type" => $uv2->value_type,"formulla" => $uv2->formulla,"show" => $uv2->show,"order_by" => $uv2->order_by);
 }
 $rr[abs($vv->header_order)][]=array("header_title" => $k,"user_input" => $vv->user_input,"details" => $j);
 }
 }
 ksort($rr);
 foreach($rr as $k=>$v){
 foreach($v as $kk=>$vv){
 $keys = array_column($rr[$k][$kk]['details'], 'order_by');
 array_multisort($keys, SORT_ASC, $rr[$k][$kk]['details']);
 }
 }
 ?>
<?
foreach($rr as $k=>$v){foreach($v as $kk=>$vv){?>
<tr style="background-color:#cbd9e0;color:#1f1e1e;">
<td colspan="10" style="text-align:left;">
<div id="<? echo $ht=str_replace(" ","",str_replace("/","",str_replace("&","",strtolower($vv['header_title']))));?>"></div>
<b><?=$vv['header_title'];?></b></td></tr>
<?if($vv['user_input']!=""){?>
<tr style="background-color:#fff;color:#000;">
<td colspan="1" style="text-align:left;">Enter Circuit Operating Conditions</td>
<td colspan="10" style="text-align:center;"><form method="GET" action="compare.php">
<input type="hidden" name="move" value="<?=$ht;?>" /><input type="hidden" name="url" value="<?=$_REQUEST['url'];?>" /><input type="hidden" name="view" value="<?=$_REQUEST['view'];?>" />
<?$u=explode(",",$vv['user_input']);
foreach($u as $uk=>$uv){
if($uv!=""){
if(strpos($uv,"<br>")!==false){
$uv=str_replace("<br>","",$uv);
echo "<br />";
}
$name=explode("=",$uv);
$name1[0]=$name[0];

if(strpos($name[0],'[')!==false)
$name[0]=trim(substr($name[0],0,strpos($name[0],'[')));
$n=str_replace(" ","",str_replace("[","",str_replace("]","",str_replace("(","",str_replace(")","",$name[0])))));
if($_REQUEST[$n]=="")
$_REQUEST[$n]=$name[1];
?>
<?=$name1[0];?> =<input type="text" name="<?=$n;?>" value="<?=$_REQUEST[$n];?>" style="width:50px;padding:0;margin:5px;" />
<?}}?>
 Show Calculation <select name="show_calculation" style="padding:2px;margin:5px;" onchange="location.href='compare.php?url=<?=$_REQUEST['url'];?>&view=<?=$_REQUEST['view'];?>&show_calculation='+this.value+'&move=<?=$ht;?>';"><option value="" <?if($_REQUEST['show_calculation']==""){echo "SELECTED";};?>>No</option><option <?if($_REQUEST['show_calculation']=="yes"){echo "SELECTED";};?> value="yes">Yes</option></select> <input type="submit" name="form" class="btn btn-success" value="Update" style="padding:4px;margin:5px;" />
</form></td>
</tr>
<?}?>
<?
foreach($vv['details'] as $dk=>$dv){
unset($matches);
preg_match_all("/\[[^\]]*\]/", $dv['formulla'], $matches);

?>
<tr <?if($dv['show']!=1){echo "style='display:none;'";}else{ echo "class='trrc".$trrc."'";$trrc++;}?>>


                            <td style="<?if($dv['show']!=1){?>display:none;<?}?>text-align:left;"><?=$dv['parameter'];?></td>
                            <td style="<?if($dv['show']!=1){?>display:none;<?}?>text-align:center;"><?=$dv['value_type'];?></td>
                             <?
                             foreach($data as $k=>$v){$nf=$formulla=$dv['formulla'];?>
                              <td style="<?if($dv['show']!=1){?>display:none;<?}?>text-align:center;">
                              <?
                              if($dv['parameter']=="Test"){
                              //echo "<pre>";
                              //print_r($_REQUEST);
                              }
                              foreach($matches[0] as $fv){
                               //echo $fv."==".$v[$fv]."<br />";
                              $n=str_replace(" ","",str_replace("[","",str_replace("]","",str_replace("(","",str_replace(")","",$fv)))));

                              if(strpos($fv,',')===false && abs($v[str_replace("[","",str_replace("]","",$fv))])>0)
                              $formulla=str_replace($fv,$v[str_replace("[","",str_replace("]","",$fv))],$formulla);
                              else if(strpos($fv,',')===false && abs($_REQUEST[$n])>0)
                              $formulla=str_replace($fv,$_REQUEST[$n],$formulla);
                              else if(strpos($fv,',')===false && abs($_REQUEST[$n.$k])>0)
                              $formulla=str_replace($fv,$_REQUEST[$n.$k],$formulla);
                              else if(strpos($fv,',')===false && abs($v[str_replace("[","",str_replace("]","",$fv))])==0)
                              $formulla=str_replace($fv,0,$formulla);

                               if(strpos($fv,',')!==false){
                               $fvv=explode(",",str_replace("[","",str_replace("]","",$fv)));
                               unset($fval);
                               foreach($fvv as $ff){
                               $ff=trim($ff);
                               if($ff!=""){
                               if(abs($v[$ff])>0)
                               $fval[]=$v[$ff];
                               }
                               }

                               if(strpos($formulla,'max'.$fv.'')!==false)
                               $formulla=str_replace("max".$fv."",max($fval),$formulla);
                               if(strpos($formulla,'least'.$fv)!==false)
                               $formulla=str_replace("least".$fv."",min($fval),$formulla);
                               }
                               }

                               if(strpos($dv['formulla'],'graph')!==false){
                                if(strpos($dv['formulla'],"||"))
                               $fv=explode("||",$dv['formulla']);
                               else
                               $fv=explode(",",$dv['formulla']);
                               foreach($fv as $graph_formulla){
                               $graph_formulla_temp=$graph_formulla;
                               unset($matches);
                               preg_match_all("/\[[^\]]*\]/", $graph_formulla, $matches);

                               foreach($matches[0] as $fv){
                               $tfv=$fv;
                               $fv=str_replace(" ","",$fv);
                               $n=str_replace("[","",str_replace("]","",$fv));
                               $valc=$_REQUEST[$n];
                               if(abs($valc)<=0)
                               $valc=$_REQUEST[$n.$k];
                               if(abs($valc)<=0)
                               $valc=$v[$n];
                               if(abs($valc)>0)
                               $graph_formulla_temp=str_replace($tfv,$valc,$graph_formulla_temp);
                               }
                               //echo $graph_formulla_temp;
                               //echo "<hr />";
                               if(strpos($graph_formulla_temp,'graph')===false && $graph_formulla_temp!=""){
                               $formulla=str_replace(" ","",$graph_formulla_temp);
                               $graph_formulla_temp=$graph_formulla;
                               break;
                               }
                               $graph_formulla_temp_2=$graph_formulla_temp;
                               $graph_title=substr($graph_formulla_temp,strpos($graph_formulla_temp,'[')+1,100);
                               $graph_title=trim(substr($graph_title,0,strpos($graph_title,']')));
                               $graph_formulla_temp_2=str_replace($graph_title,"",$graph_formulla_temp_2);
                               $graph_formulla_temp_2=str_replace("(graph [] ","",$graph_formulla_temp_2);
                               if(strpos($graph_formulla_temp,' x)')!==false){
                               $xy="x";
                               $extra_cal=trim(substr($graph_formulla_temp,strpos($graph_formulla_temp,' x)')+3,strlen($graph_formulla_temp)));
                               $graph_formulla_temp_2=str_replace(" x)","",$graph_formulla_temp_2);
                               }
                               if(strpos($graph_formulla_temp,' y)')!==false){
                               $extra_cal=trim(substr($graph_formulla_temp,strpos($graph_formulla_temp,' y)')+3,strlen($graph_formulla_temp)));
                               $xy="y";
                               $graph_formulla_temp_2=str_replace(" y)","",$graph_formulla_temp_2);
                               }
                               $graph_formulla_temp_2=explode(" ",str_replace($extra_cal,"",$graph_formulla_temp_2));
                               $tctj=str_replace("tc=","",$graph_formulla_temp_2[1]);
                               $curve_value=eval('return '.str_replace("--","+",$graph_formulla_temp_2[0]).';');
                               $tctj=eval('return '.str_replace("--","+",$tctj).';');
                               $formulla_tmp=checkfromgraph($graph_capture,$v['partno'],$graph_title,$curve_value,$xy,$tctj);

                               $formulla_tmp=explode("**",$formulla_tmp);
                               $formulla=$formulla_tmp[0];
                               $graph_id=$formulla_tmp[1];
                               if($extra_cal!="" && abs($formulla)>0){
                               $formulla=$formulla.$extra_cal;

                               $graph_formulla_temp.=" <a style='font-size:8px;cursor:pointer;color:#FF0000;' onclick='window.open(\"/../show_graph.php?graph_id=".$graph_id."\",\"\", \"toolbar=no,status=no,menubar=no,location=right,scrollbars=no,resizable=no,height=500,width=657\");'>[Graph]</a>";
                               }
                               if(abs($formulla)>0)
                               break;
                               }
                               }

                               $ff=explode("||",$formulla);
                               $dvf=explode("||",$dv['formulla']);
                               //echo $formulla."==".$dv['formulla']."+++++++++".$nf."<hr />";

                               foreach($ff as $ffk1=>$ff1){
                               $n=str_replace("[","",str_replace("]","",ncode($dv['parameter']))).$k;

                                if(abs(eval('return '.str_replace("--","+",trim($ff1)).';'))>0){
                               $p = $_REQUEST[$n] = eval('return '.str_replace("--","+",trim($ff1)).';');

                                $graph_formulla_temp=$dvf[$ffk1];
                               $p=$_REQUEST[$n];
                               $formulla=$ff1;
                                break;
                               }
                               }
                               if($calshow==1){
                               echo number_format($p,abs($dv['decimal']));
                               if($_REQUEST['show_calculation']=="yes"){
                               echo "<br /><span style='color:#428bca;font-size:11px;'>".($graph_formulla_temp!=""?$graph_formulla_temp:$dv['formulla'])."<br />=".$formulla1.$formulla."";
                               }
                               }else{
                               echo "xxxx";
                               }
                               ?>
                              </td>
                             <?}?>
<?if($dv['show']!=1){?></tr><?}?>
<?}?>
</td>
</tr>

<?}}?>
 <?}?>
	    				</tbody>
					</table>


					</div>
						<br />
<?if($_SESSION['showkp']==2 || $_REQUEST['view']==md5($_REQUEST['url']) || $_SESSION['user_kp2_view']==md5($_REQUEST['url'])){?>
						<?

                        $graph_tmp=$graph;
                        foreach($graph_tmp as $k=>$v){
                        $cvm=0;
                        foreach($v as $k1=>$v1){
                        $v1['max']=str_replace("&nbsp;","",$v1['max']);
                        $vv=explode(",",$v1['max']);

                        if(count($vv)==1)
                        $vvm=trim($vv[0]);
                        else
                        $vvm=trim($vv[count($vv)-1]);

                        if(abs($vvm)>0){
                        $graph[$k][$k1]['max']=$vvm;
                        $cvm=1;
                        }
                        if($vvm=="-")
                        $graph[$k][$k1]['max']=0;
                        }

                        foreach($v as $k1=>$v1){
                        $v1['typ']=str_replace("&nbsp;","",$v1['typ']);
                        $vv=explode(",",$v1['typ']);
                        if(count($vv)>=2)
                        $vvt=trim($vv[count($vv)-2]);
                        else
                        $vvt=trim($vv[0]);

                        //echo $k."==".$v1['typ']."==".trim($vvt)."<br />";

                        if(abs($vvt)>0){
                        $cvm=1;
                        $graph[$k][$k1]['typ']=$vvt;
                        }
                        if($vvt=="-")
                        $graph[$k][$k1]['typ']=0;
                        }
                        if($cvm==0)
                        unset($graph[$k]);

                        }
                        unset($graph_tmp);
                        $graph_percentage=$graph;
                        foreach($graph as $k=>$v){
                        $max=0;
                        foreach($v as $k1=>$v1){
                        if($v1['max']>$max && abs($v1['max'])>0)
                        $max=$v1['max'];
                        else if($v1['typ']>$max && abs($v1['typ'])>0)
                        $max=$v1['typ'];
                        }
                        foreach($v as $k1=>$v1){
                        if(abs($v1['max'])>0)
                        $val=$v1['max'];
                        else if(abs($v1['typ'])>0)
                        $val=$v1['typ'];
                        $graph_percentage[$k][$k1]['max']=number_format($val/$max*100,1);
                        unset($graph_percentage[$k][$k1]['typ']);
                        }
                        }

						?>
 <style>
 .inputGroup{padding:10px;background-color:#EFEFEF;margin-bottom:1px;}
 .inputGroup span{margin-left:10px;}
 </style>

   <form method="post" id="download_data" action="compare.php">
 <textarea name="download_text" id="download_text" style="display:none;"></textarea>
 <input type="hidden" name="load" value="download_xlxs" />
 <br />
<center><input type="button" class="btn btn-warning" id="download_xlxs"  style="padding:3px;font-size:12px;color: #fff; background-color: #009688; border-color: #009688;" value=" Download Data In CSV "></center>
  </form>
   <br />
						<table border="0" width="100%" style="border:0px;">
<tr>
  <td width="25%" valign="top" align="left">
  <div style="width:100%;">
  <div id="opt1" style="width:50%;padding:5px;float: left;background-color:#88bce8;cursor:pointer;" onclick="$('#opt1').css('background-color', '#88bce8');$('#opt2').css('background-color', '#dfecf8');">Show as %</div> <div id="opt2" onclick="$('#opt2').css('background-color', '#88bce8');$('#opt1').css('background-color', '#dfecf8');" style="width:50%;padding:5px;float: left;background-color:#dfecf8;cursor:pointer;">Show as value</div>
  </div>
   <?$s=0;foreach($graph as $k=>$v){$s++;?>
   <label for="option<?=$s;?>" style="width: 100%;margin:0px;">
   <div class="inputGroup">
    <input class="chk" id="option<?=$s;?>" value="<?=$k;?>" CHECKED type="checkbox" onclick="graph();" />
    <span><?=$k;?></span>
  </div>
  </label>
   <?}?>
  </td>
  <td width="75%"><canvas id="myChart"></canvas></td>
</tr>
</table>
<?}?>
<?}?>
</div>

</div>
 </section>
 <br /> <br /> <br /> <br /> <br /> <br /> <br />
<?include("../footer.php");?>
<div id="nodis" style="display:none;"></div>
<script src="../admin/Chart.bundle.js"></script>
<script type="text/javascript" charset="utf8" src="../admin/graph/js/jquery.dataTables.js"></script>
<script>
$(document).ready(function(){
$('#download_xlxs').click(function(){
var download_str="";
replaced = $("table.display").html().replace(/<span style="display:none;">1<\/span>/g,'');
replaced = replaced.replace(/<span style="display:none;">0<\/span>/g,'');
$('#nodis').html(replaced);

$("#nodis > thead > tr,#nodis > tbody > tr").each(function () {
//$("table.display > thead > tr,table.display > tbody > tr").each(function () {
tt=$(this).find('td').length;
if($(this).find('th').length>0)
tt=$(this).find('th').length;
for(i=0;i<tt;i++){
v=$(this).find('td').eq(i).text()+$(this).find('th').eq(i).text();
download_str=download_str+encodeURIComponent(v)+"^^";
}
download_str=download_str+"$$";
});
//download_str=download_str.replaceAll('^^$$1^^','^^$$^^');
$('#download_text').val(download_str);
$('#download_data').submit();
})
});
</script>
<? if($_REQUEST['move']!=""){?>
<script>
$('html, body').animate({
        scrollTop: $("#<?=$_REQUEST['move'];?>").offset().top+200
    }, 1000);
</script>
<?}?>
<script>
$(document).ready(function() {
    $('#maintable').DataTable( {
        'fixedHeader': true
    } );
} );
</script>
<?if($_SESSION['showkp']==2 || $_REQUEST['view']==md5($_REQUEST['url']) || $_SESSION['user_kp2_view']==md5($_REQUEST['url'])){?>
<script>
var clickopt="opt1";
var ctx = document.getElementById('myChart').getContext('2d');
var myChart = new Chart(ctx, {
    type: 'radar',
    data: {
    labels: [
<?foreach($graph_percentage as $k=>$v){?>
    '<?=$k;?>',
<?}?>
  ],
  datasets: [
  <?
  $clr=0;
  foreach($data as $k=>$v){
  $clr++;
  $cc=color($clr);
  ?>
  {
    label: '<?=strtoupper($v['partno']);?>',
    data: [<?foreach($graph_percentage as $gk=>$gv){ $vv=$gv[strtolower($v['partno'])]['max']; if($vv==""){$vv=$gv[strtolower($v['partno'])]['typ'];} if($vv==""){$vv="0";}echo str_replace(",","",number_format($vv,1)).", ";}?>],
    fill: true,
    backgroundColor: 'rgba(<?=$cc;?>, 0.2)',
    borderColor: 'rgba(<?=$cc;?>)',
    pointBackgroundColor: 'rgba(<?=$cc;?>)',
    pointBorderColor: '#fff',
    pointHoverBackgroundColor: '#fff',
    pointHoverBorderColor: 'rgba(<?=$cc;?>)'
  },
  <?}?>
  ]
    },
    options: {

            title: {
                display: true,
            },

       elements: {
      line: {
        borderWidth: 3
      }
    }
    }
});
var part={};
<? foreach($data as $k=>$v){?>
part[<?=$k;?>]="<?=strtolower($v['partno']);?>";
<?}?>
var js_array =<?php echo json_encode($graph_percentage);?>;
var js_array_actual =<?php echo json_encode($graph);?>;

function graph(){
if(clickopt=="opt1")
js=js_array;
else
js=js_array_actual;



myChart.data.labels.splice(0,100);
for(i=0;i<myChart.data.datasets.length;i++)
myChart.data.datasets[i].data.splice(0,100);

i=0;
var sel=":";
$(':checkbox').each(function() {
    if(this.checked && $(this).val()!="")
    sel=sel+$(this).val()+":";
});
for (var k in js){
if(sel.indexOf(":"+k+":")>=0){
 myChart.data.labels[i]=k;
 i++;
 }
}


for (var k in part){
i=0;
for (var k1 in js){
if(sel.indexOf(":"+k1+":")>=0){
v=js[k1][part[k]]['max'];
if(v=="" || v==0 || v==undefined)
v=js[k1][part[k]]['typ'];
if(v=="" || v==undefined)
v=0;
if(k1=="Cies [ pF ]"){
// alert(js_array[k1][part[k]]['max']+"=="+js_array[k1][part[k]]['typ']+"=="+v);
}

myChart.data.datasets[k].data[i]=v;
i++;
}
}
}
myChart.update();
}

$('#opt1, #opt2').click(function(){
if($(this).attr('id')=="opt1")
clickopt="opt1";
else
clickopt="opt2";
graph();

})
</script>
<?}?>
<?
function random_color_part() {
    return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
}

function random_color() {
    return random_color_part() . random_color_part() . random_color_part();
}
function getv($s){
if(strpos($s,".")!==false && abs($s)>0)
return $s+0;
else
return $s;
}
function find_replace_tc($t){
global $test_conditions_replace;

foreach($test_conditions_replace as $k=>$v){
$exp=explode("^^",$k);
if($exp[0]!="" && strtolower($t)==strtolower($exp[0]) && $exp[1]!="" && strtolower($t)==strtolower($exp[1])){
return str_ireplace(($exp[0]),$v,($t));
break;
} else if($exp[0]!="" && strtolower($t)==strtolower($exp[0]) && $exp[1]==""){
return str_ireplace(($exp[0]),$v,($t));
break;
}
}

foreach($test_conditions_replace as $k=>$v){
$exp=explode("^^",$k);
if($exp[0]!="" && strpos(strtolower($t),strtolower($exp[0]))!==false && $exp[1]!="" && strpos(strtolower($t),strtolower($exp[1]))!==false){
return str_ireplace(($exp[0]),$v,($t));
break;
} else if($exp[0]!="" && strpos(strtolower($t),strtolower($exp[0]))!==false && $exp[1]==""){
return str_ireplace(($exp[0]),$v,($t));
break;
}
}
return $t;
}

function gettc($str,$tc){



$str=str_replace("Non-auto","Non-Auto",$str);
if($tc=="")
return ucfirst($str);

$str=str_replace(" ","",$str);
$tcstr=explode(",",$tc);
$dstr="";


foreach($tcstr as $tcv){
$check=explode("=",$tcv);
$tcvstr=explode("||",strtolower($check[0]));
foreach($tcvstr as $v){
$v=trim($v);
if(strpos($str,$v)!==false){
$cstr=substr($str,strpos($str,$v)+strlen($v),25);
$cstr=str_replace(" ","",$cstr);

$sym=str_replace("(","[",str_replace(")","]",$check[1]));

$checkunit="";
if(strpos($sym,"[")!==false){
$checkunit=trim(strtolower(str_replace("]","",trim(substr($sym,strpos($sym,"[")+1,20)))));
$sym=substr($sym,0,strpos($sym,"["));
}

$cstr1=substr($cstr,0,strpos($cstr,$checkunit)+strlen($checkunit));
$str=str_replace($cstr1,"",$str);
$str=trim(str_replace($v,"",$str));

if(strpos($cstr1,"=")!==false)
$cstr1=trim(substr($cstr1,strpos($cstr1,"="),strlen($cstr1)));
else if(strpos($cstr1," ")!==false)
$cstr1=trim(substr($cstr1,strpos($cstr1," "),strlen($cstr1)));

$cstr1=trim(str_replace('=','',$cstr1));
$cstr1=trim(str_replace('+','',$cstr1));
$cstr1=trim(str_replace(',','',$cstr1));

$dstr.=$sym."=".$cstr1.", ";

break;
}
}
}
$dstr=utf8_decode($dstr);
$dstr=str_replace("?c","&deg;c",$dstr);
$dstr=str_replace("/Î¼s","/µs",$dstr);
//exit;

if($dstr!=""){
$tcstr= substr($dstr,0,strlen($dstr)-2);
$tcstr=strtoupper($tcstr);
if(strpos($tcstr,"MHZ")!==false)
$tcstr=str_replace("MHZ","MHz",$tcstr);
if(strpos($tcstr,"MA")!==false)
$tcstr=str_replace("MA","mA",$tcstr);
if(strpos($tcstr,"&DEG;C")!==false)
$tcstr=str_replace("&DEG;C","&deg;C",$tcstr);
if(strpos($tcstr,"A/?S")!==false)
$tcstr=str_replace("A/?S","A/us",$tcstr);
if((strpos(strtolower($tcstr),"tj=")!==false || strpos(strtolower($tcstr),"tc=")!==false) && strpos($tcstr,"?")!==false)
$tcstr=str_replace("?","&deg;C",$tcstr);
if(strpos(strtolower($tcstr),"tc=")!==false && strpos(strtolower($tcstr),"oc")!==false)
$tcstr=str_replace("OC","&deg;C",$tcstr);
$tt=explode(",",$tcstr);
$tcstr="";
foreach($tt as $t){
if(strpos($t,"RG=")!==false && (strpos($t,"VGE,")!==false || strpos($t,"OHM,")!==false))
$t=str_replace("VGE,",str_replace("OHM,","&#x03A9;",$t));

if(strpos($t,"RG=")!==false && strpos($t,"„?")!==false)
$t=str_replace("„?","&#x03A9;",$t);
if(strpos($t,"RG=")!==false && strpos($t,"&#x03A9;")===false)
$t="RG=".(int) filter_var($t, FILTER_SANITIZE_NUMBER_INT)."&#x03A9;";
if($t!="")
$tcstr.=find_replace_tc($t).",";
}
$tcstr=substr($tcstr,0,strlen($tcstr)-1);
return $tcstr;
}
}
function color($clr){
if($clr==1)
return "6,58,81"; //063a51
else if($clr==2)
return "193,49,25"; //c13119
else if($clr==3)
return "243,111,20"; //f36f14
else if($clr==4)
return "235,203,57";  //ebcb39
else if($clr==5)
return "163,185,105";  //a3b969
else if($clr==6)
return "13,150,188";  //0d96bc
else
return rand(50,255).", ".rand(50,255).", ".rand(50,255);

}

function ncode($ncode){
$ncode=str_replace(" ","",$ncode);
$ncode=str_replace("#","",$ncode);
$ncode=str_replace("(","",$ncode);
$ncode=str_replace(")","",$ncode);
if(strpos($ncode,'[')!==false)
$ncode=substr($ncode,0,strpos($ncode,'['));
$ncode=strtolower($ncode);
return "[".$ncode."]";
}


function getgraphdata($graphxy,$reverse=''){
$str="";
foreach($graphxy as $k=>$v){
if($reverse=="")
$str.="x=".$v->x.", y=".$v->y."<br />";
else
$str.="x=".$v->y.", y=".$v->x."<br />";
}
return $str;
}

function checkfromgraph($graph_capture,$partno,$graph_title,$xvalue,$xy,$tctj){

$unit_x=1;
$unit_y=1;

$returnxy=$xy;
$checkxy='x';
if($returnxy=="x")
$checkxy="y";


if($tctj!=""){
foreach($graph_capture[$partno][$graph_title] as $k=>$v){
$tc=trim(strtolower($v['tctj']));

if($tctj==$tc || $tctj."c"==$tc){}else if($tctj>0){
unset($graph_capture[$partno][$graph_title][$k]);
}
}
}
   $firstkey=key($graph_capture[$partno][$graph_title]);

if(count($graph_capture[$partno][$graph_title][$firstkey])>0){

$g=json_decode($graph_capture[$partno][$graph_title][$firstkey]['xy']);

$logscale=$graph_capture[$partno][$graph_title][$firstkey][$returnxy.'scale'];
$graph_id=$graph_capture[$partno][$graph_title][$firstkey]['graph_id'];

if($logscale=="log")
$logscale=1;
else
$logscale=0;

//echo "<pre>".$xvalue;
//print_r($g);
foreach(json_decode($graph_capture[$partno][$graph_title][$firstkey]['xy']) as $k=>$v){

if($returnxy=="x"){
$xx=$v->x;
if($logscale==1)
$xx=log10($v->x);
}
if($returnxy=="y"){
$yy=$v->y;
if($logscale==1)
$yy=log10($v->y);
}

if(abs($xvalue1)==0 && abs($xvalue2)==0 && $xvalue>=$v->$checkxy  && $xvalue<=$g[$k+1]->$checkxy){

$xvalue1=$v->$checkxy;
$xvalue2=$g[$k+1]->$checkxy;

$yvalue1=$v->$returnxy;
if($logscale==1)
$yvalue1=log10($v->$returnxy);

$yvalue2=$g[$k+1]->$returnxy;
if($logscale==1)
$yvalue2=log10($g[$k+1]->$returnxy);



}else if(abs($xvalue1)==0 && abs($xvalue2)==0 && $xvalue<=$v->$checkxy  && $xvalue>=$g[$k+1]->$checkxy){

$xvalue2=$v->$checkxy;
$xvalue1=$g[$k+1]->$checkxy;

$yvalue2=$v->$returnxy;
if($logscale==1)
$yvalue2=log10($v->$returnxy);

$yvalue1=$g[$k+1]->$returnxy;
if($logscale==1)
$yvalue1=log10($g[$k+1]->$returnxy);


}else if(abs($xvalue1)==0 && abs($xvalue2)==0 && $xvalue<=$v->$checkxy && $xvalue>=$graph[$k+1]->$checkxy && $returnxy=="x"){

$xvalue1=$v->$checkxy;
$xvalue2=$g[$k+1]->$checkxy;

$yvalue1=$v->$returnxy;
if($logscale==1)
$yvalue1=log10($v->$returnxy);

$yvalue2=$g[$k+1]->$returnxy;
if($logscale==1)
$yvalue2=log10($g[$k+1]->$returnxy);



}else if(abs($xvalue1)==0 && abs($xvalue2)==0 && $xvalue>=$v->$checkxy && $xvalue<=$graph[$k+1]->$checkxy && $returnxy=="x"){

$xvalue2=$v->$checkxy;
$xvalue1=$g[$k+1]->$checkxy;

$yvalue2=$v->$returnxy;
if($logscale==1)
$yvalue2=log10($v->$returnxy);

$yvalue1=$g[$k+1]->$returnxy;
if($logscale==1)
$yvalue1=log10($g[$k+1]->$returnxy);


}



}





if(abs($xvalue1)==0){



if($xvalue<$g[0]->$checkxy){
$xvalue1=$g[0]->$checkxy;
$xvalue2=$g[0]->$checkxy+0;

$yvalue1=$g[0]->$returnxy;
if($logscale==1)
$yvalue1=log10($g[0]->$returnxy);

$yvalue2=$g[0]->$returnxy+0;
if($logscale==1)
$yvalue2=log10($g[0]->$returnxy);



}else if($xvalue>$g[(count($g)-1)]->$checkxy){
$xvalue1=$g[(count($g)-1)]->$checkxy;
$xvalue2=$g[(count($g)-1)]->$checkxy+0;

$yvalue1=$g[(count($g)-1)]->$returnxy;
if($logscale==1)
$yvalue1=log10($g[(count($g)-1)]->$returnxy);

$yvalue2=$g[(count($g)-1)]->$returnxy+0;
if($logscale==1)
$yvalue2=log10($g[(count($g)-1)]->$returnxy+0);



}

}




$xdifference=$xvalue2-$xvalue1;
$ydifference=$yvalue2-$yvalue1;
$difference=$ydifference/$xdifference;
$axd=($xvalue-$xvalue1)*$difference;
$y=$yvalue1+$axd;



if(abs($y)>0)
return str_replace(",","",number_format($y*$unit_y,2))."**".$graph_id;
else
return 0;


}
return '0';
}

function checksplit($f,$k,$s,$vtt){

if(strtolower($s)=="na")
return "NA";

if(strtolower($s)=="tvs diode")
return "TVS Diode";
if(strtolower($s)=="pin diode")
return "PIN Diode";
if(strtolower($s)=="sic schottky diode")
return "SiC Schottky Diode";
if(strtolower($s)=="esd diode")
return "ESD Diode";

global $manf_array;



foreach($manf_array as $vc){
if(strtolower($vc['org_manf'])==strtolower($s)){
return $vc['org_manf'];
break;
}
}

global $value_array,$r;

if($value_array[$r['link_with']][strtolower($s)]['org_value']!="")
return $value_array[$r['link_with']][strtolower($s)]['org_value'];

if(strpos(strtolower($vtt),'condition')!==false || strpos(strtolower($s),'non-auto')!==false)
return $s;

if(find_replace_tc($s)!="" && find_replace_tc($s)!=$s && (substr($f,strlen($f)-2,2)=="tc" || substr($f,strlen($f)-3,2)=="tc"))
return find_replace_tc($s);


 global $rray;
 global $product_page_setting_convert_text;


  global $rr_manf;
 if($f=="manf"){
 if($rr_manf[strtolower($s)]!="")
 return $rr_manf[strtolower($s)];
 }

 $fu= $product_page_setting_convert_text[$f];
 if($fu=="")
 $fu="ucwords";

 if(count($rray[$k][$f])<=0)
 return ${fu}(strtolower($s));

 $s=trim($s);
 $str="";



 foreach($rray[$k][$f] as $key=>$value){
 //echo $sv."==".$s."<hr />";
 $search_include=explode(",",$value['inc']);
 $search_exclude=explode(",",$value['exc']);
 foreach($search_include as $si=>$sv){
 $sv=trim($sv);
 if($sv!=""){
 $pass=0;
 $sv=explode("||",$sv);
 foreach($sv as $si1=>$sv1){
 $sv1=trim($sv1);
 if($sv1!=""){
 if(strpos(strtolower($s),strtolower($sv1))!==false){
 $pass=1;
 break;
 }}}

 }}



 foreach($search_exclude as $si=>$sv){
 $sv=trim($sv);
 if($sv!=""){
 $pass1=0;
 $sv=explode("||",$sv);
 foreach($sv as $si1=>$sv1){
 $sv1=trim($sv1);
 if($sv1!=""){
 if(strpos(strtolower($s),strtolower($sv1))!==false){
 $pass1=1;
 break;
 }}}

 }}



 if($pass==1 && $pass1==0)
 $str.=${fu}(strtolower($key))."<hr />";
 }

 if($str!="")
 return $str;
 else
 return ${fu}(strtolower($s));
}

function get_title($c1,$c2,$c3){
if($c1>0)
$s1=mysql_fetch_array(mysql_query("SELECT title FROM srai_others.discoveree_product_category WHERE id=".$c1));
if($c2>0)
$s2=mysql_fetch_array(mysql_query("SELECT title FROM srai_others.discoveree_product_category WHERE id=".$c2));
if($c3>0)
$s3=mysql_fetch_array(mysql_query("SELECT title FROM srai_others.discoveree_product_category WHERE id=".$c3));

if($s1[0]!="" && $s2[0]!="" && $s3[0]!="")
return $s1[0]."-".$s2[0]."-".$s3[0];
else if($s1[0]!="" && $s2[0]!="" && $s3[0]=="")
return $s1[0]."-".$s2[0];
if($s1[0]!="" && $s2[0]=="" && $s3[0]=="")
return $s1[0];

}

function utf8ize( $mixed ) {
    if (is_array($mixed)) {
        foreach ($mixed as $key => $value) {
            $mixed[$key] = utf8ize($value);
        }
    } elseif (is_string($mixed)) {
        return mb_convert_encoding($mixed, "UTF-8", "UTF-8");
    }
    return $mixed;
}
?>

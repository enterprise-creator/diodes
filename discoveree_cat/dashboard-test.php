<?php include "../includes/config.inc.php";?>
<?php
if(($_SESSION['user_name']!="dnp1976" && $_SESSION['user_name']!="srai") && $_SESSION['test_user_id']==""){
if(strpos($_SESSION['opage_access'],"id=".$_REQUEST['id']."&discoveree_cat_id=".$_REQUEST['discoveree_cat_id'])===false){
$_SESSION['lasturl'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] .  $_SERVER['REQUEST_URI'];

header("Location: /");
exit;
}
}



if($_SESSION['expired_user']==1 && $_COOKIE['expired_user_dashboard']==1){
header("Location: https://www.discoveree.io/request_upgrade.php");
exit;
}
if($_SESSION['expired_user']==1){
$expire=time()+60*60*24;//however long you want
setcookie('expired_user_dashboard', 1, $expire,'/');
}

ini_set('memory_limit', '-1');
//mysql_query("INSERT INTO users_landing_page_log_time SET log_id=".$_SESSION['log_id'].",page='".str_replace("/","",$_SERVER['SCRIPT_URL'])."',username='".$_SESSION['user_name']."',action='start'");

$dashboard_setting=mysql_fetch_array(mysql_query("SELECT * FROM srai_others.dashboard_setting WHERE id=".$_REQUEST['id']));
$default_style=json_decode($dashboard_setting['graph_settings'], true);
$indv_default_style=json_decode($dashboard_setting['indv_graph_settings'], true);
$random_default_style=json_decode($dashboard_setting['random_graph_settings'], true);

$show_part_status=$dashboard_setting['show_part_status'];

if($_REQUEST['manf_access']==""){
$checkmanfassign=mysql_fetch_array(mysql_query("SELECT manf FROM srai_others.usermanf_assign WHERE id=".$_REQUEST['id']." AND username='".$_SESSION['user_name']."'"));
if($checkmanfassign[0]!="")
$_REQUEST['manf_access']=$checkmanfassign[0];
else if($dashboard_setting['default_manf']!="")
$_REQUEST['manf_access']=$dashboard_setting['default_manf'];
}

$find_replace_test_conditions=(mysql_query("SELECT find1,find2,replacewith FROM srai_others.find_replace_test_conditions WHERE 1"));
while($mswrow=mysql_fetch_array($find_replace_test_conditions)){
$test_conditions_replace[$mswrow[0]."^^".$mswrow[1]]=trim($mswrow[2]);
}

$dss=mysql_query("SELECT * FROM srai_others.tooltips ORDER BY title");
while($drow=mysql_fetch_array($dss))
$tooltip[trim($drow['title'])]=trim($drow['tooltip']);

$getmanf_array=mysql_query("SELECT * FROM srai_others.manage_manf_name");
while($bh=mysql_fetch_array($getmanf_array)){
if($bh['manf_array']!="")
$manf_array=json_decode($bh['manf_array'],true);
if($bh['value_array']!="" && $bh['discoveree_cat_id']==$_REQUEST['discoveree_cat_id'])
$value_array=json_decode($bh['value_array'],true);
}
$dfm=0;

foreach($manf_array as $k=>$v)
{
$rr_manf[strtolower($v['source_manf'])]=$v['org_manf'];
if($v['default_manf']==1)
$dfm=1;

if($default_manf[$v['org_manf']]==""){
$default_manf[$v['org_manf']]=$v['default_manf'];
$default_manf_lower[strtolower($v['org_manf'])]=$v['default_manf'];
}
}

 if($_SESSION['user_name']=="dnp1976"){
//echo "<pre>";
//print_r($rr_manf);
//print_r($default_manf);
//exit;
 }



foreach($indv_default_style as $k=>$v)
$tmp_indv[strtolower($k)]=$v;
unset($indv_default_style);
$indv_default_style=$tmp_indv;
unset($tmp_indv);

foreach($random_default_style as $k=>$v)
$tmp_indv[strtolower($k)]=$v;
unset($random_default_style);
$random_default_style=$tmp_indv;
unset($tmp_indv);

$field_x=$dashboard_setting['link_withx'];
$field_y=$dashboard_setting['link_withy'];

$filter_search=$dashboard_setting['fieldx'];
$filter_search.=$dashboard_setting['fieldy'];

$dropdown_options=json_decode($dashboard_setting['dropdown_options'], true);
if(count($dropdown_options)>0){
$strdd="";
foreach($dropdown_options as $dk=>$dv)
$strdd.=$dk.",";

$xy=1;
$dashboard_setting_dd=(mysql_query("SELECT * FROM srai_others.dashboard_setting WHERE id in (".substr($strdd,0,strlen($strdd)-1).")"));
while($droprow=mysql_fetch_array($dashboard_setting_dd)){

$link_valuex[$xy]=$droprow['link_valuex'];
$link_valuey[$xy]=$droprow['link_valuey'];

$field_x[$xy]=$droprow['link_withx'];
$field_y[$xy]=$droprow['link_withy'];

$fieldx[$xy]=$droprow['fieldx'];
$fieldy[$xy]=$droprow['fieldy'];

$multiplyx[$xy]=$droprow['multiplyx'];
$multiplyy[$xy]=$droprow['multiplyy'];

$filter_search.=$droprow['fieldx'];
$filter_search.=$droprow['fieldy'];

$xy++;
}
}


$c=0;
$sql="SELECT * FROM srai_others.dashboard_search_setting WHERE dashboard_setting_id=".$_REQUEST['id']." ORDER BY priority";
$results = mysql_query($sql);
while($row = mysql_fetch_assoc($results))
{
    $filter_search.=$row['fieldname'];
    foreach($row as $key => $value)
    {
    $search[$c][$key]=trim($value);
    }
        $multiplyby[$row['multiplyby']."_".$c] = $row['fieldname'];
$c++;
}

$filter_search.='partno,manf,discoveree_cat_id,searchword,';


if($_REQUEST['type']!="development"){
$eq="SELECT * FROM srai_others.discoveree_error_reporting_condition  WHERE NOT condition_type='Null' AND discoveree_cat_id=".$_REQUEST['discoveree_cat_id']." and symbol_field in ('".$field_x."','".$field_y."')";
$error_reporting=mysql_query($eq);
while($err=mysql_fetch_array($error_reporting))
if($field_x==strtolower($err['symbol_field']))
$error_rep['x'][$err['condition_type']]=$err['value'];
else
$error_rep['y'][$err['condition_type']]=$err['value'];
}
//echo "<pre>";
//print_r($error_rep);
//exit;
//$field="";
//foreach($search as $v){
//$fieldname=explode(",",$v['fieldname']);
//foreach($fieldname as $vv){
//if($v!="" && strpos($field,$vv.",")===false)
//$field.=$vv.",";
//}
//}
//".$dashboard_setting['fieldx'].$dashboard_setting['fieldy'].$field."partno,id,manf

if($dashboard_setting['discoveree_cat_id']!=""){
$manf_search_words=(mysql_query("SELECT title,save_word,no_word FROM srai_others.manf_search_words WHERE discoveree_cat_id=".$dashboard_setting['discoveree_cat_id']." GROUP BY title,save_word,no_word ORDER BY save_word,no_word"));
while($mswrow=mysql_fetch_array($manf_search_words)){
$msw[$mswrow[0]].=trim($mswrow[1])."||".trim($mswrow[2])."||";
}
}

//echo "<pre>";
//print_r($msw);
  if($_REQUEST['legend_view']=="qualification"){
 $mm=explode("||",$msw['Auto']);
 foreach($mm as $k=>$v){
 if($v=="")
 $v="NA";
  $qlists[]=$v;
 }
 }
  if($_REQUEST['legend_view']=="configuration"){
 $mm=explode("||",$msw['Config']);
 foreach($mm as $k=>$v){
 if($v=="")
 $v="NA";
  $qlists[]=$v;
 }
 }

$spc="";
$spc1="";
$spcex=explode(",",$dashboard_setting['fieldx']);
foreach($spcex as $s=>$p){
if($p!=""){
$spc1.=" (NOT ".$p."='' AND NOT isnull(".$p.")) OR ";
}
}
if($spc1!="")
$spc.=" AND (".substr($spc1,0,strlen($spc1)-3).") ";
$spc1="";
$spcex=explode(",",$dashboard_setting['fieldy']);
foreach($spcex as $s=>$p){
if($p!=""){
$spc1.=" (NOT ".$p."='' AND NOT isnull(".$p.")) OR ";
}
}
if($spc1!="")
$spc.=" AND (".substr($spc1,0,strlen($spc1)-3).") ";

$cnt=0;
$live_sql="SELECT * FROM srai_others.discoveree_live_".$dashboard_setting['discoveree_cat_id']."_v2 WHERE ((NOT ignorefld LIKE '%datasheet-partno-mismatch-issue%' AND NOT ignorefld LIKE '%wrongcategory%' AND NOT ignorefld LIKE '%removepart%'  AND NOT ignorefld LIKE '%datasheetissue%'  AND NOT ignorefld LIKE '%crawl-again%'  AND NOT ignorefld LIKE '%datasheet-format-issue%') OR isnull(ignorefld))  ";

$mac="";
$manfaccess=explode(",",$_REQUEST['manf_access']);
foreach($manfaccess as $m)
if($m!="")
$mac.="'".$m."',";

$live_sql.=$spc;

if($show_part_status==1 && ($_REQUEST['show_part_status']=="" || $_REQUEST['show_part_status']==1))
$live_sql.=" AND data_source=1 ";
if($show_part_status==1 && $_REQUEST['show_part_status']==2)
$live_sql.=" AND data_source=2 ";



if($_SESSION['user_name']=="sanden_demo")
$live_sql.=" AND manf in('rohm','silan','ncepower') ";
if(strpos($dashboard_setting['fieldx'],'package')!==false)
$live_sql.=" ORDER BY discoveree_area DESC";



$result=mysql_query($live_sql);
while($row = mysql_fetch_assoc($result))
{



 //$c=manflabel($row[$dashboard_setting['legend']]);
 $c=checksplit($dashboard_setting['legend'],'',$row[$dashboard_setting['legend']],'');


   if($_REQUEST['legend_view']=="category")
 $c=$othercat[$row['discoveree_cat_id']];
 if($_REQUEST['legend_view']=="package")
 $c=strtoupper($row['package']);
 if($_REQUEST['legend_view']=="qualification" || $_REQUEST['legend_view']=="configuration"){
 foreach($qlists as $qv){
 if(strpos(strtolower(",".trim($row['searchword']).","),strtolower(",".trim($qv).","))!==false){
 $c=$qv;
 break;
 }else{
 $c="NA";
 }
 }
 }
// if(($mac=="" || ($mac!="" && strpos(strtolower($mac),"'".strtolower($row['manf'])."'")!==false)) && ($show_part_status==0 || ($show_part_status==1 && $row['data_source']==1))){
  if($mac=="" || ($mac!="" && strpos(strtolower(str_replace(" ","",$mac)),"'".strtolower(str_replace(" ","",$row['manf']))."'")!==false)){
 $data[$c][$cnt]['partno']=$row['partno'];
 $data[$c][$cnt]['manf_org']=$row['manf'];

 foreach($row as $key => $value)
{

if(count($value_array[$key][$value])>0)
$value=$value_array[$key][$value]['org_value'];
else if(count($value_array[$key][trim($value)])>0)
$value=$value_array[$key][trim($value)]['org_value'];

    $key=trim($key);
    $value=str_replace("Âss","",utf8_encode($value));
    $value=trim($value);
    $value=trim(str_replace("\n","",str_replace("\r","",str_replace("\t","",trim($value)))));
     if($key=="package" || (strpos($filter_search,$key.",")!==false && $key!="features" && $key!="user_id" && $key!="fixingtime_min" && $key!="searchword" && strlen($key)>=2 && substr($key, -2)!="tc" && substr($key, -3)!="tc1" && substr($key, -3)!="tc2"  && substr($key, -3)!="tc3"  && substr($key, -3)!="tc4" && strpos($value,"=")===false && strpos($value,",")===false && strpos($value,";")===false)){

     if(strpos($key,'discoveree_')!==false)
     $vvv=trim($value);
     else
     $vvv=trim(checksplit($key,'',$value,''));

     $data[$c][$cnt][$key]=str_replace(",","",str_replace("(","",str_replace(")","",str_replace("[","",str_replace("]","",$vvv)))));
      //if($data[$c][$cnt][$key]=="")
      //$data[$c][$cnt][$key]="0";
     }
     if($key=="searchword"){
      //echo $value."<br />";
      foreach($msw as $mswk=>$mswv){
      $valuemsw=",".strtolower($value).",";
      $mswstr=explode("||",strtolower($mswv));
      $msf=0;
      foreach($mswstr as $mss){
      if(strpos($valuemsw,",".$mss.",")!==false && str_replace(",","",$mss)!=""){
      $data[$c][$cnt][$key.$mswk].=str_replace(",","",str_replace("(","",str_replace(")","",str_replace("[","",str_replace("]","",trim(manflabel($mss))))))).",";
      $msf=1;
      }
      }
      if($msf==0)
      $data[$c][$cnt][$key.$mswk]="";
      }
     }
}

/////////////////////////// For Default X/Y - Start
 if($dashboard_setting['link_valuex']=="exact")
 $data[$c][$cnt]['x']=getv(trim($row[str_replace(",","",$dashboard_setting['fieldx'])]));

 if($dashboard_setting['link_valuey']=="exact")
 $data[$c][$cnt]['y']=getv(trim($row[str_replace(",","",$dashboard_setting['fieldy'])]));



 unset($xv);
 $getv=explode(",",$dashboard_setting['fieldx']);
 foreach($getv as $v){
 if($v!="" && abs($row[$v])>0){
 $xv[]=$row[$v];
 }
 }

 unset($yv);
 $getv=explode(",",$dashboard_setting['fieldy']);
 foreach($getv as $v){
 if($v!="" && abs($row[$v])>0){
 $yv[]=$row[$v];
 }
 }


 if($dashboard_setting['link_valuex']=="maximum")
 $data[$c][$cnt]['x']=getv(max($xv));

 if($dashboard_setting['link_valuex']=="least")
 $data[$c][$cnt]['x']=getv(min($xv));

 if($dashboard_setting['link_valuey']=="maximum")
 $data[$c][$cnt]['y']=getv(max($yv));

 if($dashboard_setting['link_valuey']=="least")
 $data[$c][$cnt]['y']=getv(min($yv));


 $xcond=$ycond=0;

 if(count($error_rep)>0){
 $lcond=$gcond=0;

 foreach($error_rep['x'] as $xyk=>$xyv){
 if($xyk=="LIKE"){
 if(strpos($data[$c][$cnt]['x'],$xyv)===false)
 $xcond=1;
 }else  if($xyk=="="){
 if($data[$c][$cnt]['x']!=$xyv)
 $xcond=1;
 }else  if($xyk=="<="){
 if($data[$c][$cnt]['x']<=$xyv)
 $lcond=1;
 }else  if($xyk==">="){
 if($data[$c][$cnt]['x']>=$xyv)
 $gcond=1;
 }
 if($lcond==1 && $gcond==1)
 $xcond=1;
 if($xcond==1)
 break;
 }
  $lcond=$gcond=0;
  foreach($error_rep['y'] as $xyk=>$xyv){
 if($xyk=="LIKE"){
 if(strpos($data[$c][$cnt]['y'],$xyv)===false)
 $ycond=1;
 }else  if($xyk=="="){
 if($data[$c][$cnt]['y']!=$xyv)
 $ycond=1;
 }else  if($xyk=="<="){
 if($data[$c][$cnt]['y']<=$xyv)
 $lcond=1;
 }else  if($xyk==">="){
 if($data[$c][$cnt]['y']>=$xyv)
 $gcond=1;
 }
  if($lcond==1 && $gcond==1)
 $ycond=1;
 if($ycond==1)
 break;
 }
 }

 if(strpos($dashboard_setting['fieldx'],"package")===false && strpos($dashboard_setting['fieldy'],"package")===false && strpos($dashboard_setting['fieldx'],"searchword")===false && strpos($dashboard_setting['fieldy'],"searchword")===false && ($xcond==1 || $ycond==1 || !is_numeric($data[$c][$cnt]['x']) || !is_numeric($data[$c][$cnt]['y']))){
 $data[$c][$cnt]['x']=$data[$c][$cnt]['y']="0";
 }



 if(abs($data[$c][$cnt]['x'])>0 && $dashboard_setting['multiplyx']>1)
  $data[$c][$cnt]['x']=getv($data[$c][$cnt]['x']*$dashboard_setting['multiplyx']);

 if(abs($data[$c][$cnt]['y'])>0 && $dashboard_setting['multiplyy']>1)
  $data[$c][$cnt]['y']=getv($data[$c][$cnt]['y']*$dashboard_setting['multiplyy']);

  ////////////////////////////////////  For Default X/Y - End

   $data[$c][$cnt]['x']=getv(str_replace(",","",$data[$c][$cnt]['x']));
   $data[$c][$cnt]['y']=getv(str_replace(",","",$data[$c][$cnt]['y']));

    if(!array_key_exists($c."@".$data[$c][$cnt]['x'],$jitterx) && abs($data[$c][$cnt]['x'])>0)
    $jitterx[$c."@".$data[$c][$cnt]['x']]=getcount($jitterx,$data[$c][$cnt]['x'])+1;


/////////////////////////// For Dropdown X/Y - Start
foreach($link_valuex as $ddk=>$ddv){
 if($link_valuex[$ddk]=="exact")
 $data[$c][$cnt]['x'.$ddk]=$row[str_replace(",","",$fieldx[$ddk])];

 if($link_valuey[$ddk]=="exact")
 $data[$c][$cnt]['y'.$ddk]=$row[str_replace(",","",$fieldy[$ddk])];

 unset($xv);
 $getv=explode(",",$fieldx[$ddk]);
 foreach($getv as $v){
 if($v!="" && abs($row[$v])>0){
 $xv[]=$row[$v];
 }
 }

 unset($yv);
 $getv=explode(",",$fieldy[$ddk]);
 foreach($getv as $v){
 if($v!="" && abs($row[$v])>0){
 $yv[]=$row[$v];
 }
 }


 if($link_valuex[$ddk]=="maximum")
 $data[$c][$cnt]['x'.$ddk]=max($xv);

 if($link_valuex[$ddk]=="least")
 $data[$c][$cnt]['x'.$ddk]=min($xv);

 if($link_valuey[$ddk]=="maximum")
 $data[$c][$cnt]['y'.$ddk]=max($yv);

 if($link_valuey[$ddk]=="least")
 $data[$c][$cnt]['y'.$ddk]=min($yv);

 $xcond=$ycond=0;
 $lcond=$gcond=0;
 if(count($error_rep[$ddk])>0){
 foreach($error_rep[$ddk]['x'] as $xyk=>$xyv){
 if($xyk=="LIKE"){
 if(strpos($data[$c][$cnt]['x'.$ddk],$xyv)===false)
 $xcond=1;
 }else  if($xyk=="="){
 if($data[$c][$cnt]['x'.$ddk]!=$xyv)
 $xcond=1;
 }else  if($xyk=="<="){
 if($data[$c][$cnt]['x'.$ddk]<=$xyv)
 $lcond=1;
 }else  if($xyk==">="){
 if($data[$c][$cnt]['x'.$ddk]>=$xyv)
 $gcond=1;
 }
   if($lcond==1 && $gcond==1)
 $xcond=1;
 if($xcond==1)
 break;
 }
  $lcond=$gcond=0;
  foreach($error_rep[$ddk]['y'] as $xyk=>$xyv){
 if($xyk=="LIKE"){
 if(strpos($data[$c][$cnt]['y'.$ddk],$xyv)===false)
 $ycond=1;
 }else  if($xyk=="="){
 if($data[$c][$cnt]['y'.$ddk]!=$xyv)
 $ycond=1;
 }else  if($xyk=="<="){
 if($data[$c][$cnt]['y'.$ddk]<=$xyv)
 $lcond=1;
 }else  if($xyk==">="){
 if($data[$c][$cnt]['y'.$ddk]>=$xyv)
 $gcond=1;
 }
   if($lcond==1 && $gcond==1)
 $ycond=1;
 if($ycond==1)
 break;
 }
 }

 if($xcond==1 || $ycond==1 ||  !is_numeric($data[$c][$cnt]['x'.$ddk]) || !is_numeric($data[$c][$cnt]['y'.$ddk])){
 $data[$c][$cnt]['x'.$ddk]=$data[$c][$cnt]['y'.$ddk]="0";
 }

 if(abs($data[$c][$cnt]['x'.$ddk])>0 && $multiplyx[$ddk]>1)
  $data[$c][$cnt]['x'.$ddk]=$data[$c][$cnt]['x'.$ddk]*$multiplyx[$ddk];

 if(abs($data[$c][$cnt]['y'.$ddk])>0 && $multiplyy[$ddk]>1)
  $data[$c][$cnt]['y'.$ddk]=$data[$c][$cnt]['y'.$ddk]*$multiplyy[$ddk];


     $data[$c][$cnt]['x'.$ddk]=str_replace(",","",$data[$c][$cnt]['x'.$ddk]);
   $data[$c][$cnt]['y'.$ddk]=str_replace(",","",$data[$c][$cnt]['y'.$ddk]);

}
  ////////////////////////////////////  For Dropdown X/Y - End


  foreach($multiplyby as $km=>$mv){
    $mb=explode("-",$km);
    if($mb[0]>1){
    $mpv=explode(",",$mv);
    foreach($mpv as $mpvv){
     if(abs($data[$c][$cnt][$mpvv])>0){
     $mb[0]=substr($mb[0],0,strpos($mb[0],'_'));
     $data[$c][$cnt][$mpvv]=$data[$c][$cnt][$mpvv]*$mb[0];
     }
    }
    }
  }
  $tmpdata[]=$c;
  if((abs($data[$c][$cnt]['y'])==0 || $data[$c][$cnt]['x']=="") && strpos($dashboard_setting['fieldx'],"package")===false)
  unset($data[$c][$cnt]);
  else if(abs($data[$c][$cnt]['y'])==0 && strpos($dashboard_setting['fieldx'],"package")!==false)
  unset($data[$c][$cnt]);

}else{
if(count($data[strtolower($row['manf'])])<=0)
 $data[strtolower($c)][$cnt]['manf']=$row['manf'];
}

$cnt++;
}
   echo date("Y-m-d H:i:s");
exit;
foreach($tmpdata as $k=>$v){
if(count($data[$v])<=0)
unset($data[$v]);
}

if(strpos($dashboard_setting['fieldx'],"package")!==false){
foreach($data as $k=>$v)
if(count($v)<=0)
unset($data[$k]);
}

 $dresults=mysql_query("select  * FROM srai_others.part_split_field_value WHERE discoveree_cat_id= '".$_REQUEST['discoveree_cat_id']."'    ORDER BY id ");
while($drow=mysql_fetch_array($dresults)){
$rray[$drow['discoveree_cat_id']][$drow['symbol_field']][$drow['display']]=array("inc"=>$drow['search_include'],"exc"=>$drow['search_exclude']);
}

 $sql="SELECT * FROM srai_others.product_page_setting WHERE discoveree_cat_id in (".$_REQUEST['discoveree_cat_id'].") ORDER BY priority";
$product_setting=(mysql_query($sql));
$cnt=0;
while($row = mysql_fetch_assoc($product_setting)){
foreach($row as $key => $value)
{
    if($key!="id" && $key!="discoveree_cat_id"){
     $product_page_setting_convert_text[strtolower(substr($row['fieldname'],0,strlen($row['fieldname'])-1))]=$row['convert_text'];
     }
}
$cnt++;
}
echo date("Y-m-d H:i:s");

  // echo "<pre>";
 //print_r($data);
 //  print_r($jitterx);
  //exit;
//$tot_part[0]=0;
//foreach($data as $k=>$v)
//foreach($v as $k1=>$v1)
//if(abs($v1['x'])>0 && abs($v1['y'])>0)
//$tot_part[0]=$tot_part[0]+1;
?>
<? include("../header.php");?>
<link rel="stylesheet" href="/css/protip.min.css">
<link href="../dist/css/jquery.multiselect.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="/assets/fonts/font-awesome/font-awesome.min.css">
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
      .form-control {
   border-radius: 0px;
   -webkit-box-shadow: inherit;
   box-shadow: inherit;
   -webkit-transition:   inherit;
   transition: inherit;
   color: #aaa;
   border: 1px solid #aaa;
   padding: 5px 20px 5px 5px;
   height: auto;
    }
     .protip-skin-default--scheme-pro.protip-container {
	color: #FFF;
	background: #021e3a;
	line-height: 24px;
}
    .protip{cursor:pointer;font-size:12px;}
.protip-skin-default--scheme-pro[data-pt-position="bottom-left"] .protip-arrow,
.protip-skin-default--scheme-pro[data-pt-position="bottom"] .protip-arrow,
.protip-skin-default--scheme-pro[data-pt-position="bottom-right"] .protip-arrow {
	border-bottom-color: #021e3a;
}

.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 24px;
}


.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}


.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 16px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #428bca;
}

input:focus + .slider {
  box-shadow: 0 0 1px #428bca;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}
#loading_layer {
  position: fixed;
  display: none;
  width: 100%;
  height: 96%;
  margin-top:4%;
  top: 0;
  left: 0;
  text-align: center;
  opacity: 0.7;
  background-color: #000;
  z-index: 99;
}
#loading-image {
  position: absolute;
  top: 100px;
  left: 40%;
  z-index: 100;
}
	</style>
<!-- Service Section -->

<section id="services">
<div id="loading_layer">
<img id="loading-image" src="../loading.gif" alt="Loading..." />
</div>
<div class="container text-center" dstyle="width: 100%;">

<div class="row">
<?if(strpos($_SESSION['page_access'],'discoversssssssssssss')!==false){?>
<br />
<div class="alert text-left alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                <h4><i class="icon fa fa-ban"></i> <b>Alert!</b></h4>
                You do not have permission to access this page.. Please consult to Administrator at info@discoveree.io
              </div>
<?}else{?>
                 <!-- /.tab-pane -->
              <div class="tab-pane active">
                 <!-- /.box-header -->

<div class="nav-tabs-custom">

              <div class="table-responsive">
            <div class="tab-content">
            <div class="tab-pane active" style="padding:20px;">
            <div style="clear:both;"></div>

<div id="search" style="display:none;">
<?
if(strpos($_SERVER['REQUEST_URI'],'show')!==false)
$_SERVER['REQUEST_URI']=substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'&show'));

$sql="SELECT iframesrc,savedefault,id FROM user_save_search WHERE username='".$_SESSION['user_name']."' AND pageurl='".addslashes($_SERVER['REQUEST_URI'])."' ";
$saveresult=mysql_query($sql);
while($rowsave=mysql_fetch_array($saveresult)){
if(($rowsave['savedefault']=="1" && $_REQUEST['show']=="") || $_REQUEST['show']==$rowsave['id'])
$getdefaultsearch[0]=$rowsave['iframesrc'];
}
if(mysql_num_rows($saveresult)>0){?><a id="href_mysaved_search" onclick="$('#savedsearchform').load('../extraactions.php?action=showsaved&pageurl=<?=str_replace("&","@and@",$_SERVER['REQUEST_URI']);?>');jQuery('#modal-mysavedsearch').modal('show');" style="cursor:pointer;">[ My Saved Search <i class="fa fa-list" title="My Saved Search" style="font-size: 12px;"></i> ]</a><?}?> <a id="savesearch_div"  onclick="setsavetext();" style="cursor:pointer;display:none;">[ Save This Search <i class="fa fa-save" title="Save" style="font-size: 12px;"></i> ]</a><div style='clear:both;'></div>
<? $cnt=0;
foreach($search as $k=>$v){
$cnt++;
if($v['link_with']=="discoveree_package_cat")
$v['link_with']=str_replace(",","",$v['fieldname']);

if($cnt%5==0){
 //echo "<div style='clear:both;'></div><br />";
}?>
<div class="col-md-3">
<label style="width:100%;"><?=$v['label']?>
<?if($v['tooltip']!="" && $tooltip[$v['tooltip']]!=""){?>
 <a class="protip" data-pt-skin="default" data-pt-gravity="bottom;" data-pt-title="<?=$tooltip[$v['tooltip']];?>"><i class="fa fa-info-circle" aria-hidden="true"></i></a></label>
<?}?>
<br />
<? if(strpos($v['input_type'],"dropdown")!==false){?>
<select operator="" link_condition="<?=$v['link_value']?>" link_with="<?=$v['link_with']?>" multiplyby="<?=$v['multiplyby']?>"  input_type="<?=$v['input_type']?>"  link_fld="<?=$v['fieldname']?>" str="<?=$v['label']?>" <? if(strpos($v['input_type'],"dropdownm")!==false){?>multiple<?}?> class="form-control <? if(strpos($v['input_type'],"dropdownm")!==false){?>dropdownmultiselect<?}?>" id="d<?=$cnt;?>" <? if(strpos($v['input_type'],"dropdownm")===false){?>onchange=""<?}?>>
<? if(strpos($v['input_type'],"dropdownm")===false){?>
<option SELECTED value="">-Select All-</option>
<?}?>
<?
foreach($data as $v1){
foreach($v1 as $v2){
$datavalue=$v2[substr($v['fieldname'],0,strlen($v['fieldname'])-1)];
if($datavalue=="")
$datavalue="NA";
if(strpos($v['link_value'],"max")!==false || strpos($v['link_value'],"least")!==false){

 unset($xv);
 $getv=explode(",",$v['fieldname']);
 foreach($getv as $vs){
 if($vs!="" && abs($v2[$vs])>0){
 $xv[]=$v2[$vs];
 }
 }

if(strpos($v['link_value'],"max")!==false)
$datavalue=(float) max($xv);
else
$datavalue=(float) min($xv);

}
if(!in_array($datavalue,$founddata[substr($v['fieldname'],0,strlen($v['fieldname'])-1)]) && $datavalue!="" && ($v['fieldname']=="manf," || $mac=="" || strpos(strtolower($mac),"'".strtolower($v2['manf'])."'")!==false)){
if(strpos($datavalue,",")!==false){
$exdv=explode(",",$datavalue);
foreach($exdv as $exdva){
if($exdva!="" && !in_array($exdva,$founddata[substr($v['fieldname'],0,strlen($v['fieldname'])-1)])){
$founddata[substr($v['fieldname'],0,strlen($v['fieldname'])-1)][]=$exdva;
?>
<option <? if((strpos($v['input_type'],"dropdownm")!==false && $getdefaultsearch[0]=="") || strpos($getdefaultsearch[0],$exdva)!==false){?>SELECTED<?}?> value="<?=$exdva;?>"><?=checksplit(substr($v['fieldname'],0,strlen($v['fieldname'])-1),$_REQUEST['discoveree_cat_id'],$exdva,$v['value_type']);?></option>
<?
}
}
}
if(strpos($datavalue,",")===false){
$dd_val=checksplit(substr($v['fieldname'],0,strlen($v['fieldname'])-1),$_REQUEST['discoveree_cat_id'],$datavalue,$v['value_type']);
?>
<option <? if((strpos($v['input_type'],"dropdownm")!==false && $getdefaultsearch[0]=="" && ($dfm==0 || $default_manf[$dd_val]==1 || $default_manf_lower[strtolower(str_replace(" ","",$dd_val))]==1 || substr($v['fieldname'],0,strlen($v['fieldname'])-1)!="manf")) || strpos($getdefaultsearch[0],$datavalue)!==false){?><? if($v['fieldname']!="manf," || $mac=="" || strpos(strtolower($mac),"'".strtolower($v2['manf'])."'")!==false){?>SELECTED<?}?><?}?> value="<?=$datavalue;?>"><?=$dd_val;?></option>
<?
}
$founddata[substr($v['fieldname'],0,strlen($v['fieldname'])-1)][]=$datavalue;
}}}?>
</select>
<script>
$("#d<?=$cnt;?>").append($("#d<?=$cnt;?> option").remove().sort(function(a, b) {
    var at = $(a).text(), bt = $(b).text();
    return (at > bt)?1:((at < bt)?-1:0);
}));
</script>
<?}?>

<? if(strpos($v['input_type'],"textboxm")!==false){?>
<input operator=">="  input_type="<?=$v['input_type']?>" multiplyby="<?=$v['multiplyby']?>" link_condition="<?=$v['link_value']?>" link_with="<?=$v['link_with']?>"  link_fld="<?=$v['fieldname']?>" type="text" onkeyup="" class="form-control" id="min<?=$cnt;?>" placeholder="Min" style="float:left;width:47%;text-align:center;" /><div style="margin-top:5px;float:left;width:5%;text-align:center;">-</div><input onkeyup="" multiplyby="<?=$v['multiplyby']?>" input_type="<?=$v['input_type']?>" link_condition="<?=$v['link_value']?>" link_with="<?=$v['link_with']?>"  link_fld="<?=$v['fieldname']?>" operator="<=" type="text" class="form-control" id="max<?=$cnt;?>" placeholder="Max" style="float:left;width:47%;text-align:center;" />
<?}?>

<? if(strpos($v['input_type'],"textboxs")!==false){?>
<input operator="="  input_type="<?=$v['input_type']?>" multiplyby="<?=$v['multiplyby']?>" link_condition="<?=$v['link_value']?>" link_with="<?=$v['link_with']?>"  link_fld="<?=$v['fieldname']?>" type="text" onkeyup="" class="form-control" id="s<?=$cnt;?>" />
<?}?>

</div>
<?
$zcnt=0;
if($cnt%4==0){
 echo "<div style='clear:both;'></div><br />";
 $zcnt=1;
}
}
if($cnt%4==0 && $zcnt==0){
   echo "<div style='clear:both;'><br /></div>";
}
if(count($dropdown_options)>0){
$cnt++;?>
<div class="col-md-3">
<label>View</label>
<br />
<select class="form-control" id="viewd" onchange="">
<option SELECTED value=""><?=strtoupper($dashboard_setting['link_withx']);?>,<?=strtoupper($dashboard_setting['link_withy']);?></option>
<?$dcn=0;
foreach($dropdown_options as $dk=>$dv){$dcn++;?>
<option value="<?=$dcn;?>"><?=stripslashes($dv);?></option>
<?}?>
</select>
</div>
<?}?>
<?
if($dashboard_setting['jitter']==1){
$dashboard_setting['defaultjitter']=2;
$cnt++;?>
<div class="col-md-3">
<label>Jitter Shift (x-axis) <a href="#" class="zebra_tooltips"  title="Enter a jitter value between 0-5 to separate parts along the x-axis.">?</a></label>
<br />
<label class="switch">
  <input type="checkbox" <? if($dashboard_setting['defaultjitter']>0){?>checked<?}?> value="1" id="jitter">
  <span donclick="" class="slider"></span>
</label>

</div>
<?}else{
$dashboard_setting['defaultjitter']=0;
}?>
<? if($dashboard_setting['legend_view_label']!="" && $dashboard_setting['legend_view']!=""){
$cnt++;
if($cnt%4==0){
   echo "<div style='clear:both;'><br /></div>";
}
?>
<div class="col-md-3">
<label><?=$dashboard_setting['legend_view_label'];?></label>
<br />
  <select class="form-control" id="legend_view" style="width:100%;" onchange="location.href='?id=<?=$_REQUEST['id'];?>&discoveree_cat_id=<?=$_REQUEST['discoveree_cat_id'];?>&legend_view='+this.value;">
  <? if(strpos($dashboard_setting['legend_view'],'manf')!==false){?>
   <option value="manf" <?if($_REQUEST['legend_view']=="" || $_REQUEST['legend_view']=="manf"){echo "SELECTED";}?>>Manf</option>
   <?}?>
   <? if(strpos($dashboard_setting['legend_view'],'category')!==false){?>
   <option value="category" <?if($_REQUEST['legend_view']=="category"){echo "SELECTED";}?>>Category</option>
    <?}?>
   <? if(strpos($dashboard_setting['legend_view'],'qualification')!==false){?>
   <option value="qualification" <?if($_REQUEST['legend_view']=="qualification"){echo "SELECTED";}?>>Qualification</option>
    <?}?>
   <? if(strpos($dashboard_setting['legend_view'],'configuration')!==false){?>
   <option value="configuration" <?if($_REQUEST['legend_view']=="configuration"){echo "SELECTED";}?>>Configuration</option>
    <?}?>
   <? if(strpos($dashboard_setting['legend_view'],'package')!==false){?>
   <option value="package" <?if($_REQUEST['legend_view']=="package"){echo "SELECTED";}?>>Package</option>
   <?}?>
    </select>

</div>
<?}?>
<div class="col-md-3" style="text-align:left;"><br />
<a onclick="$('#loading_layer').show();datacall();" class="btn btn-success" style="cursor:pointer;">Search</a>
<a href="dashboard.php?id=<?=$_REQUEST['id'];?>&discoveree_cat_id=<?=$_REQUEST['discoveree_cat_id'];?>&type=<?=$_REQUEST['type'];?>" class="btn btn-danger">Refresh</a></div>
</div>
<style>
#container {
  position:fixed;
  right:-220px;
  top:60px;
  width:250px;
  height:120%;
  z-index:1000;
  overflow:hidden;
  display:none;
}
#block {
  background: #000;
  filter: alpha(opacity=80);
  /* IE */
  -moz-opacity: 0.8;
  /* Mozilla */
  opacity: 0.8;
  /* CSS3 */
  position: absolute;
  top: 0;
  left: 0;
  height: 100%;
  width: 100%;
}
#text {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  color:#FFF;
  text-align:left;
  padding:10px;
}
#comparebox{
 padding:10px;
 padding-right:1px;
 margin-left:10px;
 font-size:12px;
 overflow:auto;
 height:550px;
}
.partno{border-radius: 5px;padding:5px;background-color:#EFEFEF;margin-bottom:10px;display: inline-block; color:#000;}
.comparebutton{text-align:center;padding:10px;}
</style>
<div id="container">
  <div id="block"></div>
  <div id="text">
  <div>
    <span id="sideMenu" class="fa fa-angle-double-right" style="cursor:pointer;"></span>
    <div id="comparebox"></div>
    <br />
    <div class="comparebutton">
    <button type="button" class="btn btn-primary btn-sm compare">Compare</button> <button onclick="$('#comparebox').empty();$('#sideMenu').trigger('click');$('#container').hide();" type="button" class="btn btn-danger btn-sm">Clear All</button>
    </div>
  </div>

  </div>
</div>
<div id="loading"><br /><br />Scanning..</div>
<div id="search_count" style="clear:both;font-weight:bold;"></div>
<div style="width:90%;height:90%;margin: 0 auto;">
<canvas id="canvas"></canvas>
</div>
<? if($dashboard_setting['toggle_y']==1){?>
<button id="toggleScale_y" default="<?=$dashboard_setting['display_typey'];?>" style="display:none;float:left;font-size: 10px;">Toggle Linear/Log Y-Scale</button>
<?}?>
<? if($dashboard_setting['toggle_x']==1){?>
<button id="toggleScale_x" default="<?=$dashboard_setting['display_typex'];?>" style="display:none;margin-left:5px;float:left;font-size: 10px;">Toggle Linear/Log X-Scale</button>
<?}?>
<? if($show_part_status==1){?>
<div id="show_part_status" style="display:none;margin-left:5px;float:left;font-size: 10px;">
 Part Status:

  <select class="form-controls" style="padding: 2px;" onchange="$('#loading_layer').show();location.href='?id=<?=$_REQUEST['id'];?>&discoveree_cat_id=<?=$_REQUEST['discoveree_cat_id'];?><?if($_REQUEST['manf_access']!=""){?>&manf_access=<?=$_REQUEST['manf_access'];?><?}?><?if($_REQUEST['legend_view']!=""){?>&legend_view=<?=$_REQUEST['legend_view'];?><?}?>&show_part_status='+this.value;">
   <option value="1" <?if($_REQUEST['show_part_status']=="" || $_REQUEST['show_part_status']=="1"){echo "SELECTED";}?>>Promotion</option>

   <option value="2" <?if($_REQUEST['show_part_status']=="2"){echo "SELECTED";}?>>Non-Promotion</option>

   <option value="3" <?if($_REQUEST['show_part_status']=="3"){echo "SELECTED";}?>>Both</option>

    </select>
</div>
<?}?>
</div></div>

</div></div>
<?}?>
</div>

</div>
 </section>
 <br /> <br /> <br /> <br /> <br /> <br /> <br />
  <div class="modal modal-danger fade" id="modal-partselection">
          <div class="modal-dialog"  style="width:500px;">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Select Part Numbers For Comparison</h4>
              </div>
              <div style="padding:10px;">
              <input type="search_part" class="search_part_lists" placeholder="Search Part" style="padding:7px;margin-left: 15px;" />
              <div class="modal-body" id="part-selection" style="overflow:auto;height:300px;">

               </div>
               </div>
              <div class="modal-footer">
                <button type="button"  class="btn btn-primary pull-left"  data-dismiss="modal">Close</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
<script src="../admin/Chart.bundle.js"></script>
<script src="../admin/utils.js"></script>
<script src="../plugins/jQuery/jquery.min.js"></script>
<script src="../bootstrap/js/bootstrap.min.js"></script>
<script src="../dist/js/jquery.multiselect.js"></script>
<?include("../footer.php");?>
<script>
var gs = {};
<?
$random_cnt=0;
foreach($data as $k=>$v){
$lk=str_replace(" ","-",$k);
$random_cnt++;
        if($default_style['legendfontcolor']=="random"){
        $legendfontcolor[$lk]=$color[$lk]="#".random_color($random_cnt);
        $bcolor[$lk]="#000000";
        }else{
        $color[$lk]=$default_style['legendfontcolor'];
        $bcolor[$lk]=$default_style['borderColor'];
        $legendfontcolor[$lk]= $default_style['legendfontcolor'];
         }

          $mergeOpacity[$lk]= $default_style['mergeOpacity'];
        $borderWidth[$lk]= $default_style['borderWidth'];
         $pointRadius[$lk]= $default_style['pointRadius'];
          $legendpointer[$lk]= $default_style['legendpointer'];

          $legendfontsize[$lk]= $default_style['legendfontsize'];

          $llk=strtolower(str_replace("-","",$lk));

               if($default_style['legendfontcolor']=="random" && count($random_default_style[$random_cnt])>0){
          $color[$lk]= $random_default_style[$random_cnt]['legendfontcolor'];
        $bcolor[$lk]= $random_default_style[$random_cnt]['borderColor'];
        $mergeOpacity[$lk]= $random_default_style[$random_cnt]['mergeOpacity'];
        $borderWidth[$lk]= $random_default_style[$random_cnt]['borderWidth'];
         $pointRadius[$lk]= $random_default_style[$random_cnt]['pointRadius'];
          $legendpointer[$lk]= $random_default_style[$random_cnt]['legendpointer'];
          $legendfontsize[$lk]= $random_default_style[$random_cnt]['legendfontsize'];
          }

        if(count($indv_default_style[$llk])>0){
        $color[$lk]= $indv_default_style[$llk]['legendfontcolor'];
        $bcolor[$lk]= $indv_default_style[$llk]['borderColor'];
        $mergeOpacity[$lk]= $indv_default_style[$llk]['mergeOpacity'];
        $borderWidth[$lk]= $indv_default_style[$llk]['borderWidth'];
         $pointRadius[$lk]= $indv_default_style[$llk]['pointRadius'];
          $legendpointer[$lk]= $indv_default_style[$llk]['legendpointer'];
          $legendfontsize[$lk]= $indv_default_style[$llk]['legendfontsize'];
        }

        $legendfontcolor[$lk]=$color[$lk]="#".random_color($random_cnt);
        $bcolor[$lk]="#000000";

if(!in_array($llk,$store_js)){
?>
gs['<?=$lk;?>'] = new Array();
gs['<?=$lk;?>']['color']="<?=$color[$lk];?>";
gs['<?=$lk;?>']['bcolor']="<?=$bcolor[$lk];?>";
gs['<?=$lk;?>']['mergeOpacity']="<?=$mergeOpacity[$lk];?>";
gs['<?=$lk;?>']['borderWidth']="<?=$borderWidth[$lk];?>";
gs['<?=$lk;?>']['pointRadius']="<?=$pointRadius[$lk];?>";
gs['<?=$lk;?>']['legendpointer']="<?=$legendpointer[$lk];?>";
gs['<?=$lk;?>']['legendfontsize']="<?=$legendfontsize[$lk];?>";
<?
}
$store_js[]=$llk;
}?>
<? if(strpos($dashboard_setting['fieldx'],"package")!==false){?>
var packages=[];
<?
$di=0;
            foreach($data as $k=>$v){
            foreach($v as $k1=>$v1){
if(!in_array($v1['x'],$pkgs)){
$di++;?>
packages[<?=$di;?>]='<?=$v1['x'];?>';
<?$pkgtmp[$v1['x']]=$di;}?><? $pkgs[]=$v1['x'];}}?>
<?
foreach($data as $k=>$v){
            foreach($v as $k1=>$v1){
            $data[$k][$k1]['x']=$pkgtmp[$v1['x']];
            }}
 //echo "<pre>";
 //print_r($data['Toshiba']);
// exit;
}?>

var color = Chart.helpers.color;
	var scatterChartData = {
		datasets: [
        <?
        $tot_part=0;
        $legend_count=0;
        foreach($data as $k=>$v){
        if(count($v[key($v)])>1 && ($dashboard_setting['legend']!="manf" || ($dashboard_setting['legend']=="manf" && ($dfm==0 || $default_manf[$k]==1 || $default_manf_lower[strtolower(str_replace(" ","",$k))]==1)))){
        $legend_count++;
        $lk=str_replace(" ","-",$k);
        ?>
        {
  	borderColor: '<?=$bcolor[$lk];?>',
			backgroundColor: '<?=$color[$lk];?>',
			mergeOpacity: '<?=$mergeOpacity[$lk];?>',
			borderWidth: '<?=$borderWidth[$lk];?>',
			pointRadius: '<?=$pointRadius[$lk];?>',
			pointStyle: '<?=$legendpointer[$lk];?>',

			label: '<?=$k;?>',
			data: [
			<? foreach($v as $v1){
            if($v1['x']!="" && $v1['y']!="" && $v1['x']!="0" && $v1['y']!="0" && $v1['x']!="null" && $v1['y']!="null"){
            $tot_part++;
            $v1['plotx']=$v1['x'];
            $v1['ploty']=$v1['y'];
            $legend_count=$jitterx[$k."@".$v1['x']];
            $tot_count=getcount($jitterx,$v1['x']);

            if($legend_count>1 && $tot_count==2)
            $v1['plotx']=$v1['x']+pow(-($dashboard_setting['defaultjitter']),$legend_count)*($legend_count-ceil($legend_count/2))*1;
            else if($legend_count>1 && $tot_count>2)
            $v1['plotx']=$v1['x']+pow(-($dashboard_setting['defaultjitter']),$legend_count)*($legend_count-ceil($legend_count/2))*(2/($tot_count-1));
             if(strpos($dashboard_setting['fieldx'],"package")!==false)
             $v1['x']=strtoupper($v1['package']);
            ?>
            {
				x: <?=$v1['plotx']?>,
				y: <?=$v1['ploty']?>,

				orgx: '<?=$v1['x']?>',
				orgy: '<?=$v1['y']?>',
				partno: '<?=$v1['partno']?>',
				manf: '<?=$k?>',
				manf_org: '<?=$v1['manf_org']?>',
				discoveree_cat_id: '<?=$v1['discoveree_cat_id']?>',
			},
			<?}}?>
            ]
		},
       <?}}?>
        ]
	};
    var lastHoveredIndex=0;
    var  lastoverobject="";
     var first_time=1;
	window.onload = function() {
	$('#search,#toggleScale_x,#toggleScale_y,#show_part_status').show();
	$('#loading').hide();
	total_searches();
		var ctx = document.getElementById('canvas').getContext('2d');
		window.myScatter = Chart.Scatter(ctx, {
			data: scatterChartData,
			options: {

			tooltips: {enabled: false},

               animation: {
   onComplete: function(e) {
   $('#loading_layer').hide();

   }
},
			layout: {
            padding: {
                left: 5,
                right: 5,
                top: 5,
                bottom: 5
              }
        },

hover: {
mode: null,
      onHover: function(e) {
         var point = this.getElementAtEvent(e);
         if (point.length){
          e.target.style.cursor = 'pointer';
          }else{
          e.target.style.cursor = 'default';
          lastoverobject="";
          }
      }
   },

        tooltips: {
          mode: 'point',
        callbacks: {

            label: function(tooltipItem, data) {
            var label = data.datasets[tooltipItem.datasetIndex].label

             lastHoveredIndex = tooltipItem;
              orgx=data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index].orgx;
              orgy=data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index].orgy;

             lastoverobject=lastoverobject+"-"+tooltipItem.datasetIndex+"-"+tooltipItem.index+",";
             return data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index].partno.toUpperCase()+' ['+label+'] - ('+orgx+','+orgy+')';
            }
          }

        },
         legend: {

            labels: {
                fontSize: <?=$legendfontsize[key($legendfontsize)];?>,
            }
        },
				title: {
					display: true,
					text: '<?=$dashboard_setting['label']?> [Total: <?=$tot_part;?>]',
					fontSize: <?=$default_style['headerfontsize']?>,
					fontColor: '<?=$default_style['headerfontcolor']?>',
				},
				scales: {
    	xAxes: [{
					ticks: {
                fontSize: <?=$default_style['xfontsize']?>,
                fontColor: '<?=$default_style['xfontcolor']?>',
                autoSkip: false,
                <? if(strpos($dashboard_setting['fieldx'],"package")!==false){?>
                stepSize : 1,
                callback: function(value, index, values) {
                //alert(value+":"+index+":"+values);
                        return packages[value];
                     }
                     <?}?>
            },
      type: '<?=$dashboard_setting['display_typex']?>',
						scaleLabel: {
							labelString: '<?=$dashboard_setting['labelx']?>',
							display: true,
							fontSize: <?=$default_style['xfontsize']?>,
							fontColor: '<?=$default_style['xfontcolor']?>',
						}
					}],
					yAxes: [{
					ticks: {
					<? if(strpos($dashboard_setting['fieldx'],"package")===false){?>
					suggestedMin: 0,
					<?}?>
                fontSize: <?=$default_style['yfontsize']?>,
                fontColor: '<?=$default_style['yfontcolor']?>',
            },
						type: '<?=$dashboard_setting['display_typey']?>',
						scaleLabel: {
							labelString: '<?=$dashboard_setting['labely']?>',
							display: true,
							fontSize: <?=$default_style['yfontsize']?>,
							fontColor: '<?=$default_style['yfontcolor']?>',
						}
					}]
				}
			}
		});
		 if(first_time==1){
   first_time=0;
   save_load();
   }
	};

     var timeOne=0;
	document.getElementById("canvas").onclick = function(evt){
    var activePoints = myScatter.getElementsAtEvent(evt);
   if (activePoints.length > 0)
    {
           if (new Date().getTime() - timeOne < 500){
    myScatter.data.datasets[lastHoveredIndex.datasetIndex].data.splice(lastHoveredIndex.index,1);
    myScatter.update();
    }else{
    totrec=0;
         x=lastHoveredIndex.xLabel;
         y=lastHoveredIndex.yLabel;
         obj=myScatter.data.datasets;
         $('#part-selection').empty();
         for(var key in obj) {  //for-1
         obj1=obj[key].data;
         for(var key1 in obj1) { //for-2
          //alert(x+":"+y+":"+obj1[key1].x+":"+obj1[key1].y+":::"+key+"=="+key1+"+++"+lastoverobject);
          if(lastoverobject.indexOf("-"+key+"-")>=0 && lastoverobject.indexOf("-"+key1+",")>=0 && Math.round(obj1[key1].x)==Math.round(x)){ //if
          totrec++;
          }}
           for(var key1 in obj1) { //for-2
          //alert(x+":"+y+":"+obj1[key1].x+":"+obj1[key1].y+":::"+key+"=="+key1+"+++"+lastoverobject);
          if(lastoverobject.indexOf("-"+key+"-")>=0 && lastoverobject.indexOf("-"+key1+",")>=0 && Math.round(obj1[key1].x)==Math.round(x)){ //if
         // if(obj1[key1].x==x && obj1[key1].y==y){ //if
           manf=obj1[key1].manf;
           partno=obj1[key1].partno.toUpperCase();
           discoveree_cat_id=obj1[key1].discoveree_cat_id;
           manf_org=obj1[key1].manf_org;
           <?if($_REQUEST['type']!="development"){?>
           if(totrec>1){
            str='<div class="partl"><input cdlass="chb" partno="'+partno+'" manf="'+manf_org+'" type="hidden" value="'+partno+'@'+manf_org+'@'+discoveree_cat_id+'" />'+partno+' ['+manf+'] <a class="chb"  partno="'+partno+'" manf="'+manf_org+'" value="'+partno+'@'+manf_org+'@'+discoveree_cat_id+'" style="cursor:pointer;">[add]</a></div>';
           }else{
           str='<div class="partno" onclick="$(this).remove();if($(\'.partno\').length==0){$(\'#sideMenu\').trigger(\'click\');$(\'#container\').hide();}" style="cursor:pointer;margin-right:5px;"><input type="hidden" class="compare_part" value="'+partno+'@'+manf_org+'@'+discoveree_cat_id+'" />'+partno+' ['+manf+'] <font style="color:#FF0000;">X</font></div>';
           }
           <?}else{?>
           //str='<div class="partno" style="margin-right:5px;"><input type="hidden" class="compare_part" value="'+partno+'@'+manf_org+'@'+discoveree_cat_id+'" /><a href="../user/data_fixing.php?part='+partno+'&manf='+manf.replace(' ','').toLowerCase()+'&discoveree_cat_id='+discoveree_cat_id+'" target="_blank">'+partno+'</a> ['+manf+'] <a href="#" onclick="$(this).parent().remove();if($(\'.partno\').length==0){$(\'#sideMenu\').trigger(\'click\');$(\'#container\').hide();}" style="color:#FF0000;">X</a></div>';
           <?}?>
           if(totrec>1){
           jQuery('#modal-partselection').modal('show');
           $('#part-selection').append(str);
           }else{
            $('#comparebox').append(str);
           }
           //

          } //if
         }  //for-2
         }  //for-1
         if(totrec==1){
      var position = $('#container').css('right');
      if(position!="0px")
      $('#sideMenu').trigger('click');
      }
      lastoverobject="";
     }
     timeOne = new Date().getTime();
     }
};
$(document).on('click','.chb',function(){

     str='<div class="partno" onclick="$(this).remove();if($(\'.partno\').length==0){$(\'#sideMenu\').trigger(\'click\');$(\'#container\').hide();}" style="cursor:pointer;margin-right:5px;"><input type="hidden" class="compare_part" value="'+$(this).attr('value')+'" />'+$(this).attr('partno')+' ['+$(this).attr('manf')+'] <font style="color:#FF0000;">X</font></div>';
     $('#comparebox').append(str);
     $(this).closest('div').remove();
     var position = $('#container').css('right');
     if(position!="0px")
     $('#sideMenu').trigger('click');
});
$('.search_part_lists').keyup(function(){
 $('.partl').hide();
   var txt = $(this).val().toUpperCase();
   $('.partl:contains("'+txt+'")').show();
})
</script>
<script>
 $(".dropdownmultiselect").each(function(){
if($(this).attr('id')=="d1"){
 $('select#'+$(this).attr('id')).multiselect({
    columns: 2,
    search: true,
    placeholder: 'Select '+$(this).attr('str'),
    selectedOptions: ' '+$(this).attr('str'),
    selectAll     : true,
    closePopupOnSelect : true,
    maxPlaceholderOpts: 2,
    onLoad: function(){tcount = $("#d1 :selected").length; $('#d1 a.ms-selectall').click(function(){clickdropdown=1;});},
    onOptionClick: function(){$('.btn-success').attr('disabled',true);clickdropdown=1;},
    onControlClose: function(event) {
    values='';
      $('#d1 option:selected').each(function() {
    values=values+$(this).val()+',';
});
if($("#d1 :selected").length!=tcount){
var pathname = window.location.pathname;
$('#loading_layer').show();
location.href=pathname+'?id=<?=$_REQUEST['id'];?>&discoveree_cat_id=<?=$_REQUEST['discoveree_cat_id'];?><?if($_REQUEST['show_part_status']!=""){?>&show_part_status=<?=$_REQUEST['show_part_status'];?><?}?>&manf_access='+values;
return false;
}
$('.btn-success').attr('disabled',false);
     },
});
}else{
 $('select#'+$(this).attr('id')).multiselect({
    columns: 2,
    search: true,
    placeholder: 'Select '+$(this).attr('str'),
    selectedOptions: ' '+$(this).attr('str'),
    selectAll     : true,
    closePopupOnSelect : true,
    maxPlaceholderOpts: 2,
    onControlClose: function(event) {},
});
 }
 })

 function total_searches(){

 tot_searches=0;
 $(".form-control").each(function(){
  if($(this).val()!="" && $(this).attr('id')!="viewd" && $(this).attr('id')!="jitter" && $(this).attr('id')!="legend_view"){
  tot_searches++;
  }
 });

 //$('#search_count').html('<br />Total Searches: '+tot_searches).show();

 }

 $('#jitter').change(function(){

 });


 function datacall(){
    $('#loading_layer').show();
  $('#savesearch_div').show();
 total_searches();
 viewdclick=$('#viewd').val();
 //***added**/
 jittershift=0;
 if ($('#jitter').is(":checked"))
 jittershift=2;
 //***added**/

// jittershift=$('#jitter').val();

  if(viewdclick==undefined)
 viewdclick="";
 if(jittershift==undefined)
 jittershift=0;


 var data = {};
 var c=0;
 var tempArray =   <?php echo json_encode($data); ?>;

$.each(tempArray, function(key, value) {

    $.each(value, function(key1, value1) {

  var notfound=0;
  $(".form-control").each(function(){ // start form control
  if($(this).attr('id')!="viewd" && $(this).attr('id')!="jitter"  && $(this).attr('id')!="legend_view"){

  var selection_value_tmp=$(this).val().toString();


  var selection_value=new Array();
  if(selection_value_tmp.indexOf(',') !== -1 ){
  var selection_value_array = selection_value_tmp.split(",");
  var mct=0;

  $.each(selection_value_array, function(mk, mv) {
  if(mv!=""){
  selection_value[mct]=mv;
  mct++;
  }
  });
  }else{
  selection_value[0]=selection_value_tmp;
  }




  link_with=$(this).attr('link_with');
  link_fld=$(this).attr('link_fld').toString();
  link_condition=$(this).attr('link_condition');
  operator=$(this).attr('operator');
  input_type=$(this).attr('input_type');
  var mmvalue=0;
  if(link_condition=="least" || link_condition=="maximum"){
  var myarr = link_fld.split(",");
  var va=new Array();
  var vc=0;
  $.each(myarr, function(mk, mv) {
  if(Math.abs(value1[mv])>0){
  va[vc]=value1[mv];
  vc++;
  }
  });
  if(link_condition=="least")
  mmvalue= Math.min.apply(Math, va);
  else
  mmvalue= Math.max.apply(Math, va);
  }
  if(link_condition=="exact"){
   var myarr = link_fld.split(",");
   mmvalue=value1[myarr[0]];
  }
  var keep=0;

 // if(operator=="<=" || operator==">="){
   // alert(input_type+":"+link_fld+"::"+link_condition+"::"+operator+"***"+parseFloat(selection_value[0])+"="+parseFloat(mmvalue));
 //  alert(parseFloat(selection_value[0])+"=="+link_with+"==="+parseFloat(mmvalue));
 // }

//   if(selection_value[0]=="NA")
//   selection_value[0]="";
  if(value1[link_with]==undefined)
  value1[link_with]="";

    if(link_condition=="exact" && input_type=="dropdownm" ){
     // alert(selection_value+"::"+value1[link_with]+":"+selection_value.toString().indexOf(value1[link_with].toString()));
}
   if(link_condition=="and" && operator==">=" && Math.abs(selection_value[0])>=0){
    var myarrfld = link_fld.split(",");
    keep=1;
     $.each(myarrfld, function(mk, mv) {
     if(mv!="" && Math.abs(value1[mv])>0 && parseFloat(selection_value[0])>=parseFloat(value1[mv])){
     keep=0;
     return false;
    }
    });

  }else if(link_condition=="and" && operator=="<=" && Math.abs(selection_value[0])>=0){
    var myarrfld = link_fld.split(",");
    keep=1;
     $.each(myarrfld, function(mk, mv) {
     if(mv!="" && Math.abs(value1[mv])>0 && parseFloat(selection_value[0])<=parseFloat(value1[mv])){
     keep=0;
     return false;
    }
    });

  }else if(link_condition=="exact" && input_type=="dropdownm" && (value1[link_with].toString().indexOf(',') !== -1 || value1[link_with]=="")){
  var myarrfldsss = value1[link_with].toString().split(",");
  sss=","+selection_value+",";
  zz=0;
  $.each(myarrfldsss, function(mks, mvs) {
  zz++;
  if(zz==1){
   //alert(sss+":"+mvs+":"+sss.toString().indexOf(","+mvs.toString()+","));
  if((sss.toString().indexOf(","+mvs.toString()+",") !== -1 && mvs!="") || (sss.toString().indexOf(",NA,") !== -1 && mvs=="")){
     keep=1;
  return false;
  } }
  });
  }else if(link_condition=="exact" && (input_type=="dropdownm" || input_type=="dropdowns") && selection_value.toString().indexOf(value1[link_with].toString()) !== -1  && value1[link_with].toString().indexOf(',') === -1 ){
    selection_value=","+selection_value+",";
  vvv=","+value1[link_with].toString()+",";
 if(selection_value.toString().indexOf(vvv) !== -1)
 keep=1;
  }else  if(link_condition=="exact" && (input_type=="textboxs") && (parseFloat(selection_value[0])==parseFloat(mmvalue) || (selection_value[0])==(mmvalue) ) ){
  keep=1;
  }else if((link_condition=="exact" || link_condition=="least" || link_condition=="maximum") && operator=="<=" && parseFloat(mmvalue)<=parseFloat(selection_value[0])){
   keep=1;
  }else if((link_condition=="exact" || link_condition=="least" || link_condition=="maximum") && operator==">=" && parseFloat(mmvalue)>=parseFloat(selection_value[0])){
  keep=1;
  }else if((link_condition=="least" || link_condition=="maximum") && (input_type=="dropdowns" || input_type=="textboxs" || input_type=="dropdownm")  && selection_value.indexOf(mmvalue.toString()) !== -1){
   keep=1;
  }
   if(input_type=="dropdownm" && selection_value_tmp==""){
   keep=0;
   selection_value_tmp="notfound";
   }

   //alert(keep+":"+link_with);

  if(keep==0 && selection_value_tmp!="")
  notfound=1;
  }
  }) // end form control
    if(notfound==0 && value1['x'+viewdclick]!="" && value1['y'+viewdclick]!="" && value1['x'+viewdclick]!="0" && value1['y'+viewdclick]!="0" && value1['x'+viewdclick]!=null && value1['y'+viewdclick]!=null && value1['x'+viewdclick]!=undefined && value1['y'+viewdclick]!=undefined){

    if (!data.hasOwnProperty(key)){
    data[key]={};
    }

    data[key][c]=new Array();
    data[key][c]['partno']=value1['partno'];
    data[key][c]['x']=value1['x'+viewdclick];
    data[key][c]['y']=value1['y'+viewdclick];

    data[key][c]['manf_org']=value1['manf_org'+viewdclick];
    data[key][c]['discoveree_cat_id']=value1['discoveree_cat_id'+viewdclick];

    c++;
    }

    });
});

var totparts=0;
var jitterx={};


$.each(data, function(key, value) {
//totparts=totparts+Object.keys(value).length;
$.each(value, function(key1, value1) {
  if(value1['x']!="" && value1['y']!="" && value1['x']!="0" && value1['y']!="0" && value1['x']!=null && value1['y']!=null && value1['x']!=undefined && value1['y']!=undefined){

  if(jitterx[key+'@'+value1['x']] == undefined){
   jitterx[key+"@"+value1['x']]=getcount(jitterx,value1['x'])+1;
  }
 totparts++;

}
  });
 });
 //return false;

totlegend=Object.keys(data).length;
legend_count=0;

 window.myScatter.data.datasets.splice(0);
 window.myScatter.update();
 z=0;
 $.each(data, function(key, value) {
//color=getRandomColor();
lk=key.replace(' ','-');
legend_count++;

 var newDataset = {

 	borderColor: gs[lk]['bcolor'],
			backgroundColor: gs[lk]['color'],
			mergeOpacity: gs[lk]['mergeOpacity'],
			borderWidth: gs[lk]['borderWidth'],
			pointRadius: gs[lk]['pointRadius'],
			pointStyle: gs[lk]['legendpointer'],

			label: key,

};



window.myScatter.data.datasets.push(newDataset);
window.myScatter.update();
$.each(value, function(key1, value1) {
plotx=value1['x'];
ploty=value1['y'];

//jittershift=1;

legend_count=jitterx[key+"@"+value1['x']];
tot_count=getcount(jitterx,value1['x']);

if(legend_count>1 && jittershift>0 && tot_count==2)
plotx=parseFloat(value1['x'])+parseFloat(Math.pow(-(jittershift),legend_count)*(legend_count-Math.ceil(legend_count/2))*1);
else if(legend_count>1 && jittershift>0 && tot_count>2)
plotx=parseFloat(value1['x'])+parseFloat(Math.pow(-(jittershift),legend_count)*(legend_count-Math.ceil(legend_count/2))*(2/(tot_count-1)));

 // alert(legend_count+":"+plotx+":"+totlegend+":"+value1['manf_org']);

window.myScatter.data.datasets[z].data.push({
       x: plotx,
       y: ploty,
       orgx: value1['x'],
       orgy: value1['y'],
       partno: value1['partno'],
       manf_org: value1['manf_org'],
       discoveree_cat_id: value1['discoveree_cat_id'],
       manf: key
  });
});
window.myScatter.update();
 z++;
 });

 window.myScatter.options.title.text = "<?=$dashboard_setting['label']?> [Total: "+totparts+"]";
window.myScatter.update();
 }

 	$('#toggleScale_x').click(function(){
         type = $(this).attr("default");

		 type =  type === 'linear' ? 'logarithmic' : 'linear';
         $(this).attr("default",type);
   if(type=="linear"){
   window.myScatter.options.scales.xAxes[0] = {
				type: type,

				ticks: {
                fontSize: <?=$default_style['xfontsize']?>,
                fontColor: '<?=$default_style['xfontcolor']?>',
            },

						scaleLabel: {
							labelString: '<?=$dashboard_setting['labelx']?>',
							display: true,
							fontSize: <?=$default_style['xfontsize']?>,
							fontColor: '<?=$default_style['xfontcolor']?>',
						}

			};
   }else{
   window.myScatter.options.scales.xAxes[0] = {
				type: type,
				ticks: {
                fontSize: <?=$default_style['xfontsize']?>,
                fontColor: '<?=$default_style['xfontcolor']?>',
            },

						scaleLabel: {
							labelString: '<?=$dashboard_setting['labelx']?>',
							display: true,
							fontSize: <?=$default_style['xfontsize']?>,
							fontColor: '<?=$default_style['xfontcolor']?>',
						}
			};
     }
			window.myScatter.update();
		});

			$('#toggleScale_y').click(function(){
         type = $(this).attr("default");

		 type =  type === 'linear' ? 'logarithmic' : 'linear';
         $(this).attr("default",type);
   if(type=="linear"){
   window.myScatter.options.scales.yAxes[0] = {
				type: type,
				ticks: {
                fontSize: <?=$default_style['yfontsize']?>,
                fontColor: '<?=$default_style['yfontcolor']?>',
                	<? if(strpos($dashboard_setting['fieldx'],"package")===false){?>
					suggestedMin: 0,
					<?}?>
            },

						scaleLabel: {
							labelString: '<?=$dashboard_setting['labely']?>',
							display: true,
							fontSize: <?=$default_style['yfontsize']?>,
							fontColor: '<?=$default_style['yfontcolor']?>',
						}
			};
   }else{
   window.myScatter.options.scales.yAxes[0] = {
				type: type,
				ticks: {
                fontSize: <?=$default_style['yfontsize']?>,
                fontColor: '<?=$default_style['yfontcolor']?>',
                	<? if(strpos($dashboard_setting['fieldx'],"package")===false){?>
					suggestedMin: 0,
					<?}?>
            },

						scaleLabel: {
							labelString: '<?=$dashboard_setting['labely']?>',
							display: true,
							fontSize: <?=$default_style['yfontsize']?>,
							fontColor: '<?=$default_style['yfontcolor']?>',
						}
			};
     }
			window.myScatter.update();
		});


 function getRandomColor() {
  var letters = '0123456789ABCDEF';
  var color = '#';
  for (var i = 0; i < 6; i++) {
    color += letters[Math.floor(Math.random() * 16)];
  }
  return color;
}

var toggle = false;

$('#sideMenu').click(function() {
        toggle = !toggle;

        if(toggle){
            $('#container').show().animate({right: 0});
             $(this).removeClass('fa-angle-double-left').addClass('fa-angle-double-right');
        }
        else{
            $('#container').show().animate({right: -220});
            $(this).removeClass('fa-angle-double-right').addClass('fa-angle-double-left');
        }

    });


 $('.compare').click(function() {
url="";
tot=0;
$(".compare_part").each(function(){
if(url.indexOf($(this).val()+'^') == -1){
tot++;
       url=url+$(this).val()+'^';
}
});

if(tot>10){
//alert("Please select upto 10 parts for the compare.");
//return false;
}
let a= document.createElement('a');
a.target= '_blank';
a.href= 'compare.php?url='+url.toLowerCase();
a.click();

});

function getcount(j,x){
cj=0;
$.each(j, function(keyj, valuej) {
kx=keyj.substr(keyj.indexOf("@")+1,keyj.length);
if(x==kx)
cj++;
})
return cj;
}
/////////////////save search-start
function setsavetext(){
inputstr="";
u="&";
for(a=1;a<=20;a++){
str=$('#d'+a).attr('link_with');
dt=$('#d'+a).attr('input_type');
l=$('#d'+a).prevAll('label').text();

strmin=$('#min'+a).attr('link_with');
strmax=$('#max'+a).attr('link_with');

if(str!="" && str!=undefined){
if(dt=="dropdownm")
inputstr=inputstr+l+": "+$('#d'+a+' :selected').length+", ";
else if($('#d'+a).val()!="" && $('#d'+a).val()!=undefined)
inputstr=inputstr+l+": "+$('#d'+a).val()+", ";

if($('#d'+a).val()!="" && $('#d'+a).val()!=undefined)
u=u+'d'+a+'='+$('#d'+a).val()+'&';
}else if(strmin!="" && strmin!=undefined && strmax!="" && strmax!=undefined){

if($('#min'+a).val()!="" && $('#min'+a).val()!=undefined){
inputstr=inputstr+strmin.toUpperCase()+": "+$('#min'+a).val()+"(min), ";
u=u+'min'+a+'='+$('#min'+a).val()+'&';
}
if($('#max'+a).val()!="" && $('#max'+a).val()!=undefined){
inputstr=inputstr+strmax.toUpperCase()+": "+$('#max'+a).val()+"(max), ";
u=u+'max'+a+'='+$('#max'+a).val()+'&';
}
}

}
if(inputstr!="" && inputstr!=undefined)
inputstr=inputstr.substring(0,inputstr.length-2);

jQuery('#iframesrc').val(u);

jQuery('#savesearchtitle').val(inputstr);
jQuery('#modal-savesearch').modal('show');
}
/////////////////save search-end

//////////////// saved result-start
function save_load(){
//alert("<? echo date("Y-m-d H:i:s");?>");
<?if($getdefaultsearch[0]!=""){
$exstr=explode("&",$getdefaultsearch[0]);
foreach($exstr as $e){
$key=trim(substr($e,0,strpos($e,'=')));
$v=substr($e,strpos($e,'=')+1,strlen($e));
$v=str_replace("undefined","",$v);
$v=str_replace("%20"," ",$v);
if($key!=""){
$vstr=explode(",",$v);?>

if($('#<?=$key;?>').attr("input_type")!="dropdownm"){
<?
foreach($vstr as $v11){
$v11=trim($v11);
if($v11!=""){
?>
$('#<?=$key;?>').val('<?=$v11;?>');
<?
}} ?>
}
<?
}}
?>
$('.btn-success').trigger('click');
<?}?>
<?if($mac!=""){?>
$('.btn-success').trigger('click');
<?}?>
}
//////////////// saved result-end
</script>
<script src="/js/protip.min.js"></script>
<script>
$(document).ready(function(){
    $.protip();
});
</script>
<?


function getcount($j,$x){
$c=0;
foreach($j as $k=>$v){
if($x==substr($k,strpos($k,"@")+1,strlen($k)))
$c++;
}
 return $c;
}

function random_color_part() {
    return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
}

function random_color($clr) {
  if($clr==1)
return "38925e"; //063a51
else if($clr==2)
return "225953"; //c13119
else if($clr==3)
return "263049"; //f36f14
else if($clr==4)
return "f92100";  //ebcb39
else if($clr==5)
return "f60240";  //a3b969
else if($clr==6)
return "f5d601";  //0d96bc
else if($clr==7)
return "12cd86";  //0d96bc
else if($clr==8)
return "096ea2";  //0d96bc
else if($clr==9)
return "f7ae0a";  //0d96bc
else if($clr==10)
return "f62619";  //0d96bc
else if($clr==11)
return "860042";  //0d96bc
else if($clr==12)
return "332771";  //0d96bc
else if($clr==13)
return "f51456";  //0d96bc
else if($clr==14)
return "b0e7c7";  //0d96bc
else if($clr==15)
return "f8b1c1";  //0d96bc
else if($clr==16)
return "248888";  //0d96bc
else if($clr==17)
return "f2d775";  //0d96bc
else if($clr==18)
return "11977f";  //0d96bc
else if($clr==19)
return "688356";  //0d96bc
else if($clr==20)
return "c5492f";  //0d96bc
else if($clr==21)
return "f8947d";  //0d96bc
else if($clr==22)
return "f7e4c4";  //0d96bc
else if($clr==23)
return "32b8b8";  //0d96bc
else if($clr==24)
return "ecefee";  //0d96bc
else if($clr==25)
return "11977f";  //0d96bc
else if($clr==26)
return "d6b572";  //0d96bc
else if($clr==27)
return "b1b336";  //0d96bc
else if($clr==28)
return "bef7e4";  //0d96bc
else if($clr==29)
return "446d89";  //0d96bc
else if($clr==30)
return "68618b";  //0d96bc
else if($clr==31)
return "38925e"; //063a51
else if($clr==32)
return "225953"; //c13119
else if($clr==33)
return "263049"; //f36f14
else if($clr==34)
return "f92100";  //ebcb39
else if($clr==35)
return "f60240";  //a3b969
else if($clr==36)
return "f5d601";  //0d96bc
else if($clr==37)
return "12cd86";  //0d96bc
else if($clr==38)
return "096ea2";  //0d96bc
else if($clr==39)
return "f7ae0a";  //0d96bc
else if($clr==40)
return "f62619";  //0d96bc
else if($clr==41)
return "860042";  //0d96bc
else if($clr==42)
return "332771";  //0d96bc
else if($clr==43)
return "f51456";  //0d96bc
else if($clr==44)
return "b0e7c7";  //0d96bc
else if($clr==45)
return "f8b1c1";  //0d96bc
else if($clr==46)
return "248888";  //0d96bc
else if($clr==47)
return "f2d775";  //0d96bc
else if($clr==48)
return "11977f";  //0d96bc
else if($clr==49)
return "688356";  //0d96bc
else if($clr==50)
return "c5492f";  //0d96bc
else if($clr==51)
return "f8947d";  //0d96bc
else if($clr==52)
return "f7e4c4";  //0d96bc
else if($clr==53)
return "32b8b8";  //0d96bc
else if($clr==54)
return "ecefee";  //0d96bc
else if($clr==55)
return "11977f";  //0d96bc
else if($clr==56)
return "d6b572";  //0d96bc
else if($clr==57)
return "b1b336";  //0d96bc
else if($clr==58)
return "bef7e4";  //0d96bc
else if($clr==59)
return "446d89";  //0d96bc
else if($clr==60)
return "68618b";  //0d96bc
else
return str_pad( dechex( mt_rand( 0, 255 ) ), 6, '0', STR_PAD_LEFT);
}
function getv($s){
if(strpos($s,".")!==false && abs($s)>0)
return rtrim($s,"0");
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

return $t;
}

function checksplit($f,$k,$s,$vtt){
global $manf_array;

foreach($manf_array as $vc){
if(strtolower($vc['org_manf'])==strtolower($s)){
return $vc['org_manf'];
break;
}
}


global $value_array;
if($value_array[$f][strtolower($s)]['org_value']!="")
return $value_array[$f][strtolower($s)]['org_value'];

if(strpos(strtolower($vtt),'condition')!==false || strpos(strtolower($s),'non-auto')!==false)
return $s;

if(find_replace_tc($s)!="" && find_replace_tc($s)!=$s && (substr($f,strlen($f)-2,2)=="tc" || substr($f,strlen($f)-3,2)=="tc"))
return find_replace_tc($s);

if(strtolower($s)=="na")
return "NA";

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
?>

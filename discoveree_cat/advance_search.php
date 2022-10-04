<?php include "../includes/config.inc.php";?>
<?php

if(($_SESSION['user_name']!="dnp1976" && $_SESSION['user_name']!="srai") && $_SESSION['test_user_id']==""){
if(strpos($_SESSION['opage_access'],"discoveree_cat_id=".$_REQUEST['discoveree_cat_id'])===false && strpos($_SESSION['opage_access'],"id=".$_REQUEST['id'])===false){

$_SESSION['lasturl'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] .  $_SERVER['REQUEST_URI'];

header("Location: /");
exit;
}
}

if($_SESSION['expired_user']==1 && $_COOKIE['expired_user_advance']==1){
header("Location: https://www.discoveree.io/request_upgrade.php");
exit;
}
if($_SESSION['expired_user']==1){
$expire=time()+60*60*24;//however long you want
setcookie('expired_user_advance', 1, $expire,'/');
}

ini_set('memory_limit', '-1');

if($_REQUEST['id']==1){
$diode_cond=0;
$_REQUEST['discoveree_cat_id']=27;
$dss=mysql_query("SELECT * FROM srai_others.dashboard_category_order WHERE  NOT catname LIKE '%zener%' AND  NOT catname LIKE '%tvs%' AND dashboard_setting_id=126 ORDER BY catorder");
while($drow=mysql_fetch_array($dss)){
$othercat[trim($drow['discoveree_cat_id'])]=ucwords(strtolower(trim(substr(trim($drow['catname']),strpos(trim($drow['catname']),'-')+1,strlen(trim($drow['catname']))))));
$diode_cond=1;
}
}

$getmanf_array=mysql_query("SELECT * FROM srai_others.manage_manf_name");
while($bh=mysql_fetch_array($getmanf_array)){
if($bh['manf_array']!="")
$manf_array=json_decode($bh['manf_array'],true);
if($bh['value_array']!="" && $bh['discoveree_cat_id']==$_REQUEST['discoveree_cat_id'])
$value_array=json_decode($bh['value_array'],true);
}


foreach($manf_array as $k=>$v)
$rr_manf[strtolower($v['source_manf'])]=$v['org_manf'];


//mysql_query("INSERT INTO users_landing_page_log_time SET log_id=".$_SESSION['log_id'].",page='".str_replace("/","",$_SERVER['SCRIPT_URL'])."',username='".$_SESSION['user_name']."',action='start'");

if($_REQUEST['discoveree_cat_id']!=""){
$manf_search_words=(mysql_query("SELECT title,save_word,no_word FROM srai_others.manf_search_words WHERE discoveree_cat_id=".$_REQUEST['discoveree_cat_id']." GROUP BY title,save_word,no_word ORDER BY save_word,no_word"));
while($mswrow=mysql_fetch_array($manf_search_words)){
$msw[$mswrow[0]]=$msw[$mswrow[0]].trim($mswrow[1])."||".trim($mswrow[2]);
}
}

$sql="SELECT * FROM srai_others.product_page_setting WHERE discoveree_cat_id in (".$_REQUEST['discoveree_cat_id'].") ORDER BY priority";
$product_setting=(mysql_query($sql));
$cnt=0;
while($row = mysql_fetch_assoc($product_setting)){
foreach($row as $key => $value)
{
    if($key!="id" && $key!="discoveree_cat_id"){
     $product_page_setting_convert_text[substr($row['fieldname'],0,strlen($row['fieldname'])-1)]=$row['convert_text'];
     }
}
$cnt++;
}

$c=0;
$sql="SELECT * FROM srai_others.advance_search_setting WHERE discoveree_cat_id=".$_REQUEST['discoveree_cat_id']." ORDER BY priority";
$results = mysql_query($sql);
while($row = mysql_fetch_assoc($results))
{
    foreach($row as $key => $value)
    {
    $search[$c][$key]=$value;
    }
$c++;
}


$cnt=0;
$live_sql="SELECT * FROM srai_others.discoveree_live_".$_REQUEST['discoveree_cat_id']."_v2  ";

$live_sql="SELECT * FROM srai_others.discoveree_live_".$_REQUEST['discoveree_cat_id']."_v2 WHERE ((NOT ignorefld LIKE '%datasheet-partno-mismatch-issue%' AND NOT ignorefld LIKE '%wrongcategory%' AND NOT ignorefld LIKE '%removepart%'  AND NOT ignorefld LIKE '%datasheetissue%'  AND NOT ignorefld LIKE '%crawl-again%'  AND NOT ignorefld LIKE '%datasheet-format-issue%') OR isnull(ignorefld))  ";
if($_SESSION['user_name']=="sanden_demo")
$live_sql.=" AND manf in('rohm','silan','ncepower') ";
if($_SESSION['user_name']=="dnp19716")
$live_sql.=" AND manf in('toshiba') AND series='-' LIMIT 30,5 ";


$result=mysql_query($live_sql);
while($row = mysql_fetch_assoc($result))
{
 $c=manflabel($row[$dashboard_setting['legend']]);

 foreach($row as $key => $value)
{

if(count($value_array[$key][$value])>0)
$value=$value_array[$key][$value]['org_value'];
else if(count($value_array[$key][trim($value)])>0)
$value=$value_array[$key][trim($value)]['org_value'];

     if($key!="searchword"){
     $data[$c][$cnt][$key]=($value);
     }else{
        foreach($msw as $mswk=>$mswv){
      $valuemsw=",".strtolower($value).",";
      $mswstr=explode("||",strtolower($mswv));
       if(strpos($valuemsw,",".trim($mswstr[0]).",")!==false)
      $data[$c][$cnt][$key.$mswk]=str_replace(",","",str_replace("(","",str_replace(")","",str_replace("[","",str_replace("]","",trim(($mswstr[0])))))));
      else if(strpos($valuemsw,",".trim($mswstr[1]).",")!==false)
      $data[$c][$cnt][$key.$mswk]=str_replace(",","",str_replace("(","",str_replace(")","",str_replace("[","",str_replace("]","",trim(($mswstr[1])))))));
      else if(strpos($valuemsw,",".trim($mswstr[2]).",")!==false)
      $data[$c][$cnt][$key.$mswk]=str_replace(",","",str_replace("(","",str_replace(")","",str_replace("[","",str_replace("]","",trim(($mswstr[2])))))));
      else if(strpos($valuemsw,",".trim($mswstr[3]).",")!==false)
      $data[$c][$cnt][$key.$mswk]=str_replace(",","",str_replace("(","",str_replace(")","",str_replace("[","",str_replace("]","",trim(($mswstr[3])))))));
      else if(strpos($valuemsw,",".trim($mswstr[4]).",")!==false)
      $data[$c][$cnt][$key.$mswk]=str_replace(",","",str_replace("(","",str_replace(")","",str_replace("[","",str_replace("]","",trim(($mswstr[4])))))));
      else
      $data[$c][$cnt][$key.$mswk]="";
      }
     }
}
$cnt++;
}

if($_SESSION['user_name']=="dnp19761"){
  echo "<pre>";
 //  print_r($search);
   print_r($data);
   exit;
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
	</style>
<!-- Service Section -->

<section id="services">
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

              <center><h1 style="margin:0px;padding:0px;">Advance Search</h1></center>
                 <!-- /.tab-pane -->
              <div class="tab-pane active">
                 <!-- /.box-header -->

<div class="nav-tabs-custom">

              <div class="table-responsive">
            <div class="tab-content">
            <div class="tab-pane active" style="padding:20px;">
            <div style="clear:both;"></div>
<div id="search">
 <form method="POST" action="compare.php">
<input type="hidden" name="action" value="advance_search" />
<? if($_REQUEST['id']!=1){?>
<input type="hidden" name="discoveree_cat_id" value="<?=$_REQUEST['discoveree_cat_id']?>" />
<?}?>
<? $cnt=0;
if($_REQUEST['id']==1){
?>
<div class="col-md-4"></div>
<div class="col-md-4">
<select required name="discoveree_cat_id"  class="form-control" onchange="if(this.value!=''){location.href='advance_search.php?discoveree_cat_id='+this.value;}">
<option value="">--Select Category--</option>
<? foreach($othercat as $c=>$v){?>
<option value="<?=$c;?>"><?=checksplit('','',$v);?></option>
<?}?>
</select>
</div>
<div class="col-md-4"></div>
<?
$cnt=1;
}
if($_REQUEST['id']!=1){
foreach($search as $k=>$v){
$cnt++;
if($cnt%5==0){

}?>
<div class="col-md-3">
<label><?=$v['label']?></label>
<br />
<? if(strpos($v['input_type'],"dropdown")!==false){?>
<select operator="" link_condition="<?=$v['link_value']?>" name="<?=$v['link_with']?><? if(strpos($v['input_type'],"dropdownm")!==false){?>[]<?}?>" multiplyby="<?=$v['multiplyby']?>"  input_type="<?=$v['input_type']?>"  link_fld="<?=$v['fieldname']?>" str="<?=$v['label']?>" <? if(strpos($v['input_type'],"dropdownm")!==false){?>multiple<?}?> class="form-control <? if(strpos($v['input_type'],"dropdownm")!==false){?>dropdownmultiselect<?}?>" id="d<?=$cnt;?>" <? if(strpos($v['input_type'],"dropdownm")===false){?>sonchange="datacall();"<?}?>>
<? if(strpos($v['input_type'],"dropdownm")===false){?>
<option SELECTED value="">-Select All-</option>
<?}?>
<?
unset($founddata);
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
if(!in_array($datavalue,$founddata) && $datavalue!=""){
?>
<option <? if(strpos($v['input_type'],"dropdownm")!==false){?>SELECTED<?}?> value="<?=($datavalue=="NA"?"":$datavalue);?>"><?=checksplit(substr($v['fieldname'],0,strlen($v['fieldname'])-1),$_REQUEST['discoveree_cat_id'],manflabel($datavalue),$v['value_type']);?></option>
<?
$founddata[]=$datavalue;
}}}?>
</select>
<?}?>

<? if(strpos($v['input_type'],"textboxm")!==false){?>
<input operator=">="  input_type="<?=$v['input_type']?>" multiplyby="<?=$v['multiplyby']?>" link_condition="<?=$v['link_value']?>" name="<?=$v['link_with']?>_min"  link_fld="<?=$v['fieldname']?>" type="text" sonkeyup="datacall();" class="form-control" id="min<?=$cnt;?>" placeholder="Min" style="float:left;width:47%;text-align:center;" /><div style="margin-top:5px;float:left;width:5%;text-align:center;">-</div><input onkeyup="datacall();" multiplyby="<?=$v['multiplyby']?>" input_type="<?=$v['input_type']?>" link_condition="<?=$v['link_value']?>" name="<?=$v['link_with']?>_max"  link_fld="<?=$v['fieldname']?>" operator="<=" type="text" class="form-control" id="max<?=$cnt;?>" placeholder="Max" style="float:left;width:47%;text-align:center;" />
<?}?>

<? if(strpos($v['input_type'],"textboxs")!==false){?>
<input operator="="  input_type="<?=$v['input_type']?>" multiplyby="<?=$v['multiplyby']?>" link_condition="<?=$v['link_value']?>" name="<?=$v['link_with']?>"  link_fld="<?=$v['fieldname']?>" type="text" sonkeyup="datacall();" class="form-control" id="s<?=$cnt;?>" />
<?}?>

</div>
<?if($cnt%4==0){
 echo "<div style='clear:both;'></div><br />";
 $cnt=0;
}
}
}
?>
<? if($_REQUEST['id']!=1){?>
<div class="col-md-3" style="text-align:left;"><br />
<button type="submit" class="btn btn-primary">Search</button>
</div>
<?}?>
 </form>
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

</div></div>

</div></div>
<?}?>
</div>

</div>
 </section>
 <br /> <br /> <br /> <br /> <br /> <br /> <br />
<?include("../footer.php");?>
<script src="../plugins/jQuery/jquery.min.js"></script>
<script src="../bootstrap/js/bootstrap.min.js"></script>
<script src="../dist/js/jquery.multiselect.js"></script>
<script>
 $(".dropdownmultiselect").each(function(){

 $('select#'+$(this).attr('id')).multiselect({
    columns: 2,
    search: false,
    placeholder: 'Select '+$(this).attr('str'),
    selectedOptions: ' '+$(this).attr('str'),
    selectAll     : true,
    closePopupOnSelect : true,
    maxPlaceholderOpts: 2,
});

 })



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

function random_color() {
    return random_color_part() . random_color_part() . random_color_part();
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

global $value_array;
if($value_array[$f][strtolower($s)]['org_value']!="")
return $value_array[$f][strtolower($s)]['org_value'];

if(strpos(strtolower($vtt),'condition')!==false || strpos(strtolower($s),'non-auto')!==false)
return $s;

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

<?php include "../includes/config.inc.php";?>
<?php
if(($_SESSION['user_name']!="dnp1976" && $_SESSION['user_name']!="srai") && $_SESSION['test_user_id']==""){
if(strpos($_SESSION['opage_access'],"id=".$_REQUEST['id']."&discoveree_cat_id=".$_REQUEST['discoveree_cat_id'])===false){
$_SESSION['lasturl'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] .  $_SERVER['REQUEST_URI'];

header("Location: /");
exit;
}
}
//mysql_query("INSERT INTO users_landing_page_log_time SET log_id=".$_SESSION['log_id'].",page='".str_replace("/","",$_SERVER['SCRIPT_URL'])."',username='".$_SESSION['user_name']."',action='start'");

$dashboard_setting=mysql_fetch_array(mysql_query("SELECT * FROM srai_others.dashboard_setting WHERE id=".$_REQUEST['id']));
$default_style=json_decode($dashboard_setting['graph_settings'], true);
$indv_default_style=json_decode($dashboard_setting['indv_graph_settings'], true);

$field_x=$dashboard_setting['link_withx'];
$field_y=$dashboard_setting['link_withy'];

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


$xy++;
}
}


$c=0;
$sql="SELECT * FROM srai_others.dashboard_search_setting WHERE dashboard_setting_id=".$_REQUEST['id']." ORDER BY priority";
$results = mysql_query($sql);
while($row = mysql_fetch_assoc($results))
{
    foreach($row as $key => $value)
    {
    $search[$c][$key]=$value;
    }
        $multiplyby[$row['multiplyby']."_".$c] = $row['fieldname'];
$c++;
}



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
$cnt=0;
$live_sql="SELECT * FROM srai_others.discoveree_live_".$dashboard_setting['discoveree_cat_id']."_v2 LIMIT 155,1 ";
$result=mysql_query($live_sql);
while($row = mysql_fetch_assoc($result))
{
 $c=manflabel($row[$dashboard_setting['legend']]);

 $data[$c][$cnt]['partno']=$row['partno'];
 $data[$c][$cnt]['manf_org']=$row['manf'];

 foreach($row as $key => $value)
{
     if(strlen($key)>2 && substr($key, -2)!="tc" && strpos($value,"=")===false)
     $data[$c][$cnt][$key]=manflabel($value);
}


/////////////////////////// For Default X/Y - Start
 if($dashboard_setting['link_valuex']=="exact")
 $data[$c][$cnt]['x']=$row[str_replace(",","",$dashboard_setting['fieldx'])];

 if($dashboard_setting['link_valuey']=="exact")
 $data[$c][$cnt]['y']=$row[str_replace(",","",$dashboard_setting['fieldy'])];

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
 $data[$c][$cnt]['x']=max($xv);

 if($dashboard_setting['link_valuex']=="least")
 $data[$c][$cnt]['x']=min($xv);

 if($dashboard_setting['link_valuey']=="maximum")
 $data[$c][$cnt]['y']=max($yv);

 if($dashboard_setting['link_valuey']=="least")
 $data[$c][$cnt]['y']=min($yv);

 $xcond=$ycond=0;

 if(count($error_rep)>0){
 foreach($error_rep['x'] as $xyk=>$xyv){
 if($xyk=="LIKE"){
 if(strpos($data[$c][$cnt]['x'],$xyv)===false)
 $xcond=1;
 }else  if($xyk=="="){
 if($data[$c][$cnt]['x']!=$xyv)
 $xcond=1;
 }else  if($xyk=="<="){
 if($data[$c][$cnt]['x']<=$xyv)
 $xcond=1;
 }else  if($xyk==">="){
 if($data[$c][$cnt]['x']>=$xyv)
 $xcond=1;
 }
 if($xcond==1)
 break;
 }

  foreach($error_rep['y'] as $xyk=>$xyv){
 if($xyk=="LIKE"){
 if(strpos($data[$c][$cnt]['y'],$xyv)===false)
 $ycond=1;
 }else  if($xyk=="="){
 if($data[$c][$cnt]['y']!=$xyv)
 $ycond=1;
 }else  if($xyk=="<="){
 if($data[$c][$cnt]['y']<=$xyv)
 $ycond=1;
 }else  if($xyk==">="){
 if($data[$c][$cnt]['y']>=$xyv)
 $ycond=1;
 }
 if($ycond==1)
 break;
 }
 }

 if($xcond==1 || $ycond==1 || !is_numeric($data[$c][$cnt]['x']) || !is_numeric($data[$c][$cnt]['y'])){
 $data[$c][$cnt]['x']=$data[$c][$cnt]['y']="0";
 }

 if(abs($data[$c][$cnt]['x'])>0 && $dashboard_setting['multiplyx']>1)
  $data[$c][$cnt]['x']=$data[$c][$cnt]['x']*$dashboard_setting['multiplyx'];

 if(abs($data[$c][$cnt]['y'])>0 && $dashboard_setting['multiplyy']>1)
  $data[$c][$cnt]['y']=$data[$c][$cnt]['y']*$dashboard_setting['multiplyy'];

  ////////////////////////////////////  For Default X/Y - End

   $data[$c][$cnt]['x']=str_replace(",","",$data[$c][$cnt]['x']);
   $data[$c][$cnt]['y']=str_replace(",","",$data[$c][$cnt]['y']);

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
 $xcond=1;
 }else  if($xyk==">="){
 if($data[$c][$cnt]['x'.$ddk]>=$xyv)
 $xcond=1;
 }
 if($xcond==1)
 break;
 }

  foreach($error_rep[$ddk]['y'] as $xyk=>$xyv){
 if($xyk=="LIKE"){
 if(strpos($data[$c][$cnt]['y'.$ddk],$xyv)===false)
 $ycond=1;
 }else  if($xyk=="="){
 if($data[$c][$cnt]['y'.$ddk]!=$xyv)
 $ycond=1;
 }else  if($xyk=="<="){
 if($data[$c][$cnt]['y'.$ddk]<=$xyv)
 $ycond=1;
 }else  if($xyk==">="){
 if($data[$c][$cnt]['y'.$ddk]>=$xyv)
 $ycond=1;
 }
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
     if(abs($data[$c][$cnt][$mpvv])>0)
     $data[$c][$cnt][$mpvv]=$data[$c][$cnt][$mpvv]*$mb[0];

    }
    }
  }

// if(abs($data[$c][$cnt]['x'])==0 || abs($data[$c][$cnt]['y'])==0 || $c=="")
// unset($data[$c][$cnt]);
$cnt++;
}


  // echo "<pre>";
  // print_r($data);
 //  print_r($jitterx);
  //  exit;
//$tot_part[0]=0;
//foreach($data as $k=>$v)
//foreach($v as $k1=>$v1)
//if(abs($v1['x'])>0 && abs($v1['y'])>0)
//$tot_part[0]=$tot_part[0]+1;
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
                 <!-- /.tab-pane -->
              <div class="tab-pane active">
                 <!-- /.box-header -->

<div class="nav-tabs-custom">

              <div class="table-responsive">
            <div class="tab-content">
            <div class="tab-pane active" style="padding:20px;">
            <div style="clear:both;"></div>
<div id="search" style="display:none;">
<? $cnt=0;
foreach($search as $k=>$v){
$cnt++;
if($cnt%5==0){
 echo "<div style='clear:both;'></div><br />";
}?>
<div class="col-md-3">
<label><?=$v['label']?></label>
<br />
<? if(strpos($v['input_type'],"dropdown")!==false){?>
<select operator="" link_condition="<?=$v['link_value']?>" link_with="<?=$v['link_with']?>" multiplyby="<?=$v['multiplyby']?>"  input_type="<?=$v['input_type']?>"  link_fld="<?=$v['fieldname']?>" str="<?=$v['label']?>" <? if(strpos($v['input_type'],"dropdownm")!==false){?>multiple<?}?> class="form-control <? if(strpos($v['input_type'],"dropdownm")!==false){?>dropdownmultiselect<?}?>" id="d<?=$cnt;?>" <? if(strpos($v['input_type'],"dropdownm")===false){?>onchange="datacall();"<?}?>>
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
if(!in_array($datavalue,$founddata) && $datavalue!=""){
?>
<option <? if(strpos($v['input_type'],"dropdownm")!==false){?>SELECTED<?}?> value="<?=$datavalue;?>"><?=$datavalue;?></option>
<?
$founddata[]=$datavalue;
}}}?>
</select>
<?}?>

<? if(strpos($v['input_type'],"textboxm")!==false){?>
<input operator=">="  input_type="<?=$v['input_type']?>" multiplyby="<?=$v['multiplyby']?>" link_condition="<?=$v['link_value']?>" link_with="<?=$v['link_with']?>"  link_fld="<?=$v['fieldname']?>" type="text" onkeyup="datacall();" class="form-control" id="min<?=$cnt;?>" placeholder="Min" style="float:left;width:47%;text-align:center;" /><div style="margin-top:5px;float:left;width:5%;text-align:center;">-</div><input onkeyup="datacall();" multiplyby="<?=$v['multiplyby']?>" input_type="<?=$v['input_type']?>" link_condition="<?=$v['link_value']?>" link_with="<?=$v['link_with']?>"  link_fld="<?=$v['fieldname']?>" operator="<=" type="text" class="form-control" id="max<?=$cnt;?>" placeholder="Max" style="float:left;width:47%;text-align:center;" />
<?}?>

<? if(strpos($v['input_type'],"textboxs")!==false){?>
<input operator="="  input_type="<?=$v['input_type']?>" multiplyby="<?=$v['multiplyby']?>" link_condition="<?=$v['link_value']?>" link_with="<?=$v['link_with']?>"  link_fld="<?=$v['fieldname']?>" type="text" onkeyup="datacall();" class="form-control" id="s<?=$cnt;?>" />
<?}?>

</div>
<?}if($cnt%4==0){
 echo "<div style='clear:both;'></div><br />";
}
if(count($dropdown_options)>0){
$cnt++;?>
<div class="col-md-3">
<label>View</label>
<br />
<select class="form-control" id="viewd" onchange="datacall();">
<option SELECTED value=""><?=strtoupper($dashboard_setting['link_withx']);?>,<?=strtoupper($dashboard_setting['link_withy']);?></option>
<?$dcn=0;
foreach($dropdown_options as $dk=>$dv){$dcn++;?>
<option value="<?=$dcn;?>"><?=stripslashes($dv);?></option>
<?}?>
</select>
</div>
<?}?>
<?if($cnt%4==0){
 echo "<div style='clear:both;'></div><br />";
}
if($dashboard_setting['jitter']==1){
$cnt++;?>
<div class="col-md-3">
<label>Jitter Shift (x-axis) <a href="#" class="zebra_tooltips"  title="Enter a jitter value between 0-5 to separate parts along the x-axis.">?</a></label>
<br />
<input type="text" class="form-control" id="jitter" onkeyup="datacall();" value="<?=$dashboard_setting['defaultjitter'];?>" />
</div>
<?}else{
$dashboard_setting['defaultjitter']=0;
}?>
<div class="col-md-3" style="text-align:left;"><br /><a href="dashboard.php?id=<?=$_REQUEST['id'];?>&discoveree_cat_id=<?=$_REQUEST['discoveree_cat_id'];?>&type=<?=$_REQUEST['type'];?>" class="btn btn-danger">Refresh</a></div>
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
<canvas id="canvas"></canvas>
<? if($dashboard_setting['toggle_y']==1){?>
<button id="toggleScale_y" default="<?=$dashboard_setting['display_typey'];?>" style="display:none;float:left;font-size: 10px;">Toggle Linear/Log Y-Scale</button>
<?}?>
<? if($dashboard_setting['toggle_x']==1){?>
<button id="toggleScale_x" default="<?=$dashboard_setting['display_typex'];?>" style="display:none;margin-left:5px;float:left;font-size: 10px;">Toggle Linear/Log X-Scale</button>
<?}?>
</div></div>

</div></div>
<?}?>
</div>

</div>
 </section>
 <br /> <br /> <br /> <br /> <br /> <br /> <br />
<script src="../admin/Chart.bundle.js"></script>
<script src="../admin/utils.js"></script>
<script src="../plugins/jQuery/jquery.min.js"></script>
<script src="../bootstrap/js/bootstrap.min.js"></script>
<script src="../dist/js/jquery.multiselect.js"></script>
<?include("../footer.php");?>
<script>
var gs = {};
<?
foreach($data as $k=>$v){
$lk=str_replace(" ","-",$k);

        if($default_style['legendfontcolor']=="random"){
        $legendfontcolor[$lk]=$color[$lk]=$bcolor[$lk]="#".random_color();
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

        if(count($indv_default_style[$llk])>0){
        $color[$lk]= $indv_default_style[$llk]['legendfontcolor'];
        $bcolor[$lk]= $indv_default_style[$llk]['borderColor'];
        $mergeOpacity[$lk]= $indv_default_style[$llk]['mergeOpacity'];
        $borderWidth[$lk]= $indv_default_style[$llk]['borderWidth'];
         $pointRadius[$lk]= $indv_default_style[$llk]['pointRadius'];
          $legendpointer[$lk]= $indv_default_style[$llk]['legendpointer'];
          $legendfontsize[$lk]= $indv_default_style[$llk]['legendfontsize'];
        }
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
var color = Chart.helpers.color;
	var scatterChartData = {
		datasets: [
        <?
        $tot_part=0;
        $legend_count=0;
        foreach($data as $k=>$v){
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
            ?>
            {
				x: <?=$v1['plotx']?>,
				y: <?=$v1['ploty']?>,

				orgx: <?=$v1['x']?>,
				orgy: <?=$v1['y']?>,
				partno: '<?=$v1['partno']?>',
				manf: '<?=$k?>',
				manf_org: '<?=$v1['manf_org']?>',
				discoveree_cat_id: '<?=$v1['discoveree_cat_id']?>',
			},
			<?}}?>
            ]
		},
       <?}?>
        ]
	};
    var lastHoveredIndex=0;
    var  lastoverobject="";
      
	window.onload = function() {
	$('#search,#toggleScale_x,#toggleScale_y').show();
	$('#loading').hide();
	total_searches();
		var ctx = document.getElementById('canvas').getContext('2d');
		window.myScatter = Chart.Scatter(ctx, {
			data: scatterChartData,
			options: {

			tooltips: {enabled: false},


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
	};


	document.getElementById("canvas").onclick = function(evt){
    var activePoints = myScatter.getElementsAtEvent(evt);
   if (activePoints.length > 0)
    {

         x=lastHoveredIndex.xLabel;
         y=lastHoveredIndex.yLabel;
         obj=myScatter.data.datasets;

         for(var key in obj) {  //for-1
         obj1=obj[key].data;
         for(var key1 in obj1) { //for-2
          //alert(x+":"+y+":"+obj1[key1].x+":"+obj1[key1].y+":::"+key+"=="+key1+"+++"+lastoverobject);
          if(lastoverobject.indexOf("-"+key+"-")>=0 && lastoverobject.indexOf("-"+key1+",")>=0 && Math.round(obj1[key1].x)==Math.round(x)){ //if
         // if(obj1[key1].x==x && obj1[key1].y==y){ //if
           manf=obj1[key1].manf;
           partno=obj1[key1].partno.toUpperCase();
           discoveree_cat_id=obj1[key1].discoveree_cat_id;
           manf_org=obj1[key1].manf_org;
           <?if($_REQUEST['type']!="development"){?>
           str='<div class="partno" onclick="$(this).remove();if($(\'.partno\').length==0){$(\'#sideMenu\').trigger(\'click\');$(\'#container\').hide();}" style="cursor:pointer;margin-right:5px;"><input type="hidden" class="compare_part" value="'+partno+'@'+manf_org+'@'+discoveree_cat_id+'" />'+partno+' ['+manf+'] <font style="color:#FF0000;">X</font></div>';
           <?}else{?>
           str='<div class="partno" style="margin-right:5px;"><input type="hidden" class="compare_part" value="'+partno+'@'+manf_org+'@'+discoveree_cat_id+'" /><a href="../user/data_fixing.php?part='+partno+'&manf='+manf.replace(' ','').toLowerCase()+'&discoveree_cat_id='+discoveree_cat_id+'" target="_blank">'+partno+'</a> ['+manf+'] <a href="#" onclick="$(this).parent().remove();if($(\'.partno\').length==0){$(\'#sideMenu\').trigger(\'click\');$(\'#container\').hide();}" style="color:#FF0000;">X</a></div>';
           <?}?>
           $('#comparebox').append(str);
          } //if
         }  //for-2
         }  //for-1
         var position = $('#container').css('right');
      if(position!="0px")
      $('#sideMenu').trigger('click');
      lastoverobject="";
     }
};

</script>
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
    onControlClose: function(event) {datacall();},
});

 })

 function total_searches(){

 tot_searches=0;
 $(".form-control").each(function(){
  if($(this).val()!="" && $(this).attr('id')!="viewd" && $(this).attr('id')!="jitter"){
  tot_searches++;
  }
 });

 $('#search_count').html('<br />Total Searches: '+tot_searches).show();

 }

 function datacall(){
 total_searches();
 viewdclick=$('#viewd').val();
 jittershift=$('#jitter').val();

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
  if($(this).attr('id')!="viewd" && $(this).attr('id')!="jitter"){

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

   if(selection_value[0]=="NA")
  selection_value[0]="";

  if(link_condition=="exact" && (input_type=="dropdownm" || input_type=="dropdowns") && selection_value.indexOf(value1[link_with].toString()) !== -1 ){
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

?>

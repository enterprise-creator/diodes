<?php include "../includes/config.inc.php";?>
<?php
if(($_SESSION['user_name']!="dnp1976" && $_SESSION['user_name']!="srai") && $_SESSION['test_user_id']==""){
if($_SESSION['opage_access']==""){
$_SESSION['lasturl'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] .  $_SERVER['REQUEST_URI'];

header("Location: /");
exit;
}
}

if($_SESSION['expired_user']==1 && $_COOKIE['expired_user_compare_lists']==1){
header("Location: https://www.discoveree.io/request_upgrade.php");
exit;
}
if($_SESSION['expired_user']==1){
$expire=time()+60*60*24;//however long you want
setcookie('expired_user_compare_lists', 1, $expire,'/');
}

$find_replace_test_conditions=(mysql_query("SELECT find1,find2,replacewith FROM srai_others.find_replace_test_conditions WHERE 1"));
while($mswrow=mysql_fetch_array($find_replace_test_conditions)){
$test_conditions_replace[$mswrow[0]."^^".$mswrow[1]]=trim($mswrow[2]);
}



$url=$_REQUEST['url'];
$url=explode("^",$url);

if(count($url)<=10){
//header("Location: compare.php?url=".$_REQUEST['url']);
//exit;
}
foreach($url as $u){
if($u!=""){
$url1=explode("@",$u);
$discoveree_cat_id.=$url1[2].",";
$part_cond.="(partno='".$url1[0]."' AND manf='".$url1[1]."' AND discoveree_cat_id='".$url1[2]."') OR ";
}
}
if($_REQUEST['discoveree_cat_id']!="")
$discoveree_cat_id=$_REQUEST['discoveree_cat_id'];

$getmanf_array=mysql_query("SELECT * FROM srai_others.manage_manf_name");
while($bh=mysql_fetch_array($getmanf_array)){
if($bh['manf_array']!="")
$manf_array=json_decode($bh['manf_array'],true);
if($bh['value_array']!="" && strpos($discoveree_cat_id,$bh['discoveree_cat_id'])!==false)
$value_array=json_decode($bh['value_array'],true);
}

$paging=25;

$dss=mysql_query("SELECT * FROM srai_others.dashboard_category_order WHERE discoveree_cat_id in (".$discoveree_cat_id."0) ORDER BY catorder");
while($drow=mysql_fetch_array($dss)){
$othercat[trim($drow['discoveree_cat_id'])]=ucwords(strtolower(trim(substr(trim($drow['catname']),strpos(trim($drow['catname']),'-')+1,strlen(trim($drow['catname']))))));
if(strpos(strtolower(strtolower(trim(substr(trim($drow['catname']),strpos(trim($drow['catname']),'-')+1,strlen(trim($drow['catname'])))))),'diode')!==false)
$paging=50;
}

foreach($manf_array as $k=>$v)
$rr_manf[strtolower($v['source_manf'])]=$v['org_manf'];


$sql="SELECT * FROM srai_others.product_page_setting WHERE inlisting=1 AND discoveree_cat_id in (".$discoveree_cat_id."0) ORDER BY priority";
$product_setting=(mysql_query($sql));
$cnt=0;
while($row = mysql_fetch_assoc($product_setting)){
foreach($row as $key => $value)
{
    if($key!="id" && $key!="discoveree_cat_id"){
     $product_page_setting[$row['discoveree_cat_id']][$cnt][$key]=$value;
     $product_page_setting_convert_text[substr($row['fieldname'],0,strlen($row['fieldname'])-1)]=$row['convert_text'];
     }
}
$cnt++;
}

if($_SESSION['user_name']=="dnp1976"){
  //echo "<pre>".$sql;
 // print_r($product_page_setting);
//exit;
}
$avlp=$_REQUEST['avlp'];
if($_SESSION['avlp'.$_SESSION['time_rec']]!="")
$avlp=$_SESSION['avlp'.$_SESSION['time_rec']];
$avlp=json_decode($avlp);

$ca=$_REQUEST['ca'];
if($_SESSION['ca'.$_SESSION['time_rec']]!="")
$ca=$_SESSION['ca'.$_SESSION['time_rec']];


$url=0;
$cnt=0;
foreach($product_page_setting as $k=>$v){
$live_sql="SELECT * FROM srai_others.discoveree_live_".$k."_v2 WHERE ".substr($part_cond,0,strlen($part_cond)-3);
if($_REQUEST['type']=="list"){
if($_REQUEST['rec']!="")
$part_cond=str_replace("@and@","&",$_REQUEST['rec']);
if($_SESSION['rec'.$_SESSION['time_rec']]!="")
$part_cond=str_replace("@and@","&",$_SESSION['rec'.$_SESSION['time_rec']]);

$part_cond=str_replace("@slash@","/",$part_cond);
$part_cond=str_replace("@bslash@","\\",$part_cond);
$part_cond=str_replace("@plus@","+",$part_cond);
$live_sql="SELECT * FROM srai_others.discoveree_live_".$k."_v2 WHERE ".substr($part_cond,0,strlen($part_cond)-3)." ";
if($_REQUEST['search_part']!="")
$live_sql.=" ORDER BY FIELD(partno,'".$_REQUEST['search_part']."') DESC";
$live_sql.=" LIMIT ".$paging;
}

if($_SESSION['user_name']=="dnp1976"){
//echo $live_sql;
}

$result=mysql_query(str_replace(" LIMIT ".$paging,"",$live_sql));

$url=$url+mysql_num_rows($result);

$result=mysql_query($live_sql);
//$url=$url+mysql_num_rows($result);
while($row = mysql_fetch_assoc($result))
{

 foreach($row as $key => $value)
{
     $data[$cnt][strtolower($key)]=($value);
}
$cnt++;
}
}
//echo "<pre>";
//print_r($data);
//   print_r($multiplyby);
//   exit;
$trrc=1;
reset($product_page_setting);

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
td{padding:5px;}
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
<center><h1 style="margin:0px;padding:0px;">Comparison</h1></center>
<div class="alert alert-info alert-dismissible" style="text-align:left;">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                <h4><b>Listing <?=($url>$paging?$paging:$url);?> out of <?=$url;?> parts that matched your query. Please refine your search further. <? if($ca!="2"){?><a href="#" onclick="history.back(-1);"><< Back</a><?}?></b></h4>
                To see detailed comparison, select up to 10 part numbers.
                <? if($ca==2 && count($avlp)>1){?>
               <br /><br />Similar parts<br />
               <?foreach($avlp as $av){?>
               <?if(strtoupper($av)==strtoupper($_REQUEST['search_part'])){?>
               <b><?=strtoupper($av);?></b><br />
               <?}else{
                 //$uu=strtolower($_SERVER['QUERY_STRING']);
                 //$uu=str_reaplce("type=list&","",$uu);
                 //$uu=str_replace("search_part=".strtolower($_REQUEST['search_part']),"crossreferencepartno=".strtolower($av),$uu);
               ?>
               <a href="compare-test.php?action=cross_search&o=1&p=<?=strtolower($av);?>"><?=strtoupper($av);?></a><br />
               <?}?>
               <?}?>
                <?}?>
                </div>
<a id="anchorID" href="#" target="_blank"></a>
<div class="floatbtn" style="display:none;text-align:right;margin-bottom:10px;background-color:#CCC;padding:10px;">
<input type="button" value="View Comparison" class="comparebtn btn btn-primary" />
</div>
<div style="overflow:auto;width:100%;">
 <table border="1" cellspacing="0" width="100%">
                        <thead>
				        	                             <tr style="background-color:#cbd9e0;color:#1f1e1e;">
	            				<td style="height:30px;width:250px;"><b>Part Number</b></td>
	            			 	<td style="width:100px;"><b>Manufacturer</b></td>
                                        <?foreach($product_page_setting[key($product_page_setting)] as $d=>$r){?>
                                 	            				<td><b><?=$r['label'];?><br /><?if($r['link_with']!="searchword"){echo $r['value_type'];}?></b></td>
                               <?}?>
                               <td><b>Compare</b></td>
	        				</tr>

                                                                                    </thead>
                            <tbody>
                            <?$p=0;foreach($data as $k=>$v){


                            if($v['discoveree_cat_id']!="" && count($msw[$v['discoveree_cat_id']])<=0){
$manf_search_words=(mysql_query("SELECT title,save_word,no_word FROM srai_others.manf_search_words WHERE discoveree_cat_id=".$v['discoveree_cat_id']." GROUP BY title,save_word,no_word ORDER BY save_word,no_word"));
while($mswrow=mysql_fetch_array($manf_search_words)){
$msw[$v['discoveree_cat_id']][strtolower($mswrow[0])]=$msw[$v['discoveree_cat_id']][strtolower($mswrow[0])].trim($mswrow[1])."||".trim($mswrow[2]);
}
}

                            $p++;?>

                              <tr <? echo "class='trbg".$p." trrc".$trrc."'";$trrc++;?> <?if(strtoupper($v['partno'])==strtoupper($_REQUEST['search_part'])){?>style="background-color:#b2c3e1;"<?}?>>


                            	     <td><a href="../discoveree_cat_resource/<?=strtolower($v['manf']);?>/<?=$v['discoveree_cat_id'];?>/datasheet-pdf/<?=strtolower($v['partno']);?>.pdf" target="_blank"><?=strtoupper($v['partno']);?></a><br></td>

                                     <td><?=checksplit('manf','',$v['manf']);?></td>

                             <?foreach($product_page_setting[key($product_page_setting)] as $d=>$r){?>
                             <td>
                             <?
                             if($r['link_value']=="exact"){    // For Exact

                             $l=explode(",",strtolower($r['fieldname']));
                             $str="";
                             $swstr="";
                             $vtt=$r['value_type'];$vtt=explode(",",$vtt);
                             for($i=0;$i<count($l)-1;$i++){
                             if(strpos($l[$i],"searchword")!==false){

                             $swlive=",".trim(strtolower($v['searchword'])).",";
                             $vtt=explode("||",$msw[$v['discoveree_cat_id']][str_replace("searchword","",$l[$i])]);
                             foreach($vtt as $vtt1){
                             if(strpos(trim($swlive),",".trim(strtolower($vtt1)).",")!==false)
                             $swstr.=trim($vtt1).",";
                             }
//                             if($swstr!="")
                             $str=substr($swstr,0,strlen($swstr)-1);
                             
                             }else if($l[$i]=="searchword" && $r['value_type']!="" && $v[$l[$i]]!=""){
                             $swlive=",".trim(strtolower($v[$l[$i]])).",";
                             foreach($vtt as $vtt1){
                             if(strpos(trim($swlive),",".trim(strtolower($vtt1)).",")!==false)
                             $swstr.=trim($vtt1).",";
                             }
//                             if($swstr!="")
                             $v[$l[$i]]=substr($swstr,0,strlen($swstr)-1);
                             }

                             if($r['multiplyby']>1 && strpos($l[$i],"searchword")===false)
                             $str.=($v[$l[$i]]==''?'-':str_replace(",","",number_format(($v[$l[$i]]*$r['multiplyby']),$r['decimal_place'])));
                             else if(is_numeric($v[$l[$i]]) && strpos($l[$i],"searchword")===false)
                             $str.=($v[$l[$i]]==''?'-':str_replace(",","",number_format($v[$l[$i]],$r['decimal_place'])));
                             else if(!is_numeric($v[$l[$i]]) && strpos($l[$i],"searchword")===false)
                             $str.=($v[$l[$i]]==''?'-':$v[$l[$i]]);

                             $vss=explode(",",$str);
                             if(count($vtt)-1>=count($vss) && strpos($l[$i],"searchword")===false)
                             $str.=",&nbsp;&nbsp;";

                             }
                             if(strpos($r['fieldname'],'searchword')!==false){
                             echo str_replace("Non-auto","Non-Auto",checksplit($l[0],$v['discoveree_cat_id'],manflabel(strtolower($str))));//substr($str,0,strlen($str)-1);
                             }else{
                             echo  str_replace("Non-auto","Non-Auto",checksplit($l[0],$v['discoveree_cat_id'],$str));
                             }
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
                             echo getv(str_replace(",","",number_format(min($xv),$r['decimal_place'])));
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
                             echo getv(str_replace(",","",number_format(max($xv),$r['decimal_place'])));
                             }
                             ?>
                             </td>
                             <?}?>

                             <td><label><input <?if(strtoupper($v['partno'])==strtoupper($_REQUEST['search_part'])){?>checked<?}?> id="compare_parts_<?=$p;?>" type="checkbox" onclick="addtocompare(this.checked,<?=$p;?>);" value="<?=$v['partno'];?>@<?=$v['manf'];?>@<?=$v['discoveree_cat_id'];?>" name="compare_parts[]"> select</label></td>

	            			 </tr>
	            			 <?if(strtoupper($v['partno'])==strtoupper($_REQUEST['search_part'])){?>
                             <script>
                             $(document).ready(function(){
                             addtocompare(true,<?=$p;?>);
                             });
                             </script>
                             <?}?>
                            <?}?>
	    				</tbody>
					</table>
					</div>
<?}?>
</div>

</div>
 </section>
 <br /> <br /> <br /> <br /> <br /> <br /> <br />
<?include("../footer.php");?>
<script>
function addtocompare(s,c){
total=$('input[name="compare_parts[]"]:checked').length;
if(total>10){
alert("Compare must be upto 10 parts only");
$('#compare_parts_'+c).prop("checked", false);
return false;
}
if(s)
$('.trbg'+c).css('background-color', '#F2DEDE');
else
$('.trbg'+c).css('background-color', '');

$('.comparebtn').val('View Comparison ('+total+')');
$('.buybtn').val('View Part Info ('+(total*10)+' Credits)');

if(total>0)
$('.floatbtn').show();
else
$('.floatbtn').hide();
}


$('.comparebtn').click(function(){
   url="";
   c=0;
   $('input[name="compare_parts[]"]:checked').each(function(){
    c++;
    url=url+$(this).val()+"^";
    $(this).click();
})
if(url!=""){
$('#anchorID').attr('href','compare.php?url='+url);
document.getElementById("anchorID").click();
}
});


(function($) {
    var element = $('.floatbtn'),
        originalY = element.offset().top;

    // Space between element and top of screen (when scrolling)
    var topMargin = 0;

    // Should probably be set in CSS; but here just for emphasis
    element.css('position', 'relative');

    $(window).on('scroll', function(event) {
        var scrollTop = $(window).scrollTop();

        element.stop(false, false).animate({
            top: scrollTop < originalY
                    ? 0
                    : scrollTop - originalY + topMargin
        }, 300);
    });
})(jQuery);
</script>
<?
function getv($s){
if(strpos($s,".")!==false && abs($s)>0)
return $s+0;
else
return $s;
}
function random_color_part() {
    return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
}

function random_color() {
    return random_color_part() . random_color_part() . random_color_part();
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

function checksplit($f,$k,$s){

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
 if($pass==1)
 $str.=${fu}(strtolower($key))."<hr />";
 }

 if($str!="")
 return $str;
 else
 return ${fu}(strtolower($s));
}
?>

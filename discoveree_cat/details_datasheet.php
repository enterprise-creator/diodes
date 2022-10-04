<?php
include "../includes/config.inc.php";
mysql_query("SET NAMES utf8");

if($_SESSION['expired_user']==1 && $_COOKIE['expired_user_details_datasheet']==1){
header("Location: https://www.discoveree.io/request_upgrade.php");
exit;
}
if($_SESSION['expired_user']==1){
$expire=time()+60*60*24;//however long you want
setcookie('expired_user_details_datasheet', 1, $expire,'/');
}

if($_REQUEST['loadadditional']=="yes"){
    echo '<br /><div class="col-md-12" style="text-align:center;">';
    $sql="SELECT DISTINCT doc_title,doc_url,hyperlink_name,manf FROM latest_crawl_result WHERE doc_url like '%.%' AND (NOT doc_desc like '%sheet%' AND NOT doc_url like '%sheet%' AND NOT doc_title like '%sheet%' AND NOT hyperlink_name like '%sheet%' AND NOT product_page_url like '%sheet%') AND part_no like '%".substr($_REQUEST['partno'],0,strpos($_REQUEST['partno'],":"))."%' AND manf like '%".substr($_REQUEST['partno'],strpos($_REQUEST['partno'],":")+1,strlen($_REQUEST['partno']))."%'";
    $results=mysql_query($sql);
    if(mysql_num_rows($results)<=0){
    $sql="SELECT DISTINCT doc_title,doc_url,hyperlink_name,manf FROM parts_doc_details_prv WHERE doc_url like '%.%' AND (NOT doc_desc like '%sheet%' AND NOT doc_url like '%sheet%' AND NOT doc_title like '%sheet%' AND NOT hyperlink_name like '%sheet%' AND NOT product_page_url like '%sheet%') AND part_no like '%".substr($_REQUEST['partno'],0,strpos($_REQUEST['partno'],":"))."%' AND manf like '%".substr($_REQUEST['partno'],strpos($_REQUEST['partno'],":")+1,strlen($_REQUEST['partno']))."%'";
    $results=mysql_query($sql);
    }
    $rr=0;
    while($row=mysql_fetch_array($results)){
    $domain=$row['manf'];
    if($row['manf']=="aos")
    $domain="aosmd";
    if(strpos($row['doc_url'],"http")===false)
    $row['doc_url']="http://".$domain.".com".$row['doc_url'];
    $rr++;
    ?>
    <div class="col-md-5" style="text-align:left;">
    <a href="<?=$row['doc_url'];?>" target="datasheet"><?=$row['doc_title'];?> <?=($row['hyperlink_name']!=""?"(".$row['hyperlink_name'].")":"");?></a>
    <hr />
    </div>
    <div class="col-md-1" style="text-align:left;">
     <a href="<?=$row['doc_url'];?>" target="datasheet"><img src="/download-icon.png" width="20" border=0 /></a>
    </div>
    <?if($rr%2==0){?>
     <div style="clear:both;"></div>
    <?}?>
   <? }
   echo "</div>";
   exit;
}


$surl=explode("/",$_REQUEST['url']);


if($surl[2]!=""){
$_REQUEST['partno']=$part=$pno=$surl[2];
$surl = decrypt($surl[0]."/".$surl[1]);
}else{
$_REQUEST['partno']=$part=$pno=$surl[1];
$surl = decrypt($surl[0]);
}
$manf=str_replace("igbt/","",$surl);



if(strpos($surl,'igbt/')===false){
 header("Location: igbts.php");
 exit;
}



$discoveree_cat_id=$_REQUEST['id'];

$sql="SELECT * FROM srai_others.product_page_setting WHERE discoveree_cat_id in (".$discoveree_cat_id.") ORDER BY priority";
$product_setting=(mysql_query($sql));
$cnt=0;
while($row = mysql_fetch_assoc($product_setting)){
foreach($row as $key => $value)
{
    if($key!="id" && $key!="discoveree_cat_id")
     $product_page_setting[$row['discoveree_cat_id']][$cnt][$key]=$value;
}
$cnt++;
}




$cnt=0;
foreach($product_page_setting as $k=>$v){

if($k!="" && count($msw[$k])<=0){
$manf_search_words=(mysql_query("SELECT title,save_word,no_word FROM srai_others.manf_search_words WHERE discoveree_cat_id=".$k." GROUP BY title,save_word,no_word ORDER BY save_word,no_word"));
while($mswrow=mysql_fetch_array($manf_search_words)){
$msw[$k][strtolower($mswrow[0])]=$mswrow[1]."||".$mswrow[2];
}
}


  $live_sql="SELECT * FROM srai_others.discoveree_live_".$k."_v2 WHERE partno='".$part."' AND manf='".$manf."'";
$result=mysql_query($live_sql);


if(mysql_num_rows($result)<=0){
header("Location: igbts.php");
exit;
}
while($row = mysql_fetch_assoc($result))
{
 foreach($row as $key => $value)
{
     if($key!="searchword")
     $data[$cnt][$key]= ($value);

        if($key=="searchword"){
      foreach($msw[$k] as $mswk=>$mswv){
      $valuemsw=",".strtolower($value).",";
      $mswstr=explode("||",strtolower($mswv));
      if(strpos($valuemsw,",".$mswstr[0].",")!==false && strpos($valuemsw,",".$mswstr[1].",")===false)
      $data[$cnt][$key.$mswk]=str_replace(",","",str_replace("(","",str_replace(")","",str_replace("[","",str_replace("]","",trim(manflabel($mswstr[0])))))));
      else if(strpos($valuemsw,",".$mswstr[0].",")===false && strpos($valuemsw,",".$mswstr[1].",")!==false)
      $data[$cnt][$key.$mswk]=str_replace(",","",str_replace("(","",str_replace(")","",str_replace("[","",str_replace("]","",trim(manflabel($mswstr[1])))))));
      else
      $data[$cnt][$key.$mswk]="";
      }
     }
}
$cnt++;
}
}
//echo "<pre>";
//print_r($data);
//   print_r($multiplyby);
 //  exit;
$trrc=1;
reset($product_page_setting);


//    $sql="INSERT INTO users_log SET username='".($_SESSION['user_name']!=''?$_SESSION['user_name']:'Guest')."',page_url='".$_SERVER['REQUEST_URI']."',user_ip='".$_SERVER['REMOTE_ADDR']."',user_country='".$_SESSION['user_country_name']."',user_city='".$_SESSION['user_city']."'";
// mysql_query($sql);

?>
<? include("../header.php");?>
<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
<style>
.fixed-header {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 10000;
    background-color:#FFF;
        box-shadow: 0 1px 4px 0 rgba(0,0,0,0.2);
    -webkit-box-shadow: 0 1px 4px 0 rgba(0,0,0,0.2);

}
.hdfixed{
width: 85%;
padding-right: 15px;
    padding-left: 15px;
    margin-right: auto;
    margin-left: auto;
    }
.product_details{
font-family: 'Poppins',sans-serif!important;
font-size:20px;
}
.index-content-container .fancy-title-wrapper h1 {
    text-transform: none;
}

.index-content-container .fancy-title-wrapper h1 {
    font-weight: 700;
    font-size: 50px!important;
    text-transform: capitalize;
    line-height: 1.6;
}

.index-base-img .fancy-title-wrapper h1 {
    color: #2f2c2c!important;
}

.fancy-title-wrapper h1 {
    font-weight: 700;
    font-size: 36px!important;
}
.index-header-text {
    text-transform: none!important;
    font-size: 40px;
}
 hr{margin:10px;}
</style>
<!-- Service Section -->
<section id="services">
<!-- start -->
<div class="container product_details">
<div class="col-md-10" style="margin-bottom:0px;">
<div class="fancy-title-wrapper">
<h1 id="fancy-title-4" class="mk-fancy-title  simple-style  fancyh1 color-single">
										<span class="index-header-text"><?=strtoupper($pno);?></span>
									</h1>
</div>
</div>
<div class="col-md-2" style="margin-bottom:0px;text-align:right;">

										<h1 id="fancy-title-4" class="mk-fancy-title  simple-style  fancyh1 color-single" style="margin-top: 10px;position: absolute;margin-top: 30px;right: 0;margin-right: 10px;">
                                        <?foreach($data as $k=>$v){?>
                                        <a href="../discoveree_cat_resource/<?=strtolower($v['manf']);?>/<?=$v['discoveree_cat_id'];?>/datasheet-pdf/<?=strtolower($v['partno']);?>.pdf" target="datasheet<?=time();?>" style="color:#000;font-size:20px;margin-top:20px;"><img src="/pdf_icon.png" width="30" border="0"> Datasheet</a>
                                        <?}?>
                                        </h1>

    </div>
</div>
 <div class="container product_details">
   <span style="margin-left:20px;"><?=$data[0]['searchwordconfig'];?>, <?=$data[0]['searchwordauto'];?> <?if($data[0]['package']!=""){?>in<?}?> <?=$data[0]['package'];?> </span>
<hr />
</div>

<!-- end -->

<!-- start -->
<style>
.personailse-helper-text {
    font-weight: bold;
    font-size: 32px!important;
    line-height: 1.5em;
    margin-bottom: 30px;
}
p.dark-text {
    font-size: 17px;
    line-height: 1.72em;
}

.dark-text {
    color: #252525!important;
}
.flipRight {
    transform: scaleX(-1);
    -moz-transform: scaleX(-1);
    -o-transform: scaleX(-1);
    -webkit-transform: scaleX(-1);
    filter: FlipH;
    -ms-filter: "FlipH";
}
.protip{cursor:pointer;font-size:10px;}
td{padding:5px;}
table.similartbl-- td{font-size:16px;}
</style>
<link rel="stylesheet" href="/css/protip.min.css">
<script src="/js/protip.min.js"></script>
<style>
.protip-skin-default--scheme-pro.protip-container {
	color: #FFF;
	background: #021e3a;
	line-height: 24px;
}
.protip-skin-default--scheme-pro[data-pt-position="bottom-left"] .protip-arrow,
.protip-skin-default--scheme-pro[data-pt-position="bottom"] .protip-arrow,
.protip-skin-default--scheme-pro[data-pt-position="bottom-right"] .protip-arrow {
	border-bottom-color: #021e3a;
}
.fa-info-circle{font-size: 16px;}
</style>
<div class="container product_details">

<div class="col-md-5" sstyle="margin-bottom:0px;height:400px;overflow:auto;">
<table border="1" cellspacing="0" class="similartbl" width="100%">
                        <thead>
                                          <tr style="background-color:#cbd9e0;color:#1f1e1e;">
	            				<td align="left" width="55%"><b>Device Parameter</b></td>
	            			 	<td align="left"><b>Value</b> <a onclick="jQuery('#pnoer').val('<?=$pno;?>');jQuery('#pnoerror').html('<?=strtoupper($pno);?>');jQuery('#modal-reporterror').modal('show');" style="cursor:pointer;font-size:10px;color:#FF0000;">feedback*</a></td>
	        				</tr>
                           </thead>
                           <tbody>
                           <tr>
	            				<td align="left">Manufacturer Name <a class="protip" data-pt-skin="default" data-pt-gravity="bottom;" data-pt-title="We rank <?=manflabel($manf);?> as #<?=manf_count($manf);?> in terms of number of MOSFET products availability"><i class="fa fa-info-circle" aria-hidden="true"></i></a></td>
	            			 	<td align="left"><?=manflabel($manf);?></td>
	        				</tr>
                           <tr>
                           <?foreach($product_page_setting[key($product_page_setting)] as $d=>$r){
                             if(strpos(strtolower($r['value_type']),'condition')===false || $_SESSION['user_name']=="dnp1976" || $_SESSION['user_name']=="srai" || strpos(strtolower($_SESSION['user_name']),"ritesh")!==false){
                            ?>

                              <tr <? echo "class='trrc".$trrc."'";$trrc++;?>>


                            <td style="text-align:left;"><?=$r['label'];?> <?   $vtt=trim($r['value_type']);if($r['link_with']!="searchword"){if($vtt!=""){echo "[".$vtt."]";}} $vtt=explode(",",$vtt);?></td>

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
                             $str.=($v[$l[$i]]==''?'-':number_format(($v[$l[$i]]*$r['multiplyby']),$r['decimal_place']));
                             else if(is_numeric($v[$l[$i]]))
                             $str.=($v[$l[$i]]==''?'-':number_format($v[$l[$i]],$r['decimal_place']));
                             else if(!is_numeric($v[$l[$i]]))
                             $str.=($v[$l[$i]]==''?'-':$v[$l[$i]]);
                             $vss=explode(",",$str);
                             if(count($vtt)-1>=count($vss))
                             $str.=",&nbsp;&nbsp;";
                             }
                             echo gettc($str,$r['tc']);//substr($str,0,strlen($str)-1);
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
                             echo number_format(min($xv),$r['decimal_place']);
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
                             echo number_format(max($xv),$r['decimal_place']);
                             }
                             ?>
                             </td>
                             <?}?>
	            			 </tr>
                            <?}}?>


                              </tbody>
                              </table>
</div>
 <script>
$(document).ready(function(){
    $.protip();
});
</script>
<div class="col-md-7 text-left" style="margin-bottom:0px;">
  <h3 class="mb10 heading-title" style="margin-top: 0px;">Similar Products <a class="protip" data-pt-skin="default" data-pt-gravity="bottom;" data-pt-title="This list is derived by applying DiscoverEE's own selection criteria and is highly dependent on them. It is possible to tighten the selection criteria to get fewer matches and relax the criteria to increase the matches."><i class="fa fa-info-circle" aria-hidden="true"></i></a></h3>


 We found <b><?$total=0; echo $total;?></b> products that are actively under promotion and similar to this product.
<br /><br />
<style>
td{padding:5px;}
table.similartbl td{padding:4px;}
</style>
<?if($total>0){?>
<div style="overflow:auto;height:970px;">
<table class="similartbl" border="1" cellspacing="0" width="100%">
                        <thead>
				        	                             <tr style="background-color:#cbd9e0;color:#1f1e1e;">
	            				<td align="center"><b>BV<sub>DSS</sub> [V]</b></td>
	            			 	<td align="center" width="35%"><b>Package</b></td>
 	            				<td align="center"><b>R<sub>DS(ON)</sub> max [&#937;] </b></td>
                                <td align="center"><b>V<sub>TH</sub> [V]</b></td>
                                <td align="center"><b>I<sub>D</sub> [A]</b></td>
                                <td align="center"><b>P<sub>D</sub> [W]</b></td>
                                <td align="center"><b>Q<sub>G Total</sub> [nC]</b></td>
                                <td align="center"><b>Q<sub>GS</sub> [nC]</b></td>
                                <td align="center"><b>Q<sub>GD</sub> [nC]</b></td>
	        				</tr>
                           </thead>


</table>
</div>
<?}else{
echo "There are no similar product found.";
}
?>
</div>

</div>
<!-- end -->

<!-- start-->
<!--
<link rel="stylesheet" href="/assets/fonts/font-awesome/font-awesome.min.css">
<div class="container product_details">
<div class="additionalresources" style="text-align:right;font-size:20px;margin-top:30px;margin-bototm:10px;background-color:#cbd9e0;color:#1f1e1e;margin-left:15px;cursor:pointer;margin-right:15px;"><b>Additional Resources</b><span class="fa fa-angle-double-down" style="font-size:14px;"></span>&nbsp;&nbsp;</div>
<div id="loadadditional"></div>
</div>
-->
<!-- end -->


<!-- start -->
<div style="clear:both;"></div>
<style>
.signup-price-helper {
    font-size: 25px;
    color: #161616;
    line-height: 1.6;
}
            .index-header-signup-form {
    display: table-cell;
    padding-right: 8px;
    float: none;
}
 .email {
    padding: 21px 26px 21px 26px;
    font-size: 24px;
}
  .email {
    background-color: #fff;

    color: #797676;
}

  .email {
    margin-bottom: 0!important;
}
  .index-header-signup-form input {

    border: 1px solid #d3d3d3!important;
    border-radius: 6px 6px 6px 6px!important;
    width: 100%!important;
    float: none!important;
}
input:invalid {
    box-shadow: none;
}
input {
    transition: all .3s ease;
}
.subscr-btn {
    padding: 21px 40px 22px 40px!important;
    font-size: 24px;
}

 .subscr-btn.bold {
    font-weight: bold!important;
}
 .subscr-btn {
    border-radius: 6px 6px 6px 6px;
    border:0px;
}

.subscr-btn {
    display: inline-block;
    background-color: #f16334;

    text-transform: uppercase;
    cursor: pointer;
    position: relative;
    color: #fff;
    text-align: center;
    width: auto!important;
    letter-spacing: .08em;
}


.ellipses {
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}
.min-height-360 {
}

.sub-form-container-1 {
    display: table;
    table-layout: fixed;
    width: 100%;
    position: relative;
    background: #e8f4ff;
    background: -moz-linear-gradient(left,#ffe124 30%,#fbee8d 100%);
    background: -webkit-linear-gradient(left,#ffe124 30%,#fbee8d 100%);
    background: linear-gradient(to right,#ffe124 30%,#fbee8d 100%);
}
.sub-form-container-1 h3 {
    font-size: 29px!important;
    margin-bottom: 5px;
    line-height: 1.6;
    font-weight: 600;
    padding-top:50px;
}
.promoton-side {
    border: 0px solid #f8f8f8;
    min-height: 360px;
    vertical-align: middle;
    width: 45%;
    border-right: 1px solid #dcbd04;
}
</style>


<div class="col-md-12  sub-form-container-1"  style="padding:10px;margin-top:40px;">
   <center>
   <h3 style="padding-top:0px;">Signup And Explore DiscoverEE's Solutions</h3>

<div class="index-header-signup-form">
												<input class="email input-standard-grey input-white" id="signupemail" name="email" required="" placeholder="Your email address" type="email">

											</div>

											<div class="index-header-signup-form">
												<button class="subscr-btn ellipses bold signupbtn" data-effect="mfp-zoom-in">Sign up</button>
											</div>
											<span class="signupmsg" style="color:#FF0000;"></span>
 <br /></center>
 </div>

<!-- end -->
<!-- start -->
<!--<center><img src="/flow-img.png" /></center>-->
<!-- end -->



<!-- start -->
<div style="clear:both;"></div>
<div class="container product_details" style="padding:30px;">
 <div class="col-md-6">
 <h3 class="mb10 heading-title personailse-helper-text">Create Live Datasheets</h3>
 <p class="dark-text">Datasheets were created in the era of printing press and
even with all their limitations they still are a de facto
standard of publishing product information.<br /><br />
At DiscoverEE, we bring the power of cloud computing and
web technologies to create LIVE Datasheets. With LIVE
Datasheets you can instantly simulate the datasheet curves
under your specific application conditions.<br /><br />
Why spend hours extrapolating datasheet curves when you
can simulate them in an instant? Get in touch to explore the
possibilities at <a href="mailto:info@discoveree.io">info@discoveree.io</a>.

					</p>
 </div>
 <div class="col-md-6">
 <br /><br />
 <div id="chartContainer_2" style="height: 400px; width: 100%;"><center><br /><br />Loading..</center></div>
 <div style="position:absolute;width:60px;height:15px;background-color:#FFF;bottom: 0;margin-bottom: 0px;"></div>
 </div>
</div>
<!-- end -->


<!-- start -->

<center><div class="flipRight"><img src="/flow-img.png"  /></div></center>
<!-- end -->

<!-- start -->
<div style="clear:both;"></div>
<div class="container product_details" style="padding:30px;margin-top:30px;">
 <div class="col-md-6"><br /><br />
 <center><img src="https://www.discoveree.io/syncbuck.jpg" style="width:100%;height:400px;" /></center>
 </div>
 <div class="col-md-6">
 <h3 class="mb10 heading-title personailse-helper-text">Simulate Product Performance in Application Instantly</h3>
 <p class="dark-text">Product specifications are one thing but how the product behaves in the real application circuit can never be captured from the information in the datasheet. Never make the mistake of selecting a part based solely on datasheet parameters.
<br /><br />DiscoverEE gives you the ability to instantly simulate the performance of different products from the market with unprecedented ease.
<br /><br />To learn how LIVE Application Pages can benefit you, click here or email <a href="mailto:sales@discoveree.io">sales@discoveree.io</a> to schedule a demo.
					</p>
 </div>

</div>
<!-- end -->
 <!-- start -->
<center><div cdlass="flipRight"><img src="/flow-img.png"  /></div></center>
<!-- end -->

<!-- start -->
<div style="clear:both;"></div>
<div class="container product_details" style="padding:30px;">


  <div class="col-md-6">
 <h3 class="mb10 heading-title personailse-helper-text">DiscoverEE Provides Your Business
A Competitive Advantage</h3>
 <p class="dark-text">With so many new electronics components being released every day, we understand it can be difficult to keep track of
what's new in the market. If you ever wished that there was a
better way to keep track of the electronics components, you
have come to the right place.<br /><br />
At DiscoverEE, we not only provide information about the new
releases but also map that information against the backdrop
of the entire market so you know where individual products
and new releases actually fit in. Further, you can be assured
that you are not missing any notable products.
					</p>
 </div>
  <div class="col-md-6"><br />
 <center><img src="https://www.discoveree.io/images/img12.jpeg" style="width:100%;" /></center>
 </div>
</div>
<!-- end -->
 <!-- start -->
<center><div class="flipRight"><img src="/flow-img.png"  /></div></center>
<!-- end -->
<!-- start -->
<div class="container product_details">
    <div class="col-md-6">
 <center><img src="/images/img3.jpeg" style="width:90%;" /></center>
 </div>

  <div class="col-md-6">
 <h3 class="mb10 heading-title personailse-helper-text">DiscoverEE Let's You Visualize The
Entire Component Landscape</h3>
 <p class="dark-text">Imagine the ability to visualize  products along with their entire market.<br /><br />
DiscoverEE is the world's first company to create to product dashboards where you see the entire market landscape in one chart.<br /><br />
Product dashboards lets you see all products from all the competing manufacturers in one place so you can pick the most optimum product for your use cases.

					</p>
 </div>

</div>
<!-- end -->

<!-- start -->
<center><div cladss="flipRight"><img src="/flow-img.png"  /></div></center>
<!-- end -->
<!-- start -->
<div class="container product_details">

  <div class="col-md-6">
 <h3 class="mb10 heading-title personailse-helper-text">DiscoverEE Provides You The
Industry Best Cross References</h3>
 <p class="dark-text">Finding a good alternative for a specific product (also called cross-reference) is not easy task. Not only do you need to know how the product works but also who makes the products and how each product compares with each other.
<br /><br />At DisocverEE, our proprietary techniques provides you the industry best cross-references.
<br /><br />It is as simple as typing the part number and clicking a button. Let us do all the hard work in the background and be ready to see the cross-reference results that took you hours and days to find previously.

					</p>
 </div>

 <div class="col-md-6"><br />
 <center><img src="/images/img4.jpeg" style="width:100%;height:400px;" /></center>
 </div>
</div>
<!-- end -->


<!-- start -->
<center><div class="flipRight"><img src="/flow-img.png"  /></div></center>
<!-- end -->
<!-- start -->
<div class="container product_details">


    <div class="col-md-6"><br />
 <center><img src="/images/img5.jpeg" style="width:100%;height:350px;" /></center>
 </div>

   <div class="col-md-6">
 <h3 class="mb10 heading-title personailse-helper-text">DiscoverEE Let's You Perform
Datasheet Comparison's With
One-Click</h3>
 <p class="dark-text">How do you perform datasheet comparisons with DiscoverEE? Simply enter the part numbers and click on compare button.
<br /><br />You instantly see the side-by-side datasheet comparisons of product specifications. Prior to using DiscoverEE, it took some of our users a full 8 hours to compare datasheet of 8 products.
<br /><br />From 8 hours of engineering time to less than 8 seconds . Now that's what we call productivity gain!

					</p>
 </div>


</div>
<!-- end -->
<!-- start -->
<center><div cldass="flipRight"><img src="/flow-img.png"  /></div></center>
<!-- end -->
<!-- start -->
<div class="container product_details">


  <div class="col-md-6">
 <h3 class="mb10 heading-title personailse-helper-text">DiscoverEE Enables Fast
Device Selection Based On
Power Loss Modeling</h3>
 <p class="dark-text">At PCIM Europe 2020, we demonstrated that selecting Power Devices based on Power Loss Modeling is more effective than selection based on key parameters alone.?
<br /><br />Find Out More At The Link: <a href="https://www.discoveree.io/collateral/access_content.php?file=continental/PCIM2020_DiscoverEE_PowerLossModeling_AudioVisual.mp4">PCIM 10-Min Video</a> or <a href="https://www.discoveree.io/collateral/access_content.php?file=PCIM_Europe_2020/PCIM2020_DiscoverEE_PowerLossModeling_Slides.pdf">PCIM PDF Presenation</a>?

					</p>
 </div>
         <div class="col-md-6"><br />
 <center><img src="/images/img6.jpeg" style="width:100%;" /></center>
 </div>
</div>
<!-- end -->


<!-- start -->
<center><div  class="flipRight"><img src="/flow-img.png"  /></div></center>
<!-- end -->
<!-- start -->
<div class="container product_details">

      <div class="col-md-6"><br />
 <center><img src="/images/img7.jpeg" style="width:100%;" /></center>
 </div>

   <div class="col-md-6">
 <h3 class="mb10 heading-title personailse-helper-text">DiscoverEE Provides Industry
Specific Dashboards So You
Can Save Precious Time For
High Value Tasks</h3>
 <p class="dark-text">Whether it is comparing product portfolios of manufacturers, tracking products released over time or monitoring new releases in the market, we provide this information in easy to use interactive dashboards so you can analyze products just as you analyze your spending and investments.
					</p>
 </div>


</div>
<!-- end -->
<!-- start -->
<center><div cdlass="flipRight"><img src="/flow-img.png"  /></div></center>
<!-- end -->
<!-- start -->
<div class="container product_details">


  <div class="col-md-6">
 <h3 class="mb10 heading-title personailse-helper-text">DiscoverEE Provides Spice
Models For Power Devices -
MOSFETs, IGBTs, BJTs and
Diodes</h3>
 <p class="dark-text">Do you know a majority of the power devices are missing Spice
models? At DiscoverEE, we provide SPICE circuit models so you can
confidently simulate the devices in your application and be more
confident about your design.<br /><br />
Further, different manufacturers create different levels/types of
SPICE models which makes it difficult to compare their performance.
At DiscoverEE, we provide standardized SPICE models so you can
perform a more meaningful comparison.<br /><br />
Whether you are looking for one model or thousands of them, get in
touch with us at <a href="mailto:info@discoveree.io">info@discoveree.io</a>.</p>
 </div>
       <div class="col-md-6">
 <center><img src="/images/img8.jpeg" style="width:90%;" /></center>
 </div>
</div>
<!-- end -->


<!-- start -->
<center><div class="flipRight"><img src="/flow-img.png"  /></div></center>
<!-- end -->

<!-- start -->
<div class="container product_details">

 <div class="col-md-6"><br />
 <center><img src="/images/img10.jpeg" style="width:100%;" /></center>
 </div>
  <div class="col-md-6">
 <h3 class="mb10 heading-title personailse-helper-text">DiscoverEE Lets You Simulate
1000s of Parts In Your Circuits
To Identify Best Matches</h3>
 <p class="dark-text">Imagine simulating multiple components in your circuit at the
click of a button.<br /><br />
DiscoverEE can simulate 1000s of parts in your circuit,
compare their performance and identify the best matching
devices for your application.<br /><br />
The possibilities are endless but they cannot be created
without <b><u>your</u></b> involvement. We look forward to make your ideas
come to life.
					</p>
 </div>

</div>
<!-- end -->


<!-- start -->
<center><div clasds="flipRight"><img src="/flow-img.png"  /></div></center>
<!-- end -->
<!-- start -->
<div class="container product_details">



   <div class="col-md-6">
 <h3 class="mb10 heading-title personailse-helper-text">Top Semiconductor Manufacturers
Use DiscoverEE</h3>
 <p class="dark-text">Top Semiconductor Manufacturers Use DiscoverEE To Identify
Market Gaps, Design The Market Leading Products, Provide
Superior Customer Support, Improve Productivity And Increase
Sales.
					</p>
 </div>

 <div class="col-md-6"><br />
 <center><img src="/images/img11.jpeg" style="width:100%;" /></center>
 </div>
</div>
<!-- end -->

<!-- start -->
<link rel='stylesheet' href='/css/slick.css'>
<link rel='stylesheet' href='/css/slick-theme.css'>
<style>

.content {
  margin: auto;
  padding: 20px;
  width: 80%;
  padding-top: 0px;
}



.slick-prev,
.slick-next {
  color: white;
  opacity: 1;
  height: 40px;
  width: 40px;
  margin-top: -20px;
}
.slick-prev path,
.slick-next path {
  fill: #000;
}
.slick-prev:hover path,
.slick-next:hover path {
  fill: #686868;
}

.slick-prev {
  left: -35px;
}

.slick-next {
  right: -35px;
}

.slick-prev:before,
.slick-next:before {
  content: none;
}

.slick-dots li button:before {
  color: rgba(255, 255, 255, 0.4);
  opacity: 1;
  font-size: 8px;
  content: none;
}

.slick-dots li.slick-active button:before {
  color: #000;
}


.quote-container .portrait {
  position: absolute;
  top: 0;
  bottom: 0;
  margin: auto;
  height: 140px;
  width: 140px;
  overflow: hidden;
}
.quote-container .portrait img {
  display: block;
  height: auto;
  width: 100%;
}
.quote-container .quote {
  position: relative;
  z-index: 600;
  padding: 10px 0 0px 0px;
  margin: 0;
  font-size: 20px;
  font-style: italic;
  color: #000;
}
.quote-container .quote p {
  position: relative;
  margin-bottom: 20px;
}

.quote-container .quote cite {
  display: block;
  font-size: 14px;
}
.quote-container .quote cite span {
  font-size: 16px;
  font-style: normal;
  letter-spacing: 1px;
  text-transform: uppercase;
}

.dragging .quote-container {
  cursor: -webkit-grabbing;
  cursor: grabbing;
}


</style>



<div style="clear:both;"></div>
<br />
   <div class="col-md-12" style="background-color: #cbd9e0;text-align:center;">
 <h3 class="mb10 heading-title personailse-helper-text">Customer Testimonials</h3>
  <div class='content'>
  <div class='slider single-item'>
<!-- start 1 -->
    <div class='quote-container'>
     <div class='quote'>
        <blockquote style="border:0px;">
 <p>
          "DiscoverEE provides visual analysis of Power Mosfet landscape of over 20,000 products using their easy to use industry specific dashboards.
         <br />
My product marketing team and I have been using DiscoverEE for over an year. We are very satisfied since it provides us product and market insights while saving us valuable time."
 <br />
</p>
          <cite>
            <span>Stéphane Ernoux</span>
            <br>
            Senior Marketing Director
            <br>
            Infineon Technologies
          </cite>
        </blockquote>
      </div>
    </div>
<!-- end 1 -->

  </div>
</div>


<script src='/js/slick.min.js'></script>
<script id="rendered-js" >
var prevButton = '<button type="button" data-role="none" class="slick-prev" aria-label="prev"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" version="1.1"><path fill="#000" d="M 16,16.46 11.415,11.875 16,7.29 14.585,5.875 l -6,6 6,6 z" /></svg></button>',
nextButton = '<button type="button" data-role="none" class="slick-next" aria-label="next"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="#000" d="M8.585 16.46l4.585-4.585-4.585-4.585 1.415-1.415 6 6-6 6z"></path></svg></button>';

$('.single-item').slick({
  infinite: true,
  dots: true,
  autoplay: true,
  autoplaySpeed: 4000,
  speed: 1000,
  cssEase: 'ease-in-out',
  prevArrow: prevButton,
  nextArrow: nextButton });


$('.quote-container').mousedown(function () {
  $('.single-item').addClass('dragging');
});
$('.quote-container').mouseup(function () {
  $('.single-item').removeClass('dragging');
});

    </script>
 </div>

<!-- end -->


<!-- start -->
<!--<center><img src="/flow-img.png" /></center>-->
<!-- end -->
<!-- start -->
<style>
.min-height-360 {
    min-height: 360px;
}

.signup-form-container-2 {
    display: table;
    table-layout: fixed;
    width: 100%;
    position: relative;
    background: #e8f4ff;
    background: -moz-linear-gradient(left,#ffe124 30%,#fbee8d 100%);
    background: -webkit-linear-gradient(left,#ffe124 30%,#fbee8d 100%);
    background: linear-gradient(to right,#ffe124 30%,#fbee8d 100%);
}
.signup-form-container-2 h3 {
    font-size: 29px!important;
    margin-bottom: 5px;
    line-height: 1.6;
    font-weight: 600;
    padding-top:50px;
}
.signup-price-helper {
    font-size: 25px;
    color: #161616;
    line-height: 1.6;
}
            .index-header-signup-form {
    display: table-cell;
    padding-right: 8px;
    float: none;
}
 .email {
    padding: 21px 26px 21px 26px;
    font-size: 24px;
}
  .email {
    background-color: #fff;

    color: #797676;
}

  .email {
    margin-bottom: 0!important;
}
  .index-header-signup-form input {

    border: 1px solid #d3d3d3!important;
    border-radius: 6px 6px 6px 6px!important;
    width: 100%!important;
    float: none!important;
}
input:invalid {
    box-shadow: none;
}
input {
    transition: all .3s ease;
}
.subscr-btn {
    padding: 21px 40px 22px 40px!important;
    font-size: 24px;
}

 .subscr-btn.bold {
    font-weight: bold!important;
}
 .subscr-btn {
    border-radius: 6px 6px 6px 6px;
    border:0px;
}

.subscr-btn {
    display: inline-block;
    background-color: #f16334;

    text-transform: uppercase;
    cursor: pointer;
    position: relative;
    color: #fff;
    text-align: center;
    width: auto!important;
    letter-spacing: .08em;
}


.ellipses {
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}
</style>
<div class="product_details" style="margin-top:70px;">
<div class="col-md-12 signup-form-container-2" sstyle="padding:10px;">
   <center>
<!--   <h3 class="mb10 heading-title personailse-helper-text" style="padding-top: 20px;">DiscoverEE is all about knowing your competition, not
being caught out by your competition, and based on
competitive data that is always accurate and up to
date! It's a competitive fight, every day, and
DiscoverEE rovides you the means of helping Your
Company win that fight.</h3>
-->
   <h3 style="padding:0px;padding-bottom:10px;">Try Us Out. Ask for a demo!</h3>

<div class="index-header-signup-form">
												<input id="emailrequestdemo" class="email input-standard-grey input-white" name="email" required="" placeholder="Your email address" type="email">

											</div>

											<div class="index-header-signup-form">
           <button class="subscr-btn ellipses bold requestdemo">Request Demo</button>
											</div>
											<span class="demomsg" style="color:#FF0000;"></span>
 <br /></center>
 </div>
</div>
<!-- end -->
 </section>

<?include("../footer.php");?>

<script src="/js/grapahjs.min.js"></script>
<script>
data_arr={};
data_arr[1]=[];
data_arr[2]=[];
data_arr[3]=[];

data_arr[1][1]={x:0.000101623232937243,y:0.032586932292754};
data_arr[1][2]={x:0.000144823540623688,y:0.0390215348633906};
data_arr[1][3]={x:0.000184388684427856,y:0.0431303128837853};
data_arr[1][4]={x:0.000242446201708233,y:0.048635851273841};
data_arr[1][5]={x:0.000345510729459222,y:0.0606189899349757};
data_arr[1][6]={x:0.00045429991686721,y:0.0697394087111735};
data_arr[1][7]={x:0.00061689292607757,y:0.0851990559548702};
data_arr[1][8]={x:0.000772879677907017,y:0.094170102575442};
data_arr[1][9]={x:0.001,y:0.110529514112602};
data_arr[1][10]={x:0.00125285871378241,y:0.124638488578444};
data_arr[1][11]={x:0.00147174364035974,y:0.140548458570921};
data_arr[1][12]={x:0.00187381742286038,y:0.164964807409802};
data_arr[1][13]={x:0.00246381668309456,y:0.186022388945322};
data_arr[1][14]={x:0.00334561162465983,y:0.218338556525623};
data_arr[1][15]={x:0.00432876128108306,y:0.25118864315095};
data_arr[1][16]={x:0.005784126230175,y:0.283252605258997};
data_arr[1][17]={x:0.00748386378309053,y:0.32586932292754};
data_arr[1][18]={x:0.00984026950429283,y:0.374897930870418};
data_arr[1][19]={x:0.0129386245826521,y:0.398107170553497};
data_arr[1][20]={x:0.0149563346788184,y:0.422753251473378};
data_arr[1][21]={x:0.0187381742286038,y:0.467267114748752};
data_arr[1][22]={x:0.0231012970008316,y:0.5164680715397};
data_arr[1][23]={x:0.0280254405033009,y:0.570849650019635};
data_arr[1][24]={x:0.0356818655493467,y:0.630957344480193};
data_arr[1][25]={x:0.0447043361775117,y:0.697394087111734};
data_arr[1][26]={x:0.0542332629088885,y:0.740568469226243};
data_arr[1][27]={x:0.0679466160122538,y:0.81854673070690};
data_arr[1][28]={x:0.0893406177402612,y:0.904735724234929};
data_arr[1][29]={x:0.113748075072293,y:0.904735724234929};
data_arr[1][30]={x:0.154458278026823,y:0.94170102575442};
data_arr[1][31]={x:0.203091762090473,y:0.94170102575442};
data_arr[1][32]={x:0.313692038539278,y:0.94170102575442};
data_arr[1][33]={x:0.461674262751667,y:0.94170102575442};

data_arr[2][1]={x:0.000101623232937243,y:0.124638488578444};
data_arr[2][2]={x:0.000151991108295293,y:0.129730914120867};
data_arr[2][3]={x:0.000242446201708233,y:0.137762315823046};
data_arr[2][4]={x:0.000362610653435665,y:0.149249554505182};
data_arr[2][5]={x:0.000492388263170674,y:0.152268018309481};
data_arr[2][6]={x:0.000647424024645891,y:0.164964807409802};
data_arr[2][7]={x:0.000879135756244175,y:0.168301100786357};
data_arr[2][8]={x:0.0011193117143059,y:0.193622811380895};
data_arr[2][9]={x:0.00154458278026823,y:0.218338556525623};
data_arr[2][10]={x:0.00223691947024827,y:0.246209240149462};
data_arr[2][11]={x:0.00313692038539279,y:0.27213387683753};
data_arr[2][12]={x:0.00405874352122658,y:0.300788251804309};
data_arr[2][13]={x:0.00542332629088886,y:0.32586932292754};
data_arr[2][14]={x:0.00701703828670383,y:0.346043291889247};
data_arr[2][15]={x:0.00893406177402611,y:0.374897930870418};
data_arr[2][16]={x:0.0108383965061256,y:0.382479969144438};
data_arr[2][17]={x:0.0135789795061284,y:0.422753251473378};
data_arr[2][18]={x:0.0164733989710987,y:0.458004310330155};
data_arr[2][19]={x:0.0209738579187812,y:0.49619476030029};
data_arr[2][20]={x:0.0267038221297861,y:0.548441657612101};
data_arr[2][21]={x:0.0334561162465983,y:0.618449649643718};
data_arr[2][22]={x:0.0425961776256052,y:0.670018750350958};
data_arr[2][23]={x:0.0516757410491482,y:0.72588791356011};
data_arr[2][24]={x:0.0626906535241184,y:0.802320383860095};
data_arr[2][25]={x:0.0837677640068292,y:0.886800821906928};
data_arr[2][26]={x:0.104949173070025,y:0.94170102575442};
data_arr[2][27]={x:0.133620817933572,y:0.923033346932114};
data_arr[2][28]={x:0.184388684427856,y:0.960746244818117};
data_arr[2][29]={x:0.242446201708233,y:0.960746244818117};
data_arr[2][30]={x:0.28480358684358,y:0.98017663960029};
data_arr[2][31]={x:0.368496668996185,y:0.94170102575442};
data_arr[2][32]={x:0.500380871637579,y:0.98017663960029};
data_arr[2][33]={x:0.607039264789507,y:0.98017663960029};
data_arr[2][34]={x:0.760534432499608,y:0.98017663960029};

data_arr[3][1]={x:0.00011193117143059,y:0.48635851273841};
data_arr[3][2]={x:0.00016740800609081,y:0.48635851273841};
data_arr[3][3]={x:0.000231012970008316,y:0.48635851273841};
data_arr[3][4]={x:0.000308681420057751,y:0.48635851273841};
data_arr[3][5]={x:0.000412462638290135,y:0.48635851273841};
data_arr[3][6]={x:0.000597342996521653,y:0.48635851273841};
data_arr[3][7]={x:0.000851275099429802,y:0.49619476030029};
data_arr[3][8]={x:0.00127319552908165,y:0.52691326305265};
data_arr[3][9]={x:0.00184388684427856,y:0.51646807153977};
data_arr[3][10]={x:0.00267038221297861,y:0.559533491673248};
data_arr[3][11]={x:0.00368496668996185,y:0.582394669446952};
data_arr[3][12]={x:0.00587801607227491,y:0.606189899349757};
data_arr[3][13]={x:0.00851275099429802,y:0.643717998357411};
data_arr[3][14]={x:0.0106652742614662,y:0.643717998357411};
data_arr[3][15]={x:0.013148648598504,y:0.643717998357411};
data_arr[3][16]={x:0.0184388684427856,y:0.643717998357411};
data_arr[3][17]={x:0.0267038221297861,y:0.72588791356011};
data_arr[3][18]={x:0.0351119173421513,y:0.72588791356011};
data_arr[3][19]={x:0.0447043361775117,y:0.740568469226243};
data_arr[3][20]={x:0.0569173609517712,y:0.770826295934617};
data_arr[3][21]={x:0.0748386378309054,y:0.851990559548702};
data_arr[3][22]={x:0.0893406177402612,y:0.886800821906928};
data_arr[3][23]={x:0.115594471292347,y:0.923033346932114};
data_arr[3][24]={x:0.16740800609081,y:0.94170102575442};
data_arr[3][25]={x:0.227322988386894,y:0.94170102575442};
data_arr[3][26]={x:0.28480358684358,y:0.960746244818117};
data_arr[3][27]={x:0.380556882244545,y:0.94170102575442};
data_arr[3][28]={x:0.569173609517712,y:0.94170102575442};
data_arr[3][29]={x:1.1193117143059,y:0.94170102575442};
data_arr[3][30]={x:1.8144343483121,y:0.94170102575442};
data_arr[3][31]={x:3.13692038539279,y:0.960746244818117};
data_arr[3][32]={x:5.42332629088885,y:0.960746244818117};
data_arr[3][33]={x:8.51275099429801,y:0.960746244818117};

window.onload = function () {

var chart1 = new CanvasJS.Chart("chartContainer_2", {
 animationEnabled: false,
 zoomEnabled: true,

 title:{
     text: "Normalized Thermal Transient Impedance, Junction-to-Case",
    fontSize: 16,
    fontStyle: "normal",
    horizontalAlign: "center"
 },

 axisX: {
 logarithmic: true,
     title: "Square Wave Pulse Duration (s)",
		titleFontSize: 14
	},
	axisY: {
	logarithmic: true,
  title: "Normalized Transient Thermal Impedance",
		titleFontSize: 14
	},
    toolTip: {
		shared: true
	},
	legend: {
		cursor: "pointer",
		verticalAlign: "top",
		itemclick: toggleDataSeries
	},

	data: [
        {
		name: "Single Pulse",
		color: "#052689",
		type: "scatter",
		lineThickness: 4,
		markerSize: 4,
		showInLegend: true,
		dataPoints: [
{x:0.000101623232937243,y:0.032586932292754},
{x:0.000144823540623688,y:0.0390215348633906},
{x:0.000184388684427856,y:0.0431303128837853},
{x:0.000242446201708233,y:0.048635851273841},
{x:0.000345510729459222,y:0.0606189899349757},
{x:0.00045429991686721,y:0.0697394087111735},
{x:0.00061689292607757,y:0.0851990559548702},
{x:0.000772879677907017,y:0.094170102575442},
{x:0.001,y:0.110529514112602},
{x:0.00125285871378241,y:0.124638488578444},
{x:0.00147174364035974,y:0.140548458570921},
{x:0.00187381742286038,y:0.164964807409802},
{x:0.00246381668309456,y:0.186022388945322},
{x:0.00334561162465983,y:0.218338556525623},
{x:0.00432876128108306,y:0.25118864315095},
{x:0.005784126230175,y:0.283252605258997},
{x:0.00748386378309053,y:0.32586932292754},
{x:0.00984026950429283,y:0.374897930870418},
{x:0.0129386245826521,y:0.398107170553497},
{x:0.0149563346788184,y:0.422753251473378},
{x:0.0187381742286038,y:0.467267114748752},
{x:0.0231012970008316,y:0.5164680715397},
{x:0.0280254405033009,y:0.570849650019635},
{x:0.0356818655493467,y:0.630957344480193},
{x:0.0447043361775117,y:0.697394087111734},
{x:0.0542332629088885,y:0.740568469226243},
{x:0.0679466160122538,y:0.81854673070690},
{x:0.0893406177402612,y:0.904735724234929},
{x:0.113748075072293,y:0.904735724234929},
{x:0.154458278026823,y:0.94170102575442},
{x:0.203091762090473,y:0.94170102575442},
{x:0.313692038539278,y:0.94170102575442},
{x:0.461674262751667,y:0.94170102575442}
		]
	},
	 {
		name: "Duty Cycle = 0.1",
		color: "#000000",
		type: "scatter",
		lineThickness: 4,
		markerSize: 4,
		showInLegend: true,
		dataPoints: [
{x:0.000101623232937243,y:0.124638488578444},
{x:0.000151991108295293,y:0.129730914120867},
{x:0.000242446201708233,y:0.137762315823046},
{x:0.000362610653435665,y:0.149249554505182},
{x:0.000492388263170674,y:0.152268018309481},
{x:0.000647424024645891,y:0.164964807409802},
{x:0.000879135756244175,y:0.168301100786357},
{x:0.0011193117143059,y:0.193622811380895},
{x:0.00154458278026823,y:0.218338556525623},
{x:0.00223691947024827,y:0.246209240149462},
{x:0.00313692038539279,y:0.27213387683753},
{x:0.00405874352122658,y:0.300788251804309},
{x:0.00542332629088886,y:0.32586932292754},
{x:0.00701703828670383,y:0.346043291889247},
{x:0.00893406177402611,y:0.374897930870418},
{x:0.0108383965061256,y:0.382479969144438},
{x:0.0135789795061284,y:0.422753251473378},
{x:0.0164733989710987,y:0.458004310330155},
{x:0.0209738579187812,y:0.49619476030029},
{x:0.0267038221297861,y:0.548441657612101},
{x:0.0334561162465983,y:0.618449649643718},
{x:0.0425961776256052,y:0.670018750350958},
{x:0.0516757410491482,y:0.72588791356011},
{x:0.0626906535241184,y:0.802320383860095},
{x:0.0837677640068292,y:0.886800821906928},
{x:0.104949173070025,y:0.94170102575442},
{x:0.133620817933572,y:0.923033346932114},
{x:0.184388684427856,y:0.960746244818117},
{x:0.242446201708233,y:0.960746244818117},
{x:0.28480358684358,y:0.98017663960029},
{x:0.368496668996185,y:0.94170102575442},
{x:0.500380871637579,y:0.98017663960029},
{x:0.607039264789507,y:0.98017663960029},
{x:0.760534432499608,y:0.98017663960029}
		]
	},
	 {
		name: "Duty Cycle = 0.5",
		color: "#FF0000",
		type: "scatter",
		lineThickness: 4,
		markerSize: 4,
		showInLegend: true,
		dataPoints: [
{x:0.00011193117143059,y:0.48635851273841},
{x:0.00016740800609081,y:0.48635851273841},
{x:0.000231012970008316,y:0.48635851273841},
{x:0.000308681420057751,y:0.48635851273841},
{x:0.000412462638290135,y:0.48635851273841},
{x:0.000597342996521653,y:0.48635851273841},
{x:0.000851275099429802,y:0.49619476030029},
{x:0.00127319552908165,y:0.52691326305265},
{x:0.00184388684427856,y:0.51646807153977},
{x:0.00267038221297861,y:0.559533491673248},
{x:0.00368496668996185,y:0.582394669446952},
{x:0.00587801607227491,y:0.606189899349757},
{x:0.00851275099429802,y:0.643717998357411},
{x:0.0106652742614662,y:0.643717998357411},
{x:0.013148648598504,y:0.643717998357411},
{x:0.0184388684427856,y:0.643717998357411},
{x:0.0267038221297861,y:0.72588791356011},
{x:0.0351119173421513,y:0.72588791356011},
{x:0.0447043361775117,y:0.740568469226243},
{x:0.0569173609517712,y:0.770826295934617},
{x:0.0748386378309054,y:0.851990559548702},
{x:0.0893406177402612,y:0.886800821906928},
{x:0.115594471292347,y:0.923033346932114},
{x:0.16740800609081,y:0.94170102575442},
{x:0.227322988386894,y:0.94170102575442},
{x:0.28480358684358,y:0.960746244818117},
{x:0.380556882244545,y:0.94170102575442},
{x:0.569173609517712,y:0.94170102575442},
{x:1.1193117143059,y:0.94170102575442},
{x:1.8144343483121,y:0.94170102575442},
{x:3.13692038539279,y:0.960746244818117},
{x:5.42332629088885,y:0.960746244818117},
{x:8.51275099429801,y:0.960746244818117}
		]
	},
      ]
});
chart1.render();


totrec=33;
cnt_s=1;

var interval = setInterval(doChart, 2000);
function doChart() {
clearInterval(interval);
chart1.options.data.push({ name: "",  type: "spline",color: "#052689", showInLegend: false, lineThickness: 4, markerType: "none", dataPoints: [ data_arr[1][1] ]});
chart1.options.data.push({ name: "",  type: "spline",color: "#000000", showInLegend: false, lineThickness: 4, markerType: "none", dataPoints: [ data_arr[2][1] ]});
chart1.options.data.push({ name: "",  type: "spline",color: "#FF0000", showInLegend: false, lineThickness: 4, markerType: "none", dataPoints: [ data_arr[3][1] ]});
chart1.render();
interval1 = setInterval(doChart1,  8000/totrec); // Displaying total data data in 8 sec
}

function doChart1() {
cnt_s++;
dataString1= data_arr[1][cnt_s];
dataString2= data_arr[2][cnt_s];
dataString3= data_arr[3][cnt_s];
chart1.options.data[3].dataPoints.push(dataString1);
chart1.options.data[4].dataPoints.push(dataString2);
chart1.options.data[5].dataPoints.push(dataString3);
chart1.render();
if(cnt_s==totrec){
clearInterval(interval1);
interval2 = setInterval(doChart2,  2000);
cnt_s=1;
}
}

function doChart2(){
clearInterval(interval2);
chart1.data[5].remove();
chart1.data[4].remove();
chart1.data[3].remove();
interval = setInterval(doChart, 0);
}
};

   function toggleDataSeries(e) {
	if (typeof (e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
		e.dataSeries.visible = false;
	} else {
		e.dataSeries.visible = true;
	}
	e.chart.render();
}

</script>

 <script>

$(window).scroll(function(){
    if ($(window).scrollTop() >= 100) {
        $('.header').addClass('fixed-header');
        $('.header .hd').addClass('hdfixed');
    }
    else {
        $('.header').removeClass('fixed-header');
        $('.header .hd').removeClass('hdfixed');
    }
});

var simulatecnt=0;

function closerequest(){
$('.layer,.content').hide();
simulate(1);
}
function simulatess(s){
simulatecnt++;
manf="<?=$manf;?>";
part="<?=$part;?>";
vdd=$('#vdd').val();
vdd_step=$('#vdd_step').val();
vgs=$('#vgs').val();
vgs_step=$('#vgs_step').val();
if(vdd=="" || vgs=="" || vdd_step=="" || vgs_step==""){
alert("All fields require..");
return false;
}

$("#simulategraph").contents().find("body").html('');
if(simulatecnt==4){
simulatecnt=1;
$('.layer,.content').show();
return false;
}

$('#loadiframe span').html('Loading..');
$('#loadiframe').show();
loc="/dc-mosfet-id-vgs-vds-simulate.php?demo_sim=<?=$demo_sim;?>&step="+s+"&manf="+manf+"&part="+part+"&vdd="+vdd+"&vdd_step="+vdd_step+"&vgs="+vgs+"&vgs_step="+vgs_step+"&action=resimulate&fnname=<?=$fnname;?>&chnl=<?=$chnl;?>";
//alert(loc);
$('#simulategraph').attr('src', loc);
}

function processingComplete()
{
$('#loadiframe').hide();
}
<?//if($simulation==1){?>
simulate(1);
<?//}?>


 $('.additionalresources').click(function(){
 if($('#loadadditional').html()==""){
 $('#loadadditional').html('<center>loading..</center>');
  $.ajax({
    type: 'GET',
    url: '/datasheet_details.php?loadadditional=yes&partno=<?=$addpart;?>:<?=$addmanf;?>',
    success: function (data) {
    $('.additionalresources span').removeClass('fa-angle-double-down').addClass('fa-angle-double-up');
     $('#loadadditional').html(data);
 }
});
}else{
$('#loadadditional').toggle();
if($('#loadadditional').is(":hidden")){
$('.additionalresources span').removeClass('fa-angle-double-up').addClass('fa-angle-double-down');
}else{
$('.additionalresources span').removeClass('fa-angle-double-down').addClass('fa-angle-double-up');
}

}

});

$('.signupbtn').click(function(){

if($('#signupemail').val()==""){
$('.signupmsg').html('Please provide email<br />');
return false;
}
 $(this).attr('disabled','disabled');
 $('.signupmsg').html('<center>please wait..</center>');
  $.ajax({
    type: 'GET',
    url: '/datasheet_details.php?requestsignup=yes&email='+$('#signupemail').val(),
    success: function (data) {
    $('#signupemail').val('');
     $('.signupmsg').html(data);
     $('.signupbtn').removeAttr('disabled');
 }
});
});

$('.requestdemo').click(function(){

if($('#emailrequestdemo').val()==""){
$('.demomsg').html('Please provide email<br />');
return false;
}
 $(this).attr('disabled','disabled');
 $('.demomsg').html('<center>please wait..</center>');
  $.ajax({
    type: 'GET',
    url: '/datasheet_details.php?requestdemo=yes&email='+$('#emailrequestdemo').val(),
    success: function (data) {
    $('#emailrequestdemo').val('');
     $('.demomsg').html(data);
     $('.requestdemo').removeAttr('disabled');
 }
});
});


</script>
<?
function clean($string) {
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
   $string = str_replace('--', '-', $string); // Replaces all spaces with hyphens.
   $string = str_replace('--', '-', $string); // Replaces all spaces with hyphens.
   $string = str_replace('--', '-', $string); // Replaces all spaces with hyphens.
   $string = str_replace('--', '-', $string); // Replaces all spaces with hyphens.
   $string = str_replace('--', '-', $string); // Replaces all spaces with hyphens.

   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}
function manf_count($manf){
 if($manf=="infineon")
 return 1;
 else if($manf=="vishay")
 return 2;
 else if($manf=="onsemi")
 return 3;
 else if($manf=="st")
 return 4;
 else
 return 5;

}
?>
<?
function random_color_part() {
    return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
}

function random_color() {
    return random_color_part() . random_color_part() . random_color_part();
}
function gettc($str,$tc){

if($tc=="")
return $str;

$str=str_replace(" ","",$str);
$tcstr=explode(",",$tc);
$dstr="";

foreach($tcstr as $tcv){
$check=explode("=",$tcv);
$tcvstr=explode("||",strtolower($check[0]));
foreach($tcvstr as $v){
$v=trim($v);
if(strpos($str,$v)!==false){
  $cstr=substr($str,strpos($str,$v),25);

if(strpos($cstr,',')!==false)
$cstr=substr($cstr,0,strpos($cstr,','));
else
$cstr=substr($cstr,0,strlen($cstr));
$cstr=trim(substr($cstr,strpos($cstr,'=')+1,strlen($cstr)));
if(strpos($cstr,'=')!==false)
$cstr=trim(substr($cstr,0,strpos($cstr,'=')));

if(strpos($cstr,' ')!==false)
$cstr=trim(substr($cstr,0,strpos($cstr,' ')));

$sym=$check[1];
if(strpos($check[1],'(')!==false){
$sym=substr($check[1],0,strpos($check[1],'('));
$unit=trim(str_replace(")","",substr($check[1],strpos($check[1],'(')+1,10)));
}

$cstr1=(float) filter_var( $cstr, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
$cstr=trim(substr($cstr,strpos($cstr,$cstr1)+strlen($cstr1),10));
$cstr=trim(substr($cstr,0,strpos(strtolower($cstr),strtolower($unit))+strlen($unit)));
//echo "==".$cstr1."++".$cstr."(".$unit.")<br />";
$unit="";
if($cstr!="" && abs($cstr1)>0)
$dstr.=$sym."=".$cstr1.$cstr.", ";
break;
}
}
}
$dstr=utf8_decode($dstr);
$dstr=str_replace("?c","&deg;c",$dstr);
$dstr=str_replace("/Î¼s","/µs",$dstr);
//exit;

if($dstr!="")
return substr($dstr,0,strlen($dstr)-2);
}
?>

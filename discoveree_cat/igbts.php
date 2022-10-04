<?php include "../includes/config.inc.php";?>
<?if($_REQUEST['autosearch']=="true"){?>
<style>
#parts-list{z-index:1000;float:left;list-style:none;margin-top:-1px;margin-left:-1px;padding:0;width:100%;position: absolute;}
#parts-list li{padding: 5px; background: #f0f0f0; border-bottom: #bbb9b9 1px solid;}
#parts-list li:hover{background:#3C8DBC;cursor: pointer;}
</style>
<ul id="parts-list">
<?
$result=mysql_query("SELECT partno,manf FROM srai_others.discoveree_live_12_v2 WHERE  partno like '".$_REQUEST['keyword']."%' ORDER BY partno LIMIT 10");
while($row=mysql_fetch_array($result)){
$ul=encrypt('igbt/'.strtolower($row['manf']));
?>
<li onClick="selectPart('<?=$ul."/".strtolower(str_replace('.html','',$row['partno']));?>','<?=strtoupper(str_replace('.html','',$row['partno']));?>');"><table width='100%'><tr><td><?=str_replace(strtoupper($_REQUEST['keyword']),'<b>'.strtoupper($_REQUEST['keyword']).'</b>',strtoupper($row[0]));?></td><td align='right'><b><?=manflabel($row['manf']);?></b></td></tr></table></li>
<?}?>
</ul>
<?
exit;
}
?>
<?include("../header.php");?>
<style>
    #services h3{
        margin-top: 30px;
    }
      .box {
  padding: 110px;
  border-radius: 5px;
  background-color: #E8EAED;
  box-shadow: 0 8px 6px -6px black;
   }

</style>

<section id="hero-area">

<div class="container">
    
    <div class="row">
        <div class="col-md-12">

<br />
     <br /><br />
       <div class="row box" style="padding:70px;padding-top: 120px;padding-bottom: 120px;">
<h2 style="color:#000;text-align:center;margin:0px;">Browse Power IGBTs</h2>
<br/>
<p style="color:#000;text-align:center;padding:0px;">Just type part number and see details</p>
<br />

      <div class="col-xs-12" style="padding:0px;padding-right:0px;">
        <input type="text" id="searchTxt" autocomplete="off" placeholder="Enter igbt part number" class="form-control" name="searchtxt" required style='text-align:left;padding:25px!important;'>
        <div id="suggesstion-box1"></div>

      <br /><br />
      </div>


</div>
<div style='clear:both;'></div>
<br /><br /><br /><br /><br /><br /><br /><br />
    </div>

    </div>
</div>
</section>
<?include("../footer.php");?>
<script>
$(document).ready(function(){
$(document).click(function(){
  $("#suggesstion-box1").hide();
  $("#searchTxt").css({ 'background-color': '#ffffff' });
     $("#searchTxt").css('background-image', 'none');
});
	$("#searchTxt").keyup(function(){
	key=1;
		$.ajax({
		type: "POST",
		url: "igbts.php",
		data:'autosearch=true&c='+key+'&keyword='+$(this).val(),
		beforeSend: function(){
			$("#searchTxt").css("background","url(LoaderIcon.gif) right no-repeat");
		},
		success: function(data){
		$("#searchTxt").css({ 'background-color': '#ffffff' });
     $("#searchTxt").css('background-image', 'none');
			$("#suggesstion-box"+key).show();
			$("#suggesstion-box"+key).html(data);
			$("#partno"+key).css("background","#FFF");
		}
		});
	});
});
function selectPart(str,p){
if(str!=""){
$("#searchTxt").val(p);
location.href='https://www.discoveree.io/discoveree_cat/details_datasheet.php?id=12&url='+str;
}
}
</script>


<?php include "../includes/config.inc.php";?>
<?php
if($_REQUEST['discoveree_cat_id']=="" && $_REQUEST['id']==""){

   header("Location: /");
exit;
}

if($_SESSION['user_id']==""){
$_SESSION['lasturl'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] .  $_SERVER['REQUEST_URI'];

   header("Location: ../login.php");
exit;
}
mysql_query("SET NAMES utf8");

if($_SESSION['expired_user']==1 && $_COOKIE['expired_user_search_compare']==1){
header("Location: https://www.discoveree.io/request_upgrade.php");
exit;
}
if($_SESSION['expired_user']==1){
$expire=time()+60*60*24;//however long you want
setcookie('expired_user_search_compare', 1, $expire,'/');
}

if($_REQUEST['autosearch']=="true"){?>
<style>
#parts-list{z-index:1000;float:left;list-style:none;margin-top:-1px;margin-left:-1px;padding:0;width:95%;position: absolute;}
#parts-list li{padding: 5px; background: #f0f0f0; border-bottom: #bbb9b9 1px solid;}
#parts-list li:hover{background:#3C8DBC;cursor: pointer;}
</style>
<ul id="parts-list">
<?
$result=mysql_query("SELECT partno,manf FROM srai_others.discoveree_live_".$_REQUEST['discoveree_cat_id']."_v2 WHERE 1 AND partno like '".$_REQUEST['keyword']."%' ORDER BY partno LIMIT 10");
while($row=mysql_fetch_array($result)){?>
<li onClick="selectPart('<?=strtoupper($row[0]);?>','<?=$_REQUEST['c'];?>','<?=$row[1];?>');"><?=str_replace(strtoupper($_REQUEST['keyword']),'<b>'.strtoupper($_REQUEST['keyword']).'</b>',strtoupper($row[0]));?></li>
<?}?>
</ul>
<?
exit;
}

if($_REQUEST['id']==1){
$diode_cond=0;
$_REQUEST['discoveree_cat_id']=27;
$dss=mysql_query("SELECT * FROM srai_others.dashboard_category_order WHERE NOT catname LIKE '%zener%' AND  NOT catname LIKE '%tvs%' AND  dashboard_setting_id=126 ORDER BY catorder");
while($drow=mysql_fetch_array($dss)){
$othercat[trim($drow['discoveree_cat_id'])]=ucwords(strtolower(trim(substr(trim($drow['catname']),strpos(trim($drow['catname']),'-')+1,strlen(trim($drow['catname']))))));
$diode_cond=1;
}
}

?>
<? include("../header.php");?>

<section id="services">
<div class="container">

<?if(strpos($_SESSION['page_access'],'comparessss')!==false){?>
<br /><br /><br /><br />
<div class="alert text-left alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                <h4><i class="icon fa fa-ban"></i> <b>Alert!</b></h4>
                You do not have permission to access this page.. Please consult to Administrator at info@discoveree.io
              </div>
<?}else{?>
<div class="row">
    <center><h1 style="margin:0px;padding:0px;">Search Or Compare Parts</h1> </center>
<br />

<?
 if($_REQUEST['id']==1){
?>
<div class="col-md-12">
<label>Select Category*</label>
<br />
<select required name="discoveree_cat_id" id="discoveree_cat_id"  class="form-control" style="width:94%;">
<option value="">--Select Category--</option>
<? foreach($othercat as $c=>$v){?>
<option value="<?=$c;?>"><?=$v;?></option>
<?}?>
</select>
</div>
<?
}
 ?>
 
<div class="col-md-12" style="background-color:#FFF;">
    <h4>Type Part Number To Compare</h4>

    <div class="col-md-4" style="width:19%;padding-left: 0px;">
    <input type="hidden" name="manf1" id="manf1" /><input type="text" autocomplete="off" name="partno1" class="parts" cnt="1" id="partno1" placeholder="PN#1" style="padding:4px;width:100%;" value="<?=$_REQUEST['partno1'];?>" /><div id="suggesstion-box1"></div>
    </div>
    <div class="col-md-4" style="width:19%;padding-left: 0px;">
        <input type="hidden" name="manf2" id="manf2" /><input type="text" autocomplete="off" name="partno2" class="parts" cnt="2" id="partno2" placeholder="PN#2" style="padding:4px;width:100%;" value="<?=$_REQUEST['partno2'];?>" /><div id="suggesstion-box2"></div>
</div>
    <div class="col-md-4" style="width:19%;padding-left: 0px;">
            <input type="hidden" name="manf3" id="manf3" /><input type="text" autocomplete="off" name="partno3" class="parts" cnt="3" id="partno3" placeholder="PN#3" style="padding:4px;width:100%;" value="<?=$_REQUEST['partno3'];?>" /><div id="suggesstion-box3"></div>
</div>
    <div class="col-md-4" style="width:19%;padding-left: 0px;">
                <input type="hidden" name="manf4" id="manf4" /><input type="text" autocomplete="off" name="partno4" class="parts" cnt="4" id="partno4" placeholder="PN#4" style="padding:4px;width:100%;" value="<?=$_REQUEST['partno4'];?>" /><div id="suggesstion-box4"></div>
</div>
    <div class="col-md-4" style="width:19%;padding-left: 0px;">
                    <input type="hidden" name="manf5" id="manf5" /><input type="text" autocomplete="off" name="partno5" class="parts" cnt="5" id="partno5" placeholder="PN#5" style="padding:4px;width:100%;" value="<?=$_REQUEST['partno5'];?>" /><div id="suggesstion-box5"></div>
</div>
 <br /> <br />
   <div class="col-md-4" style="width:19%;padding-left: 0px;">
    <input type="hidden" name="manf6" id="manf6" /><input type="text" autocomplete="off" name="partno6" class="parts" cnt="6" id="partno6" placeholder="PN#6" style="padding:4px;width:100%;" value="<?=$_REQUEST['partno6'];?>" /><div id="suggesstion-box6"></div>
    </div>
    <div class="col-md-4" style="width:19%;padding-left: 0px;">
        <input type="hidden" name="manf7" id="manf7" /><input type="text" autocomplete="off" name="partno7" class="parts" cnt="7" id="partno7" placeholder="PN#7" style="padding:4px;width:100%;" value="<?=$_REQUEST['partno7'];?>" /><div id="suggesstion-box7"></div>
</div>
    <div class="col-md-4" style="width:19%;padding-left: 0px;">
            <input type="hidden" name="manf8" id="manf8" /><input type="text" autocomplete="off" name="partno8" class="parts" cnt="8" id="partno8" placeholder="PN#8" style="padding:4px;width:100%;" value="<?=$_REQUEST['partno8'];?>" /><div id="suggesstion-box8"></div>
</div>
    <div class="col-md-4" style="width:19%;padding-left: 0px;">
                <input type="hidden" name="manf9" id="manf9" /><input type="text" autocomplete="off" name="partno9" class="parts" cnt="9" id="partno9" placeholder="PN#9" style="padding:4px;width:100%;" value="<?=$_REQUEST['partno9'];?>" /><div id="suggesstion-box9"></div>
</div>
    <div class="col-md-4" style="width:19%;padding-left: 0px;">
                    <input type="hidden" name="manf10" id="manf10" /><input type="text" autocomplete="off" name="partno10" class="parts" cnt="10" id="partno10" placeholder="PN#10" style="padding:4px;width:100%;" value="<?=$_REQUEST['partno10'];?>" /><div id="suggesstion-box10"></div>
</div>

   <br /> <br />

 <br /> <br />
 <input type="hidden" name="actiontype" value="comparepart" />
 <input type="button" id="compare"  value="Compare" class="btn btn-primary" />

<br /><br />



    <?}?>
</div>
 </section>
<!-- Service Section End -->
<?include("../footer.php");?>
<!-- jQuery 2.2.3 -->
<script src="../admin/plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/app.min.js"></script>

      <div class="modal modal-danger fade" id="modal-comingsoon">

          <div class="modal-dialog"  style="width:500px;height:600px;overflow:auto;">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Page Coming Soon</h4>
              </div>
              <div class="modal-body">

               </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
<script>

  
  // AJAX call for autocomplete
$(document).ready(function(){

$('#compare').click(function(){
str="";
	discoveree_cat_id=<?=$_REQUEST['discoveree_cat_id'];?>;
     <? if($_REQUEST['id']==1){?>
     discoveree_cat_id=$('#discoveree_cat_id').val();
     <?}?>
     
for(a=1;a<=10;a++){
if($('#partno'+a).val()!="" && $('#manf'+a).val()!="")
str=str+$('#partno'+a).val()+"@"+$('#manf'+a).val()+"@"+discoveree_cat_id+"^";
}
if(str!="")
location.href='compare.php?url='+str.toLowerCase();
else
alert('Please enter any one part number');
});

$(document).click(function(){
  $("#suggesstion-box1,#suggesstion-box2,#suggesstion-box3,#suggesstion-box4,#suggesstion-box5,#suggesstion-box6,#suggesstion-box7,#suggesstion-box8,#suggesstion-box9,#suggesstion-box10").hide();
});
	$(".parts").keyup(function(e){
	
	if(e.which == 27){
        $("#suggesstion-box1,#suggesstion-box2,#suggesstion-box3,#suggesstion-box4,#suggesstion-box5,#suggesstion-box6").hide();
    return false;
    }
    
	key=$(this).attr('cnt');
	
		discoveree_cat_id=<?=$_REQUEST['discoveree_cat_id'];?>;
     <? if($_REQUEST['id']==1){?>
     discoveree_cat_id=$('#discoveree_cat_id').val();
     <?}?>
     
		$.ajax({
		type: "POST",
		url: "search_compare_parts.php",
		data:'autosearch=true&discoveree_cat_id='+discoveree_cat_id+'&c='+key+'&keyword='+$(this).val(),
		beforeSend: function(){
			$("#partno"+key).css("background","url(LoaderIcon.gif) no-repeat 165px");
		},
		success: function(data){
			$("#suggesstion-box"+key).show();
			$("#suggesstion-box"+key).html(data);
			$("#partno"+key).css("background","#FFF");
		}
		});
	});
});

function selectPart(val,c,o) {
$("#partno"+c).val(val);
$("#manf"+c).val(o);
$("#suggesstion-box"+c).hide();
}
</script>

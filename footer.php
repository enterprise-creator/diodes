<? if($_SESSION['user_name']=="dnp1976"){?>
<div class='overlay_bbg'><div class="bbg"><b>Loading..<span id="dashboard_name"></span></b><br /><br />Please wait for some time to load data in dashboard. It may take some time depend on volume.</div></div>
  <style>
  .bbg {
    text-align:center;
    color:#000;
    width:50%;
    height:150px;
    background-color:#FFF;
    padding:30px;
    opacity: 0.9;
}
  .overlay_bbg {
    position: fixed;
    width: 100%;
    height: 100%;
    padding-top:10%;
    padding-left:30%;
    left: 0;
    top: 0;
    display:none;
    background: rgba(51,51,51,0.9);
    z-index: 10000000;
  }
  </style>
<?}?>
<?
  $path_pref=SITE_SUB_PATH;

 $dashboard_folder=explode("/",$_SERVER['SCRIPT_NAME']);
  $d_folder=str_replace("-test","",str_replace(".php","",$dashboard_folder[count($dashboard_folder)-1]));
  if($_REQUEST['show']!="")
  $d_folder=$d_folder."-".$_REQUEST['show'];
  else if($_REQUEST['view']!="")
  $d_folder=$d_folder."-".$_REQUEST['view'];
  
$files = array_filter(glob("discoveree_walkthrough/".$d_folder.'/*'), 'is_file');
 $check_walk_through=mysql_fetch_array(mysql_query("SELECT username FROM discoveree_walk_through WHERE page='".$d_folder."' AND username='".$_SESSION['user_name']."'"));
 if(count($files)>0 && $_SESSION['walk_through']==1){
  echo '<center><a href="#" onclick="jQuery(\'#modal-walk_through\').modal(\'show\');">Show user guide</a></center>';
 }
  ?>
  <?if(strpos($_SERVER['REQUEST_URI'],"index")===false && $_SERVER['REQUEST_URI']!="/"){?>
   <br />
   <?}?>
     <div class="banner" style="background-color:#000;margin-top:0px;padding-bottom:20px;margin-bottom:0px;min-height:50px;">
          <div class="container"><br />
          <p style="color:#FFF;">DiscoverEE.io &copy; <?=date("Y");?> All rights reserved.</p>
          </div>
          </div>

        <!-- Bootstrap JS -->
        <script src="https://www.discoveree.io/assets/js/bootstrap.min.js"></script>

            <!-- Smooth Scroll -->
                    <!-- Smooth Scroll -->
        <script src="https://www.discoveree.io/assets/js/smooth-scroll.js"></script>
        <script src="https://www.discoveree.io/assets/js/lightbox.min.js"></script>

        <!-- All JS plugin Triggers -->
        <script src="https://www.discoveree.io/assets/js/main.js"></script>


           <script src="https://www.discoveree.io/assets/zebra_tooltips.min.js"></script>
<link rel="stylesheet" href="https://www.discoveree.io/assets/css/bubble/zebra_tooltips.css" type="text/css">
<script>
$(document).ready(function() {

    new $.Zebra_Tooltips($('.zebra_tooltips'), {
        position:           'center',

    });

        new $.Zebra_Tooltips($('.knowledgeispower'), {
        position:           'center',
        vertical_alignment: 'top',
        max_width:  520,
        opacity: 1
    });

});
</script>
  <script>
$(function(){
    $('.dropdown').hover(function() {
        $(this).addClass('open');
    },
    function() {
        $(this).removeClass('open');
    });
});
</script>
<?

if($d_folder=="compare")
$survey_page="Compare Part Numbers";
else if($d_folder=="recent-activity")
$survey_page="Recent Manufacturer Activity";
else if($d_folder=="packagereport")
$survey_page="Competitive Intelligence";
else if($d_folder=="advancesearch")
$survey_page="Advance Search";
else if($d_folder=="easysearch")
$survey_page="Easy Search (Text Based)";
else if($d_folder=="dashboard-package-trends")
$survey_page="Dashboard: Package Trends";
else if($d_folder=="discover" && $_REQUEST['view']=="rcoss")
$survey_page="Dashboard: RDS(on)*Coss – BVDSS";
else if($d_folder=="discover" && $_REQUEST['view']=="rqg")
$survey_page="Dashboard: RDS(on)*QG – BVDSS";
else if($d_folder=="discover" && $_REQUEST['show']=="rdson-vds-monthly")
$survey_page="Dashboard: RDS(on) – BVDSS (Monthly Activity)";
else if($d_folder=="dashboard-pl")
$survey_page="Dashboard: Power Loss - RDS(on)";
else if($d_folder=="dashboard-1")
$survey_page="Dashboard: RDS(on) - Package";
else if($d_folder=="discover-vds")
$survey_page="Dashboard: RDS(on) - BVDSS (Jitter)";
else if($d_folder=="discover")
$survey_page="Dashboard: RDS(on) - BVDSS";
else if($d_folder=="trends")
$survey_page="Industry Trends";
else if($d_folder=="cross_reference")
$survey_page="Cross Reference";
else if($d_folder=="process-graph-report")
$survey_page="Comparison Page";

if($_SESSION['survey_last_page']!=$survey_page)
$_SESSION[$_SESSION['survey_last_page']]=$_SESSION[$_SESSION['survey_last_page']]+1;


$_SESSION['survey_last_page']=$survey_page;

?>
       <div class="modal fade" id="modal-survey">
          <div class="modal-dialog"  style="width:650px;height:520px;overflow:hidden;">
            <div class="modal-content" style="height:100%;">
               <iframe src="https://www.fet.discoveree.io/survey_user.php?page=<?=$survey_page;?>" frameborder=0 width="100%" height="88%" scrolling="no"></iframe>
              <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
<? if($_SESSION['survey_feedback']=="Yes" && $_SESSION[$survey_page]>=1 && $survey_page!="" && $_SESSION['user_id']!="" && strpos($_SESSION['feedback_status'],$survey_page)===false){?>
<script>
jQuery('#modal-survey').modal('show');
</script>
<?}else if($_SESSION['survey_feedback']=="Yes" && strpos($survey_page,'Dashboard')!==false && $_SESSION['user_id']!="" && strpos($_SESSION['feedback_status'],$survey_page)===false){?>
<script>
var surveyInterval = setInterval(function () {
            jQuery('#modal-survey').modal('show');
            clearInterval(surveyInterval);
},60000);
</script>
<?}?>
       <div class="modal fade" id="modal-walk_through">
          <div class="modal-dialog"  style="width:1150px;height:660px;overflow:hidden;">
            <div class="modal-content" style="height:100%;">
               <iframe src="https://www.fet.discoveree.io/walk_through.php?page=<?=$d_folder;?>" frameborder=0 width="100%" height="90%"></iframe>
              <div class="modal-footer">
               <? if(count($files)>0 && $_SESSION['walk_through']==1 && $check_walk_through[0]==""){?><button type="button" class="btn btn-primary pull-left" onclick="notshow()" data-dismiss="modal">Do not show again</button>  &nbsp; <?}?><button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
   <script>
    <? if(count($files)>0 && $_SESSION['walk_through']==1 && $check_walk_through[0]==""){?>
  jQuery('#modal-walk_through').modal('show');
  <?}?>
  function notshow(){

$.ajax({
    url: 'login.php?action=walk_through&page=<?=$d_folder;?>',
    async: false,
    success: function(data) {
    }
});

  }
  </script>
<div class="modal modal-danger fade" id="modal-termsconditions">
 <div class="modal-dialog"  style="width:850px;height:620px;">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">WEBSITE TERMS AND CONDITIONS</h4>

              </div>
              <div class="modal-body">
               <div id="tscontent" style="height:500px;overflow:auto;"></div>

              </div>
               <div class="modal-footer">
                 <button type="button" class="btn btn-primary pull-left" data-dismiss="modal">Close</button>
              </div>
              </div>
</div></div>
<div class="modal modal-danger fade" id="modal-reporterror">

          <div class="modal-dialog"  style="width:500px;height:600px;overflow:auto;">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="pnoerror"></h4>

              </div>
              <div class="modal-body">
               <form id="errorform">
               <span style="color:#FF0000;">Let us know how we are doing?:</span><br />
               <input type="checkbox" name="errortype" value="Excellent" /> Excellent<br />
               <input type="checkbox" name="errortype" value="Could Improve" /> Could Improve<br />
               <input type="checkbox" name="errortype" value="Flag For Review" /> Flag For Review<br /><br />
               <input type="email" name="guest_email" value="" placeholder="Provide Email For Updates" style="width:70%;margin-bottom:10px;" /><br />
               <textarea name="errormsg" rows="5" style="width:70%;" id="errormsg" placeholder="Comments & Suggesstions"></textarea><br />
               <input type="hidden" name="pnoer" id="pnoer" />
               <span style="color:#FF0000;" id="statusmsg"></span>
               </form>
               </div>
              <div class="modal-footer">
                <button type="button" id="errorreporting" class="btn btn-primary pull-left" ddata-dismiss="modal">Submit</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>

        <div class="modal modal-danger fade" id="modal-savesearch">
          <div class="modal-dialog"  style="width:600px;margin-top:250px;">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Save Your Search</h4>
                <span style="color:#FF0000;" id="statussavemsg"></span>
              </div>
              <div class="modal-body">
               <form id="savesearchform">
               <input type="hidden" name="pageurl" value="<?=$_SERVER['REQUEST_URI'];?>" />
               <input type="hidden" name="iframesrc" id="iframesrc" value="" />
               <input type="text" name="savesearchtitle" id="savesearchtitle" placeholder="Enter Save Title" style="width:99%;" />
               <br /><input type="checkbox" name="savedefault" value="1" /> Make it default
               </form>
               </div>
              <div class="modal-footer">
                <button type="button" id="savesearchbtn" class="btn btn-primary pull-left" ddata-dismiss="modal">Save</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>

                <div class="modal modal-danger fade" id="modal-mysavedsearch">
          <div class="modal-dialog"  style="width:900px;">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">My Saved Search</h4>
                <span style="color:#FF0000;" id="statussavedmsg"></span>
              </div>
              <div class="modal-body">
               <div id="savedsearchform" style="width:100%;height:250px;overflow:auto;">

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
<script>
jQuery('#savesearchbtn').click(function(){
if(jQuery('#savesearchtitle').val()==""){
  jQuery('#statussavemsg').html('<br />Please enter save title.');
return false;
}
<? if(strpos($_SERVER['REQUEST_URI'],'discoveree_cat')===false){?>
iframesrc=jQuery('#chart').contents().get(0).location.href;
jQuery('#iframesrc').val(iframesrc);
<?}?>

$.ajax({
        url: '<?=$path_pref;?>extraactions.php',
        type: 'post',
        data: jQuery('form#savesearchform').serialize(),
        success: function(data) {
        jQuery('#savesearchform')[0].reset();
                   jQuery('#modal-savesearch').modal('hide');
                   jQuery('#savesearch_div').hide();

                 }
});
    
});

jQuery('#errorreporting').click(function(){
if (jQuery('input[name="errortype"]:checked').length == 0) {
         jQuery('#statusmsg').html('<br />Please select issue type.');
return false;
}
jQuery('#statusmsg').html('Please wait..');
$.ajax({
        url: '<?=$path_pref;?>errorreporting.php',
        type: 'post',
        dataType: 'json',
        data: jQuery('form#errorform').serialize(),
        success: function(data) {
        jQuery('#errorform')[0].reset();
                   jQuery('#statusmsg').html('<br />Thank you for reporting this issue to us, it helps us get better.');
                 }
    });
//jQuery('#modal-reporterror').modal('hide');
});
<? if($_SESSION['user_name']=="dnp1976"){?>
$('.href').click(function(){
if($(this).text().indexOf('Dashboard')>=0){
$("#dashboard_name").text($(this).text());
$(".overlay_bbg").show();
}
});
<?}?>
</script>
<?if($_SESSION['user_name']=="111"){?>
<script type="text/javascript">

var $zoho=$zoho || {};$zoho.salesiq = $zoho.salesiq ||

{widgetcode:"fccbff555ee56085b7570e5fd43bed7b433373d5a3c676fd37786bbfec919cc0", values:{},ready:function(){}};

var d=document;s=d.createElement("script");s.type="text/javascript";s.id="zsiqscript";s.defer=true;

s.src="https://salesiq.zoho.com/widget";t=d.getElementsByTagName("script")[0];t.parentNode.insertBefore(s,t);d.write("<div id='zsiqwidget'></div>");

</script>
<?}?>
    </body>
    </html>

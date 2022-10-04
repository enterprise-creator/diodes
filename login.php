<?php include "includes/config.inc.php";?>
<?php
if($_REQUEST['action']=="walk_through"){
mysql_query("INSERT INTO discoveree_walk_through SET username='".$_SESSION['user_name']."',page='".$_REQUEST['page']."'");
exit;
}

if($_REQUEST['apply']!=""){


$message = 'Email: '.$_REQUEST['email'].'<br />';

 $subject = 'Apply as an expert [discoverEE.io]';
 if($_REQUEST['apply']=="Become Expert"){
$message = 'Name: '.$_REQUEST['name'].'<br />';
$message .= 'Email: '.$_REQUEST['email'].'<br />';
$message .= 'LinkedIn Profile: '.$_REQUEST['linkedInprofile'].'<br />';
$message .= 'Specialization: '.$_REQUEST['specialization'].'<br />';
 $subject = 'Apply as Become an expert [discoverEE.io]';
if($_FILES["resume"]["name"]!=""){
$target_dir = "resume-become-an-expert/";
$target_file = $target_dir . time().basename($_FILES["resume"]["name"]);
$uploadOk = 1;
$FileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
if($FileType == "doc" || $FileType == "docx" || $FileType == "pdf" ) {
move_uploaded_file($_FILES["resume"]["tmp_name"], $target_file);
$message .= 'Resume: https://www.discoveree.io/'.$target_file.'<br />';
}
}
$message .= 'Yourself: '.$_REQUEST['yourself'].'<br />';
}

  mysql_query("INSERT INTO expert_become_expert SET
  type='".$_REQUEST['apply']."',
  name='".addslashes($_REQUEST['name'])."',
  email='".$_REQUEST['email']."',
  linkedInprofile='".$_REQUEST['linkedInprofile']."',
  specialization='".addslashes($_REQUEST['specialization'])."',
  resume='".$target_file."',
  yourself='".addslashes($_REQUEST['yourself'])."'");
 $id=mysql_insert_id();

$from = 'DiscoverEE<noreply@discoveree.io>';

// To send HTML mail, the Content-type header must be set
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

// Create email headers
$headers .= 'From: '.$from."\r\n".
    'X-Mailer: PHP/' . phpversion();




// Sending email
mail("info@discoveree.io", $subject, $message, $headers, "-fnoreply@discoveree.io");

header("Location: login.php?msgapply=Thank you for your interest.");
exit;

}
if($_COOKIE['userid']!=""){
 $_SESSION['user_id']=$_COOKIE['userid'];
   $_SESSION['user_name']=$_COOKIE['username'];
   $_SESSION['user_type']=$_COOKIE['usertype'];
   $_SESSION['page_access']=$_COOKIE['page_access'];
   $_SESSION['page_jitter_access']=$_COOKIE['page_jitter_access'];
   $_SESSION['opage_access']=$_COOKIE['opage_access'];
   $_SESSION['material_access']=$_COOKIE['material_access'];
   $_SESSION['pricing_access']=$_COOKIE['pricing_access'];
   $_SESSION['access_limit']=$_COOKIE['access_limit'];
   $_SESSION['show_part']=$_COOKIE['show_part'];
   $_SESSION['landing_page']=$_COOKIE['landing_page'];
   $_SESSION['link_detail_page']=$_COOKIE['link_detail_page'];
   $_SESSION['showgraph']=$_COOKIE['showgraph'];
   $_SESSION['credit']=$_COOKIE['credit'];
   $_SESSION['credituse']=$_COOKIE['credituse'];
   $_SESSION['showkp']=$_COOKIE['showkp'];
   $_SESSION['manf_access']=$_COOKIE['manf_access'];
   $_SESSION['user_company']=$_COOKIE['user_company'];
   $_SESSION['d_manf']=$_COOKIE['d_manf'];
   $_SESSION['d_vds']=$_COOKIE['d_vds'];
   $_SESSION['download_parts']=$_COOKIE['download_parts'];

   $_SESSION['walk_through']=$_COOKIE['walk_through'];
   $_SESSION['show_vds']=$_COOKIE['show_vds'];
   $_SESSION['show_qualification']=$_COOKIE['show_qualification'];
   $_SESSION['user_first_name']=$_COOKIE['user_first_name'];
   $_SESSION['admin_status']=$_COOKIE['admin_status'];

   $_SESSION['reg_type']=$_COOKIE['reg_type'];
   $_SESSION['survey_completion']=$_COOKIE['survey_completion'];



  mysql_query("UPDATE admin SET logged=1 WHERE id='".$_SESSION['user_id']."'");

mysql_query("UPDATE admin SET last_login='".date("Y-m-d H:i:s")."' WHERE username='".$_SESSION['user_name']."'");
if(strpos($_SESSION['landing_page'],'.php')===false)
    header("Location: ".$_SESSION['landing_page'].".php");
    else
        header("Location: ".$_SESSION['landing_page']."");
exit;
}

 if($_SESSION['auser_type']=="Super Admin" && $_REQUEST['username']!="" && $_REQUEST['type']=="admin"){
 $_REQUEST['username']=decrypt($_REQUEST['username']);
 }
 
  if($_REQUEST['username']!=""){
        $_REQUEST['username']=trim($_REQUEST['username']);
      $_REQUEST['password']=trim($_REQUEST['password']);
    if(strpos($_REQUEST['username'],"'")!==false || strpos($_REQUEST['username'],'"')!==false || strpos($_REQUEST['username'],"--")!==false){
   header("Location: login.php?error=Username mismatch..Please contact admin");
   exit;
  }

  if($_SESSION['auser_type']=="Super Admin" && $_REQUEST['type']=="admin") // access from admin panel
  $results=mysql_query("SELECT id,username,type,login_from,login_to,page_access,accesslimit,logged,allowtoshowpart,landingurl,hyperlink_detail_page_in_partno,showgraph,credit,showkp,credituse,manf_access,company,d_manf,d_vds,material_access,opage_access,download_parts,pricing_access,walk_through,vds,qualification,page_jitter_access,name,admin_status,reg_type,survey_completion,survey_feedback,survey_from,survey_to,kp2_igbt_price,kp2_diode_price,kp2_mcu_price,kp2_mosfet_price,xref_report,kp2_credit,kp2_credit_used,status,kp2_gdrive_price,kp2_leddrive_price,kp2_thyristor_price,kp2_relay_price,package_plan,package_plan_user FROM admin WHERE username='".$_REQUEST['username']."' ");
  else
  $results=mysql_query("SELECT id,username,type,login_from,login_to,page_access,accesslimit,logged,allowtoshowpart,landingurl,hyperlink_detail_page_in_partno,showgraph,credit,showkp,credituse,manf_access,company,d_manf,d_vds,material_access,opage_access,download_parts,pricing_access,walk_through,vds,qualification,page_jitter_access,name,admin_status,reg_type,survey_completion,survey_feedback,survey_from,survey_to,kp2_igbt_price,kp2_diode_price,kp2_mcu_price,kp2_mosfet_price,xref_report,kp2_credit,kp2_credit_used,status,kp2_gdrive_price,kp2_leddrive_price,kp2_thyristor_price,kp2_relay_price,package_plan,package_plan_user FROM admin WHERE username='".$_REQUEST['username']."'  AND password='".md5($_REQUEST['password'])."' ");

  $row=mysql_fetch_array($results);
  if($row[0]>0){




  $survey_results=mysql_query("SELECT page FROM user_feedback WHERE user_id=".$row[0]);
  while($survey_row=mysql_fetch_array($survey_results))
  $_SESSION['feedback_status'].=$survey_row[0].",";
  if($_SESSION['auser_type']!="Super Admin" && $_REQUEST['type']!="admin")
  mysql_query("UPDATE admin SET logged=1 WHERE id='".$row[0]."'");
   if($row[9]=="")
   $row[9]="discover";

   $_SESSION['user_id']=$row[0];
   $_SESSION['user_name']=$row[1];
   $_SESSION['user_type']=$row[2];
    $_SESSION['page_access']=$row[5];
    $_SESSION['material_access']=$row[19];
    $_SESSION['access_limit']=$row[6];
     $_SESSION['show_part']=$row[8];
     $_SESSION['landing_page']=$row[9];
     $_SESSION['link_detail_page']=$row[10];
     $_SESSION['showgraph']=$row[11];
     $_SESSION['credit']=$row[12];
     $_SESSION['showkp']=$row[13];
     $_SESSION['credituse']=$row[14];
     $_SESSION['manf_access']=$row[15];
     $_SESSION['user_company']=$row[16];
     $_SESSION['d_manf']=$row[17];
     $_SESSION['d_vds']=$row[18];
     $_SESSION['opage_access']=$row[20];
     $_SESSION['download_parts']=$row[21];
     $_SESSION['pricing_access']=$row[22];

      $_SESSION['walk_through']=$row[23];
   $_SESSION['show_vds']=$row[24];
   $_SESSION['show_qualification']=$row[25];
    $_SESSION['page_jitter_access']=$row[26];
     $_SESSION['user_first_name']=$row[27];

     $_SESSION['admin_status']=$row[28];
     $_SESSION['reg_type']=$row[29];
     $_SESSION['survey_completion']=$row[30];
     $_SESSION['kp2_igbt_price']=$row[34];
     $_SESSION['kp2_diode_price']=$row[35];
     $_SESSION['kp2_mcu_price']=$row[36];
     $_SESSION['kp2_mosfet_price']=$row[37];
     $_SESSION['xref_report']=$row[38];
     $_SESSION['kp2_credit']=$row[39];
     $_SESSION['kp2_credit_used']=$row[40];
     
     $_SESSION['kp2_gdrive_price']=$row[42];
     $_SESSION['kp2_leddrive_price']=$row[43];
     $_SESSION['kp2_thyristor_price']=$row[44];
     $_SESSION['kp2_relay_price']=$row[45];

     $_SESSION['package_plan']=$row[46];
     $_SESSION['package_plan_user']=$row[47];

if($row[4]!="0000-00-00" || $row[41]=="Inactive"){
  $now = new DateTime();
    $startdate = new DateTime($row[3]);
    $enddate = new DateTime($row[4]);

    if(($now > $startdate && $now > $enddate) || $row[41]=="Inactive") {
    
     $_SESSION['admin_status']=0;
     $_SESSION['user_type']='RUser';
     $_SESSION['download_parts']=0;
     $_SESSION['showkp']=2;
     $_SESSION['link_detail_page']="No";
     $_SESSION['xref_report']="";
     $_SESSION['credituse']=0;
       $_SESSION['show_vds']="";
   $_SESSION['show_qualification']="";
        $_SESSION['d_manf']="";
     $_SESSION['d_vds']="";

    $_SESSION['access_limit']='0';
     $_SESSION['show_part']='0';
     $_SESSION['landing_page']='our-technology';
      $_SESSION['credit']='0';
     $_SESSION['walk_through']='1';
     $_SESSION['manf_access']=':aos:vishay:onsemi:renesas:rohm:infineon:toshiba:st:microsemi:nexperia:diodes:ti:niko-sem:epc:taiwansemi:sanken:centralsemi:mccsemi:goford:hunteck:';

        $_SESSION['reg_type']=1;

        $_SESSION['expired_user']=1;
       //header("Location: login.php?error=Your login is expired.. Please contact admin.");
   //exit;

    }
    }
    
    
$surveyDate = date('Y-m-d');
$surveyDate=date('Y-m-d', strtotime($surveyDate));

$contractDateBegin = date('Y-m-d', strtotime($row[32]));
$contractDateEnd = date('Y-m-d', strtotime($row[33]));

if (($surveyDate >= $contractDateBegin) && ($surveyDate <= $contractDateEnd) && $contractDateBegin!="1970-01-01" && $contractDateEnd!="1970-01-01")
$_SESSION['survey_feedback']=$row[31];
else if ($surveyDate >= $contractDateBegin && $contractDateBegin!="1970-01-01" && $contractDateEnd=="1970-01-01")
$_SESSION['survey_feedback']=$row[31];
else if ($surveyDate <= $contractDateEnd && $contractDateBegin=="1970-01-01" && $contractDateEnd!="1970-01-01")
$_SESSION['survey_feedback']=$row[31];
else if ($contractDateBegin=="1970-01-01" && $contractDateEnd=="1970-01-01")
$_SESSION['survey_feedback']=$row[31];
else
$_SESSION['survey_feedback']="";

     } else {


   header("Location: login.php?page=".$_REQUEST['page']."&error=Invalid username or password.");
   exit;

  }
        if($_REQUEST['remember']!=""){

   $expire=time()+60*60*24*365;//however long you want
   setcookie('userid', $row[0], $expire,'/');
   setcookie('username', $row[1], $expire,'/');
   setcookie('usertype', $row[2], $expire,'/');
    setcookie('page_access', $row[5], $expire,'/');
    setcookie('opage_access', $row[20], $expire,'/');
    setcookie('material_access', $row[19], $expire,'/');
     setcookie('access_limit', $row[6], $expire,'/');
      setcookie('show_part', $row[8], $expire,'/');
      setcookie('landing_page', $row[9], $expire,'/');
      setcookie('link_detail_page', $row[10], $expire,'/');
      setcookie('showgraph', $row[11], $expire,'/');
       setcookie('credit', $row[12], $expire,'/');
       setcookie('showkp', $row[13], $expire,'/');
       setcookie('credituse', $row[14], $expire,'/');
       setcookie('manf_access', $row[15], $expire,'/');
       setcookie('user_company', $row[16], $expire,'/');
       setcookie('d_manf', $row[17], $expire,'/');
       setcookie('d_vds', $row[18], $expire,'/');
       setcookie('download_parts', $row[18], $expire,'/');
       setcookie('pricing_access', $row[19], $expire,'/');
  } else {
      unset($_COOKIE['userid']);
      unset($_COOKIE['username']);
      unset($_COOKIE['usertype']);
       unset($_COOKIE['userpermission']);
        unset($_COOKIE['userclient']);
        unset($_COOKIE['page_access']);
         unset($_COOKIE['page_jitter_access']);
        unset($_COOKIE['opage_access']);
         unset($_COOKIE['material_access']);
         unset($_COOKIE['pricing_access']);
        unset($_COOKIE['access_limit']);
       unset($_COOKIE['show_part']);
       unset($_COOKIE['landing_page']);
       unset($_COOKIE['link_detail_page']);
       unset($_COOKIE['showgraph']);
       unset($_COOKIE['credit']);
        unset($_COOKIE['showkp']);
         unset($_COOKIE['credituse']);
          unset($_COOKIE['manf_access']);
           unset($_COOKIE['user_company']);
           unset($_COOKIE['d_manf']);
           unset($_COOKIE['d_vds']);
           unset($_COOKIE['download_parts']);

           unset($_COOKIE['walk_through']);
           unset($_COOKIE['show_vds']);
           unset($_COOKIE['show_qualification']);
           unset($_COOKIE['user_first_name']);
                      unset($_COOKIE['admin_status']);
                      unset($_COOKIE['reg_type']);
                      unset($_COOKIE['survey_completion']);



    setcookie('userid', null, -1, '/');
    setcookie('username', null, -1, '/');
    setcookie('usertype', null, -1, '/');
    setcookie('page_access', null, -1, '/');
    setcookie('page_jitter_access', null, -1, '/');
    setcookie('opage_access', null, -1, '/');
    setcookie('material_access', null, -1, '/');
     setcookie('pricing_access', null, -1, '/');
        setcookie('access_limit', null, -1, '/');
        setcookie('show_part', null, -1, '/');
        setcookie('landing_page', null, -1, '/');
        setcookie('link_detail_page', null, -1, '/');
        setcookie('showgraph', null, -1, '/');
         setcookie('credit', null, -1, '/');
          setcookie('showkp', null, -1, '/');
           setcookie('credituse', null, -1, '/');
            setcookie('manf_access', null, -1, '/');
            setcookie('user_company', null, -1, '/');
            setcookie('d_manf', null, -1, '/');
            setcookie('d_vds', null, -1, '/');
             setcookie('download_parts', null, -1, '/');
             setcookie('walk_through', null, -1, '/');
             setcookie('show_vds', null, -1, '/');
             setcookie('show_qualification', null, -1, '/');
             setcookie('user_first_name', null, -1, '/');
             setcookie('admin_status', null, -1, '/');
             setcookie('reg_type', null, -1, '/');
             setcookie('survey_completion', null, -1, '/');



  }

  mysql_query("UPDATE admin SET last_login='".date("Y-m-d H:i:s")."' WHERE username='".$_SESSION['user_name']."'");
  if($_SESSION['lasturl']!="")
  {
  $lasturl= $_SESSION['lasturl'];
  $_SESSION['lasturl']="";
  }
   if($_SESSION['user_name']!="" && $lasturl!="")
   header("Location: ".$lasturl);
   else if($_REQUEST['page']!="" && strpos($_SESSION['page_access'],$_REQUEST['page'])!==false)
   header("Location: ".$_REQUEST['page'].".php");
//   else if($_SESSION['user_name']=="srai" || $_SESSION['user_name']=="dnp1976" || strtolower($_SESSION['user_name'])=="thomas@ru")
//   header("Location: our-technology.php");
   else if(strpos($_SESSION['landing_page'],'.php')===false)
header("Location: ".$_SESSION['landing_page'].".php");
   else if(strpos($_SESSION['landing_page'],'.php')!==false)
header("Location: ".$_SESSION['landing_page']."");
else
header("Location: /");



   exit;
  }
?>
<? include("header.php");?>
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
    .ms-options-wrap{width: 150px;}
 .ms-options-wrap > .ms-options {
      left: inherit;
    }
    td,th{    text-align: left;
    font-size: 12px;}
     .contents{font-size:18px;line-height: 26px;}
     .contents b{font-size:20px;}
       .tab-group {
  list-style:none;
  padding:0;
  margin:40px 0 20px 0;
  }
  .tab-group:after {
    content: "";
    display: table;
    clear: both;
  }
  .tab-group li a {
    display:block;
    text-decoration:none;
    padding:15px;
    background-color:#E8EAED;
    color:#000;
    font-size:20px;
    float:left;
    width:50%;
    text-align:center;
    cursor:pointer;
    transition:.5s ease;
  }
    .tab-group li a:hover {
      background:#166E8E;
      color:#FFF;
    }

  .tab-group li.active a {
    background:#166E8E;
    color:#FFF;
  }

  #login-box {
  margin-top:40px;
  position: relative;
  border-radius: 2px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);
   background-color:#E8EAED;
   min-height:200px;
   padding:20px;
   text-align:left;
}

.or {
  width: 40px;
  height: 40px;
  background: #DDD;
  border-radius: 50%;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);
  line-height: 40px;
  text-align:center;
  float:left;
  margin-right:10px;
  font-weight:bold;
  font-size:16px;
}

.orp{background: #A9D18E;}
.t{
  font-size:16px;
  margin-top: 10px;
}
.form-group{width:100%!important;}
.loginfm{border-radius: 5px; sbox-shadow: 5px -5px 2px #8f8f8f; width:40%; height:550px;background-color:#FFF;}
.ps{border-bottom:1px solid #2196f3;cursor:pointer;float:left;margin-left:6%;margin-top:50px;width:25%;padding:2px;dbackground-color:#ff9300;color:#FFF;font-size:16px;line-height:30px;}

	</style>
<section id="services" style="background-color:#EFEFEF;">
<div class="container" style="height:700px;">

<div class="row">
<br />
<center>
<div class="loginfm">
<br />
<h1 class="mb10 heading-title personailse-helper-text" style="font-size:30px!important;"><b>LOGIN</b></h1>
<center><?if($_REQUEST['error']=="" && $_REQUEST['success']==""){?><?}else{?><br /><font color="#FF0000"><?=$_REQUEST['error'];?></font><font color="#59A454"><?=$_REQUEST['success'];?></font><br /><?}?></center>
<div class="col-md-12" style="margin-top:10px;padding:70px;">
  <form action="login.php?page=<?=$_REQUEST['page'];?>" method="post">
      <div class="form-group has-feedback">
        <input type="text" class="form-control" name="username" required placeholder="Username" style="padding:20px;">
      </div>
      <div class="form-group has-feedback">
        <input type="password" class="form-control" name="password" required placeholder="Password" style="padding:20px;">
      </div>
      <div class="">
        <div class="col-xs-8" style="padding:0px;">
          <div class="checkbox icheck" style="text-align: left;">
          <a href="forgotpassword.php">Forgot Password?</a>
          </div>
        </div>
        <!-- /.col -->
        <div class="col-xs-4" style="padding:0px;">
          <button type="submit" class="btn btn-primary btn-block btn-flat" style="padding:10px;">Sign In</button>
        </div>
        <!-- /.col -->
      </div>

    </form>
</div>
</div>
</center>

    </div>

</div>
 </section>
   <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

  <style>

  .form-group{width:400px;}
  .ui-widget-content{
    background-color:inherit;
    border:0px;
}

.ui-state-active,.ui-state,.ui-button.ui-state-active,.ui-button.ui-state{    border: 0px solid #c5c5c5;
    background: #f6f6f6;
    font-weight: normal;
    color: #454545;}
  .ui-icon,.ui-state-active .ui-icon{xxbackground-image:none;}
      .ui-accordion .ui-accordion-content {
    padding: 1em 2em;
    }
    .ui-widget-content {
      color: inherit;
}
.ui-state-default{border:0px!important;background-color:#FFF!important;}
.ui-state-active .ui-icon{background-image: url("images/ui-icons_444444_256x240.png");}
.ui-helper-reset {
 line-height: inherit;
 font-size: inherit;
}
  </style>
<!-- Service Section End -->
<?include("footer.php");?>

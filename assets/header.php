<?
if($_SESSION['user_country_name']==""){

 $curl_handle=curl_init();
  curl_setopt($curl_handle,CURLOPT_URL,'http://api.ipstack.com/'.$_SERVER['REMOTE_ADDR'].'?access_key=2f8ea8267d188c5b28576707727de95a&format=1');
  curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
  curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
  $location = json_decode(curl_exec($curl_handle));
  curl_close($curl_handle);
  
$_SESSION['user_country_name']=$location->country_name;
$_SESSION['user_city']=$location->city;
}
 $sql="INSERT INTO users_log SET username='".$_SESSION['user_name']."',page_url='".$_SERVER['REQUEST_URI']."',user_ip='".$_SERVER['REMOTE_ADDR']."',user_country='".$_SESSION['user_country_name']."',user_city='".$_SESSION['user_city']."'";
 mysql_query($sql);
 $_SESSION['log_id']=mysql_insert_id();
// if(strpos($_SERVER["HTTP_HOST"],"beta.discoveree.io")!==false){
// include("header-beta.php");
?>
<!DOCTYPE html>
    <html lang="en">
      <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>DiscoverEE : Your gateway to discovering Power Devices</title>

        <? if($_SESSION['user_id']!="" && $_REQUEST['download']!=""){?>
   <meta http-equiv="refresh" content="300;url=signout.php" />
<? }?>
        <!-- Bootstrap -->
        <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">

        <!-- jQuery Load -->
        <script src="assets/js/jquery-min.js"></script>
        <link href="https://fonts.googleapis.com/css?family=Oswald&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
      <style>
      body{font-family: 'Roboto', sans-serif; color:#000;}
      a:hover{text-decoration:none;}
      ul {
    list-style: none;
}
.globalNavActions li {
    display: list-item;
    text-align: -webkit-match-parent;
    float: left;
}
.container {
    width: 85%;
}
.ctaSecondary--nav, .ctaSecondary--small {
    -webkit-box-shadow: 0 0 0 0.0625rem rgba(36,28,21,.4) inset;
    box-shadow: inset 0 0 0 0.0625rem rgba(36,28,21,.4);
    color: #241c15;
    min-width: 7.5rem;
    padding: .8125rem;
}
.ctaSecondary, .formFileUpload input[type=file]+label {
    -o-transition: all .15s linear;
    -webkit-box-shadow: 0 0 0 0.0625rem rgba(0,124,137,.3) inset;
    -webkit-transition: all .15s linear;
    background-color: transparent;
    box-shadow: inset 0 0 0 0.0625rem rgba(0,124,137,.3);
    color: #007c89;
    font-size: 1 rem;
    font-weight: 600;
    padding: 1.25rem 2.5rem;
    transition: all .15s linear;
}

.ctaPrimary, .ctaSecondary, .formFileUpload input[type=file]+label, .tagLink {
    -moz-appearance: none;
    -webkit-appearance: none;
    -webkit-box-sizing: border-box;
    background: none;
    border: none;
    box-sizing: border-box;
    cursor: pointer;
    display: inline-block;
    text-align: center;
}
ul.globalNavActions{float:right;}

.globalNavActions__signup {
    margin: 0 0 0 .75rem;
}
.ctaPrimary--nav, .ctaPrimary--small {
    min-width: 7.5rem;
    padding: .8125rem;
}

.ctaPrimary {
    -o-transition: all .15s linear;
    -webkit-box-shadow: 0 0 0 0.0625rem #007c89 inset;
    -webkit-transition: all .15s linear;
    background-color: #007c89;
    box-shadow: inset 0 0 0 0.0625rem #007c89;
    color: #fff;
    font-size: 1 rem;
    font-weight: 600;
    padding: 1.25rem 2.5rem;
    transition: all .15s linear;
}


.ctaPrimary:hover {
    background-color: #EFEFEF;
    color: #000;
}


      ul.nav li a{color: #241c15;
    font-size: 1.7375rem;}

     .hero__title {
     margin-top: 1.875rem;
    color: #241c15;
    font-family: Cooper,Georgia,Times,Times New Roman,serif;
    font-size: 5.5rem;
    font-weight: 300;
}
h1.hero__title{font-family: 'Oswald', sans-serif;font-size: 5.5rem;}
.title_content{margin-top:20px;font-size: 2.2rem;}
.banner{margin-top:10px;min-height:200px;background-color:#ffe01b;}

.banner .slider{padding:50px  0px 80px 0px;}
.banner_left{margin-top:90px;}

.banner .slider{padding: 0px  0px  0px 0px;}
.banner_left{margin-top:40px;}
.roboto {font-size:18px;font-weight:normal;}
span.content{margin-top:10px;display:block;}
#services{min-height:500px;}
.row h1{margin: 20px!important;}
.nav > li > a {
    position: relative;
    display: block;
    padding: 10px 10px;
    font-size: 16px!important;
}


 .dropdown-menu {
    background-color: #ffffff;
    padding: 0.5em 0.625em;
    border-radius: 5px;
    border: 1px solid #dfe3e8;
    box-shadow: 0 5px 30px 5px rgba(69,79,91,0.1);
}
.products {
    min-width: 800px;
}
.solutions {
    min-width: 300px;
}
.dropdown-menu ul{padding: 5px;}
.dropdown-menu ul li{padding: 5px; border-left:solid 2px #FFF;}
.dropdown-menu ul a.href{font-size: 16px!important;}
.dropdown-menu ul li:hover{background-color:#EFEFEF; border-left:solid 2px #000; }
.dropdown-menu ul li.nohover, .dropdown-menu ul li.nohover:hover{background-color:#FFF; border-left:solid 2px #FFF; border-bottom:solid 1px; }
      </style>
      </head>
          <body>
          <div class="container">
          <?if(strpos($_SERVER['REQUEST_URI'],"index")!==false || $_SERVER['REQUEST_URI']=="/"){?>
          <br />
          <?}?>
          <div class="col-md-3" <?if(strpos($_SERVER['REQUEST_URI'],"index")===false && $_SERVER['REQUEST_URI']!="/"){?>style="margin-top:6px;"<?}?>><a href="index.php"><img src="images/logo-black.png" border="0" /></a></div>
           <div class="col-md-6" <?if(strpos($_SERVER['REQUEST_URI'],"index")===false && $_SERVER['REQUEST_URI']!="/"){?>style="margin-top:5px;"<?}?>>
          <ul class="nav navbar-nav" style="width:auto;">
                            <li><a href="index.php">  Home</a></li>
                            <?if($_SESSION['user_name']!=""){?>

                                <li class="dropdown"><a href="#" oenmouseover="$(this).trigger('click');" data-toggle="dropdown" data-hover="dropdown">
     Discover<span class="caret"></span>
  </a>
  <div class="dropdown-menu solutions" style="left: 0px;">
                            <ul>

                            <?if(strpos($_SESSION['page_access'],'dashboard-r')!==false){?>
                                        <a class="href" href="dashboard-r.php"><li><?if($_SESSION['user_name']=="srai"){?>Report-R<?}else{?>Rutronik's Report<?}?></li></a>

                            <?}?>
                            <?if(strpos($_SESSION['page_access'],'discover')!==false || $_SESSION['page_access']=="" ){?>
                                        <a class="href" href="discover.php"><li>Dashboard By VDS</li></a>

                            <?}?>
                             <?if($_SESSION['user_name']=="srai" || $_SESSION['user_name']=="dnp1976"){?>

                                        <a class="href" href="dashboard-pl.php"><li>Dashboard By Power Loss</li></a>


                              <?}?>
                            <?if(strpos($_SESSION['page_access'],'dashboard-1')!==false){?>
                                        <a class="href" href="dashboard-1.php"><li><?if($_SESSION['user_name']=="srai"){?>Dashboard-I<?}else{?>Dashboard By Package<?}?></li></a>

                            <?}?>


                            <?if($_SESSION['user_name']=="dnp1976" || $_SESSION['user_name']=="srai" ){?>
                                        <a class="href" href="discover.php?view=rqg"><li>R*QG Dashboard</li></a>

                                         <a class="href" href="discover.php?view=rcoss"><li>R*COSS Dashboard</li></a>

                            <?}?>



                             <?if(strpos($_SESSION['page_access'],'easysearch')!==false || $_SESSION['page_access']==""){?>
                                        <a class="href" href="easysearch.php"><li>Easy Search (Text Based)</li></a>


                            <?}?>
                              <?if(strpos($_SESSION['page_access'],'advancesearch')!==false){?>
                                        <a class="href" href="advancesearch.php"><li>Advance Search</li></a>

                            <?}?>
                             <?if(strpos($_SESSION['page_access'],'easysearch')!==false || $_SESSION['page_access']==""){?>

                                        <a class="href" href="cross_reference.php"><li>Cross References</li></a>
                            <?}?>
                             <?if(strpos($_SESSION['page_access'],'compare')!==false || $_SESSION['page_access']==""){?>
                                         <a class="href" href="compare.php"><li>Compare Part Numbers</li></a>

                                        <?}?>


                             <?if(strpos($_SESSION['page_access'],'packagereport')!==false){?>
                                        <a class="href" href="packagereport.php"><li>Competitive Intelligence</li></a>

                            <?}?>

                                 <?if(strpos($_SESSION['page_access'],'recent-activity')!==false || $_SESSION['page_access']==""){?>
                                          <a class="href" href="recent-activity.php"><li>Recent Manufacturer Activity</li></a>

                                        <?}?>
                                    </ul>
                                    </div>
                            </li>

                            <?}?>
                           <?if($_SESSION['user_name']==""){?>
                            <li class="dropdown"><a href="#" oenmouseover="$(this).trigger('click');" data-toggle="dropdown" data-hover="dropdown">
     Products<span class="caret"></span>
  </a>
                          <div class="dropdown-menu products" style="left: 0px;">

                            <div class="col-md-6">
                            <ul>
                            <li class="nohover"><a><b>Power Mosfet (Si/GaN/SiC) Intelligence Platform</b></a></li>
                            <a href="request_demo.php" class="href"><li>Product Tracking</li></a>
                            <a href="request_demo.php" class="href"><li>Market Research</li></a>
                            <a href="request_demo.php" class="href"><li>Cross Reference Search</li></a>
                            <a href="request_demo.php" class="href"><li>Datasheet Comparisons</li></a>
                            <a href="request_demo.php" class="href"><li>Power Loss Calculations</li></a>
                            <a href="request_demo.php" class="href"><li>Industry Dashboard for Device Rdson vs. VDS</li></a>
                            <a href="request_demo.php" class="href"><li>Industry Dashboard for Device Rdson*Qg</li></a>
                            <a href="request_demo.php" class="href"><li>Industry Dashboard for Device Power Loss</li></a>
                            </ul>
                            <ul>
                             <li class="nohover"><a><b>SPICE Models</b></a></li>
                            <a href="request_demo.php" class="href"><li>Silicon Mosfet Spice Models</li></a>
                            <a href="request_demo.php" class="href"><li>SiC Mosfet Spice Models</li></a>
                            <a href="request_demo.php" class="href"><li>GaN Mosfet Spice Models</li></a>
                            </ul>
                            </div>
                            <div class="col-md-6">
                            <ul>
                            <li class="nohover"><a><b>Analytics for Hardware Manufacturers and Distributors</b></li></a>
                            <a href="request_demo.php" class="href"><li>Web Analytics</li></a>
                            <a href="request_demo.php" class="href"><li>Data Analytics</li></a>
                            <a href="request_demo.php" class="href"><li>Technical Data Extraction</li></a>
                            <a href="request_demo.php" class="href"><li>Custom Services</li></a>


                            </ul>
                            <ul>
                            <li class="nohover"><a><b>Application Simulation On Cloud</b></li></a>
                            <a href="request_demo.php" class="href"><li>In-Circuit Device Simulation</li></a>
                            <a href="request_demo.php" class="href"><li>DC/DC Converter</li></a>
                            <a href="request_demo.php" class="href"><li>AC/DC Converter</li></a>
                            <a href="request_demo.php" class="href"><li>DC Motor</li></a>
                            <a href="request_demo.php" class="href"><li>Three Phase Motor</li></a>
                            <a href="request_demo.php" class="href"><li>LED Lighting</li></a>
                            <a href="request_demo.php" class="href"><li>Load Switch</li></a>
                            <a href="request_demo.php" class="href"><li>Inductive Switching</li></a>






                                    </ul>
                                    </div>
                          </div>
                            </li>
                                  <li class="dropdown"><a href="#" oenmouseover="$(this).trigger('click');" data-toggle="dropdown" data-hover="dropdown">
     Solutions<span class="caret"></span>
  </a>
  <div class="dropdown-menu solutions" style="left: 0px;">
                            <ul>

                            <a href="request_demo.php" class="href"><li>Product Analytics</li></a>
                            <a href="request_demo.php" class="href"><li>Market Research</li></a>
                            <a href="request_demo.php" class="href"><li>Competitor Intelligence</li></a>
                            <a href="request_demo.php" class="href"><li>Power Device Modeling</li></a>
                            <a href="request_demo.php" class="href"><li>Spice Models</li></a>
                            <a href="request_demo.php" class="href"><li>Web Based Circuit Simulation</li></a>
                            <a href="request_demo.php" class="href"><li>LIVE Product Application Pages</li></a>
                            <a href="request_demo.php" class="href"><li>Enterprise Data Extraction</li></a>
                                    </ul>
                                    </div>
                            </li>

                            <?}?>
     <?if($_SESSION['user_name']=="srai" || $_SESSION['user_name']=="dnp1976"){?>
                                 <li class="dropdown"><a href="#" oenmouseover="$(this).trigger('click');" data-toggle="dropdown" data-hover="dropdown">
     Spice Model<span class="caret"></span>
  </a>
  <div class="dropdown-menu solutions" style="left: 0px;">
                            <ul>

                            <a href="appmodel-buck.php" dtitle="part will show as basis of availability in input file" class="href"><li>Simulate Multiple Parts</li></a>
                            <a href="appmodel-multiple-new.php" dtitle="Default simulate will show as basis of output file" class="href"><li>Simulate Multiple Parts (Pre-loaded)</li></a>
                             <a href="appmodel-single-new2.php" dtitle="basis of output file" class="href"><li>Simulate Single Part (Pre-loaded)</li></a>
                             <a href="spicemodel-report.php" dtitle="As basis of file in input folder" class="href"><li>Spice Model Availability</li></a>
                                    </ul>
                                    </div>
                            </li>
   <li><a href="pricing.php">Pricing</a></li>
<?}?>
                            <li><a href="press-release.php">Industry News</a></li>
                            <?if($_SESSION['user_name']==""){?>
                            <li><a href="about-us.php">About Us</a></li>
                             <?}?>
         </div>
        <div class="col-md-3" style="padding-left:0px;padding-right:0px;<?if(strpos($_SERVER['REQUEST_URI'],"index")===false && $_SERVER['REQUEST_URI']!="/"){?>margin-top:5px;<?}?>">

        <ul class="globalNavActions">

        <?if($_SESSION['user_id']!=""){?>
       <li class="globalNavActions__signup">
       <a class="zebra_tooltips ctaPrimary ctaPrimary--nav" style="cursor:pointer;" title="<center><?if($_SESSION['showkp']=="1"){?><h5>Total&nbsp;Credit&nbsp;Use:&nbsp;<?=$_SESSION['credituse'];?></h5><br /><?}?><?if($_SESSION['user_type']=="RUser"){?><h5>Available&nbsp;Credit:&nbsp;<?=$_SESSION['credit'];?></h5><br /><?}?><ul class='nav'><li class='active' style='background-color: #FFF;color:#000;margin-bottom:5px;'><a href='user_landing_page_setting.php'>Landing Page</a></li><li class='active' style='background-color: #FFF;color:#000;margin-bottom:5px;'><a href='change_password.php'>Change Password</a></li><?if($_SESSION['user_name']=="dnp1976" || $_SESSION['user_name']=="srai"){?><li class='active' style='background-color: #FFF;color:#000;margin-bottom:5px;'><a href='dashboard_setting.php'>Dashboard&nbsp;Settings</a></li><li class='active' style='background-color: #FFF;color:#000;margin-bottom:5px;'><a href='activity_report.php'>Activity Report</a></li><?}?><li class='active' style='background-color: #FFF;color:#000;'><a href='signout.php'>Signout</a></li></ul></center>"> <?=$_SESSION['user_name'];?></a></li>
        <?}else{?>
        <li class="globalNavActions__login">
    <a href="login.php" class="ctaSecondary ctaSecondary--nav">
        Log In    </a>
</li>
<li class="globalNavActions__signup">
<a href="login.php?type=signup" class="zebra_tooltips ctaPrimary ctaPrimary--nav"  title="<center>Dashboard<br />Market&nbsp;Intelligence<br />Detailed&nbsp;Comparisons<br />View&nbsp;Normalized&nbsp;Data<br />Application&nbsp;Modeling</center>">
        Sign Up Free    </a>
</li>
  <?}?>

    </ul>

     </div>
          </div>
          <?if(strpos($_SERVER['REQUEST_URI'],"index")===false && $_SERVER['REQUEST_URI']!="/"){?>
          <div class="banner" style="height:1px;min-height:0px;margin-top:0px;"></div>
          <?}?>
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
<script src="assets/zebra_tooltips.min.js"></script>
<link rel="stylesheet" href="assets/css/bubble/zebra_tooltips.css" type="text/css">
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

<?
if($_SESSION['user_name']=="srai" || $_SESSION['user_name']=="dnp1976" || 1==1){
include("header-v2.php");
}else{
if($_SESSION['user_country_name']==""){

$ip_url='http://api.ipstack.com/'.$_SERVER['REMOTE_ADDR'].'?access_key=2f8ea8267d188c5b28576707727de95a&format=1';
$ip_url='https://freegeoip.app/json/'.$_SERVER['REMOTE_ADDR'];

 $curl_handle=curl_init();
  curl_setopt($curl_handle,CURLOPT_URL,$ip_url);
  curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
  curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
  $location = json_decode(curl_exec($curl_handle));
  curl_close($curl_handle);
  
$_SESSION['user_country_name']=$location->country_name;
$_SESSION['user_city']=$location->city;
}
$ins_process=0;
if($_SESSION['auser_type']!="Super Admin" && (strpos($_SERVER['REQUEST_URI'],'process-graph-report.php')!==false || strpos($_SERVER['REQUEST_URI'],'compare.php')!==false)){
$p_u_r_l=$_SERVER['REQUEST_URI'];
$p_u_r_l = trim(urldecode($p_u_r_l));
if(strpos($p_u_r_l,'clickmore')!==false)
$p_u_r_l = substr($p_u_r_l,0,strpos($p_u_r_l,'clickmore'));

if(strpos($p_u_r_l,'access_by')!==false)
$p_u_r_l = substr($p_u_r_l,0,strpos($p_u_r_l,'access_by'));

if(substr($p_u_r_l,strlen($p_u_r_l)-1,1)=="&")
$p_u_r_l = substr($p_u_r_l,0,strlen($p_u_r_l)-1);
$p_u_r_l = str_replace("::0","",$p_u_r_l);
$p_u_r_l = str_replace("::1","",$p_u_r_l);
$_SERVER['REQUEST_URI'] = str_replace("::2","",$p_u_r_l);
$chk_avl=mysql_fetch_array(mysql_query("SELECT * FROM users_log WHERE username='".$_SESSION['user_name']."' AND page_url='".$_SERVER['REQUEST_URI']."'"));
if($chk_avl[0]==""){
$ins_process=0;
}else{
$ins_process=1;
$_SERVER['REQUEST_URI'].="&access_by=log";
}
}
 $sql="INSERT INTO users_log SET username='".$_SESSION['user_name']."',page_url='".$_SERVER['REQUEST_URI']."',user_ip='".$_SERVER['REMOTE_ADDR']."',user_country='".$_SESSION['user_country_name']."',user_city='".$_SESSION['user_city']."'";
 if($_SESSION['auser_type']!="Super Admin")
 mysql_query($sql);
 $_SESSION['log_id']=mysql_insert_id();
// if(strpos($_SERVER["HTTP_HOST"],"beta.discoveree.io")!==false){
// include("header-beta.php");
if($meta_title==""){
$page_url = $_SERVER['REQUEST_URI'];
if(strpos($page_url,"?")!==false)
$page_url=substr($page_url,0,strpos($page_url,"?"));
if($_SESSION['user_name']=="dnp1976"){
//echo $page_url;
}
if($page_url=="/" || $page_url=="https://www.discoveree.io/" || $page_url=="https://www.discoveree.io/index.php")
$page_meta_tags=mysql_fetch_array(mysql_query("SELECT meta_title,meta_desc,meta_keywords FROM page_meta_tags WHERE page_url = 'https://www.discoveree.io/' OR page_url = 'https://www.discoveree.io/index.php' "));
else
$page_meta_tags=mysql_fetch_array(mysql_query("SELECT meta_title,meta_desc,meta_keywords FROM page_meta_tags WHERE page_url LIKE '%".$page_url."%'"));
if($page_meta_tags[0]!=""){
$meta_title=$page_meta_tags[0];
$meta_desc=$page_meta_tags[1];
$meta_keywords=$page_meta_tags[2];
}
}
?>
<!DOCTYPE html>
    <html lang="en">
      <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?if($meta_title!=""){?>
        <title><?=$meta_title;?> | DiscoverEE</title>
        <meta property="og:title" content="DiscoverEE : <?=$meta_title;?>">
        <?}else{?>
        <title>DiscoverEE : The Product Intelligence Platform</title>
        <meta property="og:title" content="DiscoverEE : The Product Intelligence Platform">
        <?}?>

        <link rel='shortcut icon' type='image/x-icon' href='/images/DiscoverEE_favicon.svg' />

        <? if($_SESSION['user_id']!="" && $_REQUEST['download']!=""){?>
   <meta http-equiv="refresh" content="300;url=signout.php" />
<? }?>
<?if($meta_desc!=""){?>
<meta name="description" content="<?=$meta_desc;?>">
<meta property="og:description" content="<?=$meta_desc;?>">
<?}else{?>
<meta name="description" content="Visualize The Entire Component Landscape - Track, Find, Select, Compare, Model and Simulate Components">
<meta property="og:description" content="Visualize The Entire Component Landscape - Track, Find, Select, Compare, Model and Simulate Components">
<?}?>
<?if($meta_keywords!=""){?>
<meta name="keywords" content="<?=$meta_keywords;?>">
<?}?>
<meta property="og:image" content="https://www.discoveree.io/images/logo-black.png">

        <!-- Bootstrap -->
        <link rel="stylesheet" type="text/css" href="/assets/css/bootstrap.css">

        <!-- jQuery Load -->
        <script src="/assets/js/jquery-min.js"></script>
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
    padding: 9px;
    font-size: 18px!important;
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
#services{min-height: 650px;}
@media (max-width: 1215px) {
  .discoveree_logo {
    width:195px;
  }
}

@media screen and (max-width:620px) {
.hd,.dropdown-menu .col-md-6 ul a{text-align: center;}
.products ul a,.solutions ul a{text-align: center;}
.products ul,.solutions ul {float:inherit;display: inline-table;}
.dropdown-menu .col-md-6{display:block;}
.products,.solutions{min-width: 100%!important;}
ul.globalNavActions{float:inherit;display: inline-table;}
.products ul,.solutions ul,.loginfm{width: 100%!important;}
}
</style>
<script src="https://cdn-in.pagesense.io/js/discoveree/1cd2547937f24259b025831d4b8086f7.js"></script>


<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-235054410-1">
</script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-235054410-1');
</script>

      </head>
          <body>
          <div class="container header">
          <div class="hd">
          <?if(strpos($_SERVER['REQUEST_URI'],"index")!==false || $_SERVER['REQUEST_URI']=="/"){?>
          <br />
          <?}?>
          <div class="col-md-3" style="<?if(strpos($_SERVER['REQUEST_URI'],"index")===false && $_SERVER['REQUEST_URI']!="/"){?>margin-top:6px;<?}?><?if($_SESSION['user_name']!=""){?>width:22%;<?}?>"><a href="/index.php">

          <img src="/images/DiscoverEE_Logo_Black_Red.svg.png" class="discoveree_logo" style="width: 100%; height: auto;" border="0" />

          </a></div>
           <div class="col-md-<?if($_SESSION['user_name']==""){?>6<?}else{?>8<?}?>" <?if(strpos($_SERVER['REQUEST_URI'],"index")===false && $_SERVER['REQUEST_URI']!="/"){?>style="margin-top:5px;padding-right: 0px; padding-left: 0px;<?if($_SESSION['user_name']!=""){?>width:69%;<?}?>"<?}?> style="padding-right: 0px; padding-left: 0px;<?if($_SESSION['user_name']!=""){?>width:69%;<?}?>">
          <ul class="nav navbar-nav" style="width:auto;">

                            <?if($_SESSION['user_name']!="" && (
                           strpos($_SESSION['page_access'],'discover:')!==false ||
                           strpos($_SESSION['page_access'],'discover-pl:')!==false ||
                           strpos($_SESSION['page_access'],'dashboard-1:')!==false ||
                           strpos($_SESSION['page_access'],'easysearch:')!==false ||
                           strpos($_SESSION['page_access'],'advancesearch:')!==false ||
                           strpos($_SESSION['page_access'],'discover-vds:')!==false ||
                           strpos($_SESSION['page_access'],'cross_reference:')!==false ||
                           strpos($_SESSION['page_access'],'packagereport:')!==false ||
                           strpos($_SESSION['page_access'],'discover-vds:')!==false
                            )){
                            $access_report['Mosfets'][]="process-graph-report.php";
                            ?>

                                <li class="dropdown"><a href="#" oenmouseover="$(this).trigger('click');" data-toggle="dropdown" data-hover="dropdown">
     Discover FETs<span class="caret"></span>
  </a>
  <div class="dropdown-menu solutions" style="left: 0px;min-width:380px!important;">
                            <ul>
                            
                            <?if(strpos($_SESSION['page_access'],'compare')!==false || $_SESSION['page_access']==""){?>
                                         <a class="href" href="https://www.fet.discoveree.io/compare.php"><li>Search Or Compare Parts</li></a>

                                        <?}?>

                                         <?if(strpos($_SESSION['page_access'],'easysearch')!==false || $_SESSION['page_access']==""){?>
                                        <a class="href" href="https://www.fet.discoveree.io/easysearch.php"><li>Easy Search (Text Based)</li></a>


                            <?}?>

                                         <?if(strpos($_SESSION['page_access'],'cross_reference')!==false || $_SESSION['page_access']==""){?>

                                        <a class="href" href="https://www.fet.discoveree.io/cross_reference.php"><li>Cross References</li></a>
                            <?}?>


                              <?if(strpos($_SESSION['page_access'],'advancesearch')!==false){?>
                                        <a class="href" href="https://www.fet.discoveree.io/advancesearch.php"><li>Advance Search</li></a>

                            <?}?>
                            

                            
                             <?if($_SESSION['user_name']=="srai" || $_SESSION['user_name']=="dnp1976"){?>

                                        <a class="href" href="https://www.discoveree.io/trends.php"><li>Industry Trends</li></a>


                              <?}?>

                                <li class="nohover"><a><b></b></a></li>
                            
                             <?if(strpos($_SESSION['page_access'],'discover-vds:')!==false){?>
                                        <a class="href" href="https://www.fet.discoveree.io/discover-vds.php"><li>Dashboard: RDS<sub>(on)</sub> vs. BV<sub>DSS</sub> (Jitter)</li></a>

                            <?}?>
                            
                             <?if(strpos($_SESSION['page_access'],'discover-gan:')!==false){?>
                                        <a class="href" href="https://www.fet.discoveree.io/discover-gan.php"><li>Dashboard: RDS<sub>(on)</sub> vs. BV<sub>DSS</sub> (GaN)</li></a>

                            <?}?>
                            
                            <?if(strpos($_SESSION['page_access'],'discover:')!==false ){?>
                                        <a class="href" href="https://www.fet.discoveree.io/discover.php"><li>Dashboard: RDS<sub>(on)</sub> vs. BV<sub>DSS</sub></li></a>

                            <?}?>
                            
                            
                             <?if(strpos($_SESSION['page_access'],'discover-rdson.php?view=idrdson:')!==false ){?>
                                        <a class="href" href="https://www.fet.discoveree.io/discover-rdson.php?view=idrdson"><li>Dashboard: I<sub>D</sub>(max) vs. RDS<sub>(on)</sub></li></a>

                            <?}?>
                             <?if(strpos($_SESSION['page_access'],'discover-rdson.php?view=qgrdson:')!==false ){?>
                                        <a class="href" href="https://www.fet.discoveree.io/discover-rdson.php?view=qgrdson"><li>Dashboard: Q<sub>G</sub>(typ) vs. RDS<sub>(on)</sub></li></a>

                            <?}?>

                             <?if(strpos($_SESSION['page_access'],'discover-rdson.php?view=qgdrdson:')!==false ){?>
                                        <a class="href" href="https://www.fet.discoveree.io/discover-rdson.php?view=qgdrdson"><li>Dashboard: Q<sub>GD</sub>(typ) vs. RDS<sub>(on)</sub></li></a>

                            <?}?>
                             <?if(strpos($_SESSION['page_access'],'discover-rdson.php?view=cossrdson:')!==false ){?>
                                        <a class="href" href="https://www.fet.discoveree.io/discover-rdson.php?view=cossrdson"><li>Dashboard: C<sub>OSS</sub>(typ) vs. RDS<sub>(on)</sub></li></a>

                            <?}?>

                            <?if(strpos($_SESSION['page_access'],'dashboard-1:')!==false){?>
                                        <a class="href" href="https://www.fet.discoveree.io/dashboard-1.php"><li>Dashboard: RDS<sub>(on)</sub> vs. Package</li></a>

                            <?}?>
                            <?if(strpos($_SESSION['page_access'],'dashboard-1.php?view=packageid:')!==false){?>
                                        <a class="href" href="https://www.fet.discoveree.io/dashboard-1.php?view=packageid"><li>Dashboard: I<sub>D</sub>(max) vs. Package</li></a>

                            <?}?>
                            <?if(strpos($_SESSION['page_access'],'dashboard-1.php?view=packagevds:')!==false){?>
                                        <a class="href" href="https://www.fet.discoveree.io/dashboard-1.php?view=packagevds"><li>Dashboard: BV<sub>DSS</sub> vs. Package</li></a>

                            <?}?>
                            <?if(strpos($_SESSION['page_access'],'dashboard-1.php?view=packagerthjc:')!==false){?>
                                        <a class="href" href="https://www.fet.discoveree.io/dashboard-1.php?view=packagerthjc"><li>Dashboard: R<sub>THJC</sub> vs. Package</li></a>

                            <?}?>
                            <?if(strpos($_SESSION['page_access'],'dashboard-1.php?view=packagerthja:')!==false){?>
                                        <a class="href" href="https://www.fet.discoveree.io/dashboard-1.php?view=packagerthja"><li>Dashboard: R<sub>THJA</sub> vs. Package</li></a>

                            <?}?>
                            
                              <?if(strpos($_SESSION['page_access'],'dashboard-pl')!==false){?>

                                        <a class="href" href="https://www.fet.discoveree.io/dashboard-pl.php"><li>Dashboard: Power Loss vs. RDS<sub>(on)</sub></li></a>


                              <?}?>
                              

                              

                               <?if(strpos($_SESSION['page_access'],'discover.php?show=rdson-vds-monthly')!==false ){?>
                                        <a class="href" href="https://www.fet.discoveree.io/discover.php?show=rdson-vds-monthly"><li>Dashboard: RDS<sub>(on)</sub> vs. BV<sub>DSS</sub> (Monthly Activity)</li></a>

                            <?}?>
                            

                            <?if($_SESSION['user_name']=="dnp1976" || $_SESSION['user_name']=="srai" || $_SESSION['user_name']=="Ananya" || $_SESSION['user_name']=="Ritesh" ){?>
                                        <a class="href" href="https://www.fet.discoveree.io/discover.php?view=rqg"><li>Dashboard: RDS<sub>(on)</sub>*QG vs. BV<sub>DSS</sub></li></a>

                                         <a class="href" href="https://www.fet.discoveree.io/discover.php?view=rcoss"><li>Dashboard: RDS<sub>(on)</sub>*Coss vs. BV<sub>DSS</sub></li></a>

                            <?}?>

                              <?if(strpos($_SESSION['page_access'],'discover-vds:')!==false && strpos($_SESSION['page_access'],'pricing')!==false ){?>
                                        <a class="href" href="https://www.fet.discoveree.io/discover-vds.php?view=pricing"><li>Dashboard: RDS<sub>(on)</sub> vs. Price</li></a>

                            <?}?>

                              <?if(strpos($_SESSION['page_access'],'dashboard-package-trends')!==false){?>

                                        <a class="href" href="https://www.fet.discoveree.io/dashboard-package-trends.php"><li>Dashboard: Package Trends</li></a>


                              <?}?>
                            



                            
                              <?if($_SESSION['user_name']=="11srai" || $_SESSION['user_name']=="11dnp1976" || $_SESSION['user_name']=="andrea.gorgerino@epc-co.com"){?>

                                        <a class="href" href="https://www.fet.discoveree.io/cross_reference_epc.php"><li>Customizable Cross Reference</li></a>
                            <?}?>
                            
                             <?if($_SESSION['user_name']=="srai" || $_SESSION['user_name']=="dnp1976" || strpos($_SESSION['page_access'],'process-report-crossreference')!==false){?>

                                        <a class="href" target="_blank" href="https://www.discoveree.io/process-report-crossreference.php"><li>Customizable Power Loss Based Cross Reference</li></a>
                            <?}?>
                            



                             <?if(strpos($_SESSION['page_access'],'packagereport')!==false){?>
                                        <a class="href" href="https://www.fet.discoveree.io/packagereport.php"><li>Competitive Intelligence</li></a>

                            <?}?>

                                 <?if(strpos($_SESSION['page_access'],'recent-activity')!==false || $_SESSION['page_access']==""){?>
                                          <a class="href" href="https://www.fet.discoveree.io/recent-activity.php"><li>Recent Manufacturer Activity</li></a>

                                        <?}?>
                                    </ul>
                                    </div>
                            </li>

                            <?}?>
                           <?if($_SESSION['user_name']==""){?>
                            <li class="dropdown"><a href="#" oenmouseover="$(this).trigger('click');" data-toggle="dropdown" data-hover="dropdown">
     Product Database<span class="caret"></span>
  </a>
                          <div class="dropdown-menu solutions" style="left: 0px;">
                            <ul>
                            <a href="https://www.discoveree.io/product_database.php" class="href"><li>What is a Product Database?</li></a>
                            <a href="https://www.discoveree.io/login.php" class="href"><li>Power MOSFETs</li></a>
                            <a href="https://www.discoveree.io/login.php" class="href"><li>IGBTs</li></a>
                            <a href="https://www.discoveree.io/request_demo.php" class="href"><li>BJTs</li></a>
                            <a href="https://www.discoveree.io/login.php" class="href"><li>Diodes</li></a>
                            <a href="https://www.discoveree.io/request_demo.php" class="href"><li>Gate Drivers</li></a>
                            <a href="https://www.discoveree.io/contact-us.php" class="href"><li>Custom ProductDB</li></a>
                            </ul>
                          </div>
                            </li>
                            <li class="dropdown"><a href="#" oenmouseover="$(this).trigger('click');" data-toggle="dropdown" data-hover="dropdown">
     Analytics<span class="caret"></span>
  </a>
  <div class="dropdown-menu solutions" style="left: 0px;">
                            <ul>

                            <a href="https://www.discoveree.io/data-extraction.php" class="href"><li>Data Extraction</li></a>
                                                       <a href="https://www.discoveree.io/product_tracking.php" class="href"><li>Product Tracking</li></a>
                            <a href="https://www.discoveree.io/page/market_research/" class="href"><li>Market Research</li></a>
                            <a href="https://www.discoveree.io/page/cross-reference_search/" class="href"><li>Cross Reference Search</li></a>
                            <a href="https://www.discoveree.io/page/datasheet_comparisons/" class="href"><li>Datasheet Comparisons</li></a>
                            <a href="https://www.discoveree.io/contact-us.php" class="href"><li>Industry Specific Dashboards</li></a>

                                    </ul>
                                    </div>
                            </li>

                             <li class="dropdown"><a href="#" oenmouseover="$(this).trigger('click');" data-toggle="dropdown" data-hover="dropdown">
     Modeling<span class="caret"></span>
  </a>
  <div class="dropdown-menu solutions" style="left: 0px;">
                            <ul>
                            <a href="https://www.discoveree.io/page/mosfet_spice_models/" class="href"><li>Mosfet Spice Models</li></a>
                            <a href="https://www.discoveree.io/page/igbt_spice_model/" class="href"><li>IGBT Spice Models</li></a>
                            <a href="https://www.discoveree.io/page/bjt_spice_model/" class="href"><li>BJT Spice Models</li></a>
                            <a href="https://www.discoveree.io/page/diode_spice_model/" class="href"><li>Diodes Spice Models</li></a>
                                    </ul>
                                    </div>
                            </li>

  <li class="dropdown"><a href="#" oenmouseover="$(this).trigger('click');" data-toggle="dropdown" data-hover="dropdown">
     Simulations<span class="caret"></span>
  </a>
  <div class="dropdown-menu solutions" style="left: 0px;">
                            <ul>
                            <a href="https://www.discoveree.io/liveproductpage.php#livedatasheets" class="href"><li>Live Datasheets</li></a>
                            <a href="https://www.discoveree.io/product_database.php#ApplicationAndCircuitSimulation" class="href"><li>Application Simulation On Cloud</li></a>
                            <a href="https://www.discoveree.io/power_loss_dashboard.php" class="href"><li>Power Loss Dashboard</li></a>
                            <a href="#" class="href"><li>DC/DC Converter (<span style="color:#FF0000;font-size:12px;">coming soon</span>)</li></a>
                            <a href="#" class="href"><li>AC/DC Converter (<span style="color:#FF0000;font-size:12px;">coming soon</span>)</li></a>
                            <a href="#" class="href"><li>DC Motor (<span style="color:#FF0000;font-size:12px;">coming soon</span>)</li></a>
                            <a href="#" class="href"><li>Three Phase Motor (<span style="color:#FF0000;font-size:12px;">coming soon</span>)</li></a>
                            <a href="#" class="href"><li>LED Lighting (<span style="color:#FF0000;font-size:12px;">coming soon</span>)</li></a>
                                    </ul>
                                    </div>
                            </li>
                            

                            
                            <?}?>
                              <?if($_SESSION['user_name']!="" && $_SESSION['opage_access']!=""){

                                                 $o="SELECT * FROM category_page WHERE  status='Active' AND url LIKE '%discoveree_cat/dashb%' ORDER BY title";
                   $os=mysql_query($o);
                   while($ow=mysql_fetch_array($os)){
                   $gettitle=mysql_fetch_array(mysql_query("SELECT title FROM category_page WHERE id=".$ow['parent_id']));
                   if(strpos($_SESSION['opage_access'],str_replace("https://www.discoveree.io","",$ow['url']))!==false){
                   $oc[$gettitle[0]][$ow['title']]=$ow['url'];
                   $oco[$gettitle[0]][$ow['order_by']][$ow['title']]=$ow['url'];
                   if(strpos(strtolower($gettitle[0]),'diode')!==false)
                   $op_access[]='Diodes';
                   if(strpos(strtolower($gettitle[0]),'igbt')!==false)
                   $op_access[]='IGBT';
                   if(strpos(strtolower($gettitle[0]),'micro')!==false || strpos(strtolower($gettitle[0]),'mcu')!==false)
                   $op_access[]='MCU';
                   if(strpos(strtolower($gettitle[0]),'gate')!==false && strpos(strtolower($gettitle[0]),'driver')!==false)
                   $op_access[]='Gate Driver';
                   if(strpos(strtolower($gettitle[0]),'led')!==false && strpos(strtolower($gettitle[0]),'driver')!==false)
                   $op_access[]='LED Driver';

                   }
                   }
                  // if($_SESSION['user_name']=="dnp1976"){

                   foreach($oc as $ok=>$ov)
                   ksort($oco[$ok]);
                   unset($oc);
                   foreach($oco as $ok=>$ov){
                   foreach($ov as $ok1=>$ov1){
                   foreach($ov1 as $ok2=>$ov2){
                   $oc[$ok][$ok2]=$ov2;
                   }
                   }
                   }
                   unset($oco);
                  // }

                              ?>
<? if(in_array('IGBT',$op_access)){?>
                            <li class="dropdown"><a href="#" oenmouseover="$(this).trigger('click');" data-toggle="dropdown" data-hover="dropdown">
      IGBT<span class="caret"></span>
  </a>

                          <div class="dropdown-menu solutions" style="left: 0px;min-width:380px!important;">
                         <div class="col-md-12"> <ul>
                          <a href="https://www.discoveree.io/discoveree_cat/search_compare_parts.php?discoveree_cat_id=12" class="href"><li>Search Or Compare Parts</li></a>
  <a href="https://www.discoveree.io/discoveree_cat/advance_search.php?discoveree_cat_id=12" class="href"><li>Advance Search</li></a>
  <a href="https://www.discoveree.io/discoveree_cat/cross_reference_search.php?discoveree_cat_id=12" class="href"><li>Cross Reference</li></a>
  </ul>
   </div>                          <? foreach($oc as $ok=>$ov){
                              if(strpos(strtolower($ok),'igbt')!==false){
                             ?>
                            <div class="col-md-12">
                            
                            <ul>
                            <li class="nohover"><a><b><?=$ok;?></b></a></li>
                            <? foreach($ov as $ok1=>$ov1){
                            $d_i_d=trim(substr($ov1,strpos($ov1,'discoveree_cat_id=')+18,100));
                            if(strpos($d_i_d,'&')!==false)
                            $d_i_d=trim(substr($ov1,0,strpos($ov1,'&')));
                            
                            if(!in_array("discoveree_cat_id=".$d_i_d,$access_report['IGBTs']))
                            $access_report['IGBTs'][]="discoveree_cat_id=".$d_i_d;
                            ?>
                            <a href="<?=$ov1;?>" class="href"><li><?=$ok1;?></li></a>
                            <?}?>
                            </ul>

                                    </div>
                              <?}}?>
                          </div>
                            </li>
<?}?>
<? if(in_array('Diodes',$op_access)){

$dio_cat[27]="Avalanche Diode";
$dio_cat[36]="Bare Die";
$dio_cat[22]="ESD Diode";
$dio_cat[60]="Fast Recovery Diode";
$dio_cat[38]="PIN Diode";
$dio_cat[77]="Rectifier";
$dio_cat[24]="Schottky Diode";
$dio_cat[29]="SiC Schottky Diode";
$dio_cat[61]="Soft Recovery Diode";
$dio_cat[25]="Standard Diode";
$dio_cat[39]="Varactor Diode";
?>
                            <li class="dropdown"><a href="#" oenmouseover="$(this).trigger('click');" data-toggle="dropdown" data-hover="dropdown">
     Diodes<span class="caret"></span>
  </a>
                          <div class="dropdown-menu solutions" style="left: 0px;min-width:620px!important;height:550px;overflow:auto;">

                             <? foreach($oc as $ok=>$ov){
                              if(strpos(strtolower($ok),'diode')!==false){
                              ?>
                            <div class="col-md-12">

                            <ul>
                            <? foreach($dio_cat as $dio_catk=>$dio_catv){?>
                            <?if(strpos(strtolower($ok),'tvs')===false && strpos(strtolower($ok),'zener')===false){?>
                            <li class="nohover"><a><b><?=$dio_catv;?></b></a></li>
                                 <a href="https://www.discoveree.io/discoveree_cat/search_compare_parts.php?discoveree_cat_id=<?=$dio_catk;?>" class="href"><li>Search Or Compare Parts</li></a>
  <a href="https://www.discoveree.io/discoveree_cat/advance_search.php?discoveree_cat_id=<?=$dio_catk;?>" class="href"><li>Advance Search</li></a>
  <a href="https://www.discoveree.io/discoveree_cat/cross_reference_search.php?discoveree_cat_id=<?=$dio_catk;?>" class="href"><li>Cross Reference</li></a>
  </li>
  
     <?if(strpos(strtolower($ok),'zener')===false && strpos(strtolower($ok),'tvs')===false){?>
                            <? foreach($ov as $ok1=>$ov1){
                            $d_i_d=trim(substr($ov1,strpos($ov1,'discoveree_cat_id=')+18,100));
                            if(strpos($d_i_d,'&')!==false)
                            $d_i_d=trim(substr($ov1,0,strpos($ov1,'&')));

                            if(!in_array("discoveree_cat_id=".$d_i_d,$access_report[$dio_catv]))
                            $access_report[$dio_catv][]="discoveree_cat_id=".$d_i_d;

                            ?>
                            <a href="<?=$ov1;?>&c=1&cid[]=<?=$dio_catk;?>" class="href"><li><?=$ok1;?></li></a>
                            <?}}?>
                            
  <?}?>
  <?}?>


                              <?if(strpos(strtolower($ok),'tvs')!==false){?>
                                <li class="nohover"><a><b><?=$ok;?></b></a></li>
                              <a href="https://www.discoveree.io/discoveree_cat/search_compare_parts.php?discoveree_cat_id=78" class="href"><li>Search Or Compare Parts</li></a>
  <a href="https://www.discoveree.io/discoveree_cat/advance_search.php?discoveree_cat_id=78" class="href"><li>Advance Search</li></a>
  <a href="https://www.discoveree.io/discoveree_cat/cross_reference_search.php?discoveree_cat_id=78" class="href"><li>Cross Reference</li></a>
  
  <?}?>
                              <?if(strpos(strtolower($ok),'zener')!==false){?>
                                <li class="nohover"><a><b><?=$ok;?></b></a></li>
                              <a href="https://www.discoveree.io/discoveree_cat/search_compare_parts.php?discoveree_cat_id=23" class="href"><li>Search Or Compare Parts</li></a>
  <a href="https://www.discoveree.io/discoveree_cat/advance_search.php?discoveree_cat_id=23" class="href"><li>Advance Search</li></a>
  <a href="https://www.discoveree.io/discoveree_cat/cross_reference_search.php?discoveree_cat_id=23" class="href"><li>Cross Reference</li></a>

  <?}?>
                         <?if(strpos(strtolower($ok),'zener')!==false || strpos(strtolower($ok),'tvs')!==false){?>
                            <? foreach($ov as $ok1=>$ov1){
                            $d_i_d=trim(substr($ov1,strpos($ov1,'discoveree_cat_id=')+18,100));
                            if(strpos($d_i_d,'&')!==false)
                            $d_i_d=trim(substr($ov1,0,strpos($ov1,'&')));

                            if(!in_array("discoveree_cat_id=".$d_i_d,$access_report['Diodes']))
                            $access_report['Diodes'][]="discoveree_cat_id=".$d_i_d;

                            ?>
                            <a href="<?=$ov1;?>" class="href"><li><?=$ok1;?></li></a>
                            <?}}?>
                            </ul>

                                    </div>
                              <?}}?>
                          </div>
                            </li>
<?}?>
<? if(in_array('MCU',$op_access)){?>
                            <li class="dropdown"><a href="#" oenmouseover="$(this).trigger('click');" data-toggle="dropdown" data-hover="dropdown">
     MCU<span class="caret"></span>
  </a>
                          <div class="dropdown-menu solutions" style="left: 0px;min-width:620px!important;">
                             <? foreach($oc as $ok=>$ov){
                              if(strpos(strtolower($ok),'mcu')!==false){
                              ?>
                            <div class="col-md-12">

                            <ul>
                            <li class="nohover"><a><b><?=$ok;?></b></a></li>
                            <? foreach($ov as $ok1=>$ov1){
                              $d_i_d=trim(substr($ov1,strpos($ov1,'discoveree_cat_id=')+18,100));
                            if(strpos($d_i_d,'&')!==false)
                            $d_i_d=trim(substr($ov1,0,strpos($ov1,'&')));

                            if(!in_array("discoveree_cat_id=".$d_i_d,$access_report['MCU']))
                            $access_report['MCU'][]="discoveree_cat_id=".$d_i_d;

                            ?>
                            <a href="<?=$ov1;?>" class="href"><li><?=$ok1;?></li></a>
                            <?}?>
                            </ul>

                                    </div>
                              <?}}?>
                          </div>
                            </li>
<?}?>

<? if(in_array('Gate Driver',$op_access)){?>
                            <li class="dropdown"><a href="#" oenmouseover="$(this).trigger('click');" data-toggle="dropdown" data-hover="dropdown">
     Gate Driver<span class="caret"></span>
  </a>
                          <div class="dropdown-menu solutions" style="left: 0px;min-width:620px!important;">
                          
                             <div class="col-md-12"> <ul>
                          <a href="https://www.discoveree.io/discoveree_cat/search_compare_parts.php?discoveree_cat_id=83" class="href"><li>Search Or Compare Parts</li></a>
  <a href="https://www.discoveree.io/discoveree_cat/advance_search.php?discoveree_cat_id=83" class="href"><li>Advance Search</li></a>
  <a href="https://www.discoveree.io/discoveree_cat/cross_reference_search.php?discoveree_cat_id=83" class="href"><li>Cross Reference</li></a>
  </ul>
   </div>
   
                             <? foreach($oc as $ok=>$ov){
                              if(strpos(strtolower($ok),'gate driver')!==false){
                              ?>
                            <div class="col-md-12">

                            <ul>
                            <li class="nohover"><a><b><?=str_replace("DEMO","",$ok);?></b></a></li>
                            <? foreach($ov as $ok1=>$ov1){
                              $d_i_d=trim(substr($ov1,strpos($ov1,'discoveree_cat_id=')+18,100));
                            if(strpos($d_i_d,'&')!==false)
                            $d_i_d=trim(substr($ov1,0,strpos($ov1,'&')));

                            if(!in_array("discoveree_cat_id=".$d_i_d,$access_report['Gate Driver']))
                            $access_report['Gate Driver'][]="discoveree_cat_id=".$d_i_d;

                            ?>
                            <a href="<?=$ov1;?>" class="href"><li><?=$ok1;?></li></a>
                            <?}?>
                            </ul>

                                    </div>
                              <?}}?>
                          </div>
                            </li>
<?}?>


<? if(in_array('LED Driver',$op_access)){?>
                            <li class="dropdown"><a href="#" oenmouseover="$(this).trigger('click');" data-toggle="dropdown" data-hover="dropdown">
     LED Driver<span class="caret"></span>
  </a>
                          <div class="dropdown-menu solutions" style="left: 0px;min-width:620px!important;">
                             <div class="col-md-12"> <ul>
                          <a href="https://www.discoveree.io/discoveree_cat/search_compare_parts.php?discoveree_cat_id=84" class="href"><li>Search Or Compare Parts</li></a>
  <a href="https://www.discoveree.io/discoveree_cat/advance_search.php?discoveree_cat_id=84" class="href"><li>Advance Search</li></a>
  <a href="https://www.discoveree.io/discoveree_cat/cross_reference_search.php?discoveree_cat_id=84" class="href"><li>Cross Reference</li></a>
  </ul>
   </div>
   
                             <? foreach($oc as $ok=>$ov){
                              if(strpos(strtolower($ok),'led driver')!==false){
                              ?>
                            <div class="col-md-12">

                            <ul>
                            <li class="nohover"><a><b><?=str_replace("DEMO","",$ok);?></b></a></li>
                            <? foreach($ov as $ok1=>$ov1){
                              $d_i_d=trim(substr($ov1,strpos($ov1,'discoveree_cat_id=')+18,100));
                            if(strpos($d_i_d,'&')!==false)
                            $d_i_d=trim(substr($ov1,0,strpos($ov1,'&')));

                            if(!in_array("discoveree_cat_id=".$d_i_d,$access_report['LED Driver']))
                            $access_report['LED Driver'][]="discoveree_cat_id=".$d_i_d;

                            ?>
                            <a href="<?=$ov1;?>" class="href"><li><?=$ok1;?></li></a>
                            <?}?>
                            </ul>

                                    </div>
                              <?}}?>
                          </div>
                            </li>
<?}?>



                            <?}?>
                            
                              <?if(strpos($_SESSION['page_access'],'sync-buck')!==false || strpos($_SESSION['page_access'],'web-simulation-concept')!==false || strpos($_SESSION['page_access'],'appmodel-')!==false || strpos($_SESSION['page_access'],'unified')!==false || strpos($_SESSION['page_access'],'-igbt-')!==false){?>
                                 <li class="dropdown"><a href="#" oenmouseover="$(this).trigger('click');" data-toggle="dropdown" data-hover="dropdown">
     Modeling<span class="caret"></span>
  </a>
  <div class="dropdown-menu solutions" style="left: 0px;">
                            <ul>
                            
                               <?if(strpos($_SESSION['page_access'],'sync-buck-compare')!==false ){?>
                                        <a class="href" href="https://www.fet.discoveree.io/sync-buck-compare.php"><li>Calculator: DC-DC SyncBuck</li></a>

                            <?}?>
                            
                               <?if(strpos($_SESSION['page_access'],'unified_fet_igbt')!==false || $_SESSION['user_name']=="srai" || $_SESSION['user_name']=="d1np1976"){?>
                             <a href="https://www.discoveree.io/unified_fet_igbt.php" class="href"><li>Dashboard: Unified Von-BV</li></a>
                            <?}?>

                            <?if((strpos($_SESSION['page_access'],'dashboard-unified-pl:')!==false) || $_SESSION['user_name']=="srai" || $_SESSION['user_name']=="dn1p1976"){?>
                            <a href="https://www.discoveree.io/dashboard-unified-pl.php" class="href"><li>Dashboard: Unified Ploss-Von<br />(Eon & Eoff used and rest calculated)</li></a>
                            <?}?>
                            <?if(strpos($_SESSION['page_access'],'dashboard-unified-pl.php?show=eoneoff')!==false || $_SESSION['user_name']=="srai" || $_SESSION['user_name']=="d1np1976"){?>
                            <a href="https://www.discoveree.io/dashboard-unified-pl.php?show=eoneoff" class="href"><li>Dashboard: Unified Ploss-Von<br />(Eon & Eoff used and rest omitted)</li></a>
                            <?}?>

                            <?if(strpos($_SESSION['page_access'],'dashboard-unified-pl.php?show=eoss')!==false || $_SESSION['user_name']=="srai" || $_SESSION['user_name']=="d1np1976"){?>
                            <a href="https://www.discoveree.io/dashboard-unified-pl.php?show=eoss" class="href"><li>Dashboard: Unified Ploss-Von with Poss<br />(Eon & Eoff used and rest calculated)</li></a>
                            <?}?>

                            <?if(strpos($_SESSION['page_access'],'dashboard-unified-pl.php?show=eoneoffeoss')!==false || $_SESSION['user_name']=="srai" || $_SESSION['user_name']=="dn1p1976"){?>
                            <a href="https://www.discoveree.io/dashboard-unified-pl.php?show=eoneoffeoss" class="href"><li>Dashboard: Unified Ploss-Von with Poss<br />(Eon & Eoff used and rest omitted)</li></a>
                            <?}?>


                            <?if(strpos($_SESSION['page_access'],'dashboard-igbt-pl')!==false || $_SESSION['user_name']=="srai" || $_SESSION['user_name']=="dn1p1976"){?>
                            <a href="https://www.discoveree.io/dashboard-igbt-pl.php" class="href"><li>Dashboard: Unified Ploss-Von (Calculated)</li></a>
                            <li class="nohover" style="border-bottom:0px;padding:0px;"><hr /></li>
                            <?}?>



                            <?if($_SESSION['user_name']=="srai" || $_SESSION['user_name']=="dnp1976"){?>
                             <a href="https://www.discoveree.io/user/managespicemodel.php" target="_blank" class="href"><li>LIVE Application Simulation</li></a>
                             <?}?>
                             
                            <?if(strpos($_SESSION['page_access'],'sync-buck-converter')!==false){?>
                             <a href="https://www.fet.discoveree.io/sync-buck-converter.php" class="href"><li>LIVE SyncBuck Simulation</li></a>
                            <?}?>
                            
                             <?if($_SESSION['user_name']=="srai" || $_SESSION['user_name']=="dnp1976"){?>
                            <a href="https://www.discoveree.io/user/spice_model_working_status_report.php" target="_blank" class="href"><li>LIVE Batch Simulation</li></a>
                               <li class="nohover" style="border-bottom:0px;padding:0px;"><hr /></li>
                               <?}?>
                               

                             <?if(strpos($_SESSION['page_access'],'web-simulation-concept')!==false){?>
                             <a href="https://www.fet.discoveree.io/web-simulation-concept.php" class="href"><li>Web Simulation Concept</li></a>
                            <?}?>
                            <?if(strpos($_SESSION['page_access'],'appmodel-buck')!==false){?>
                            <a href="https://www.fet.discoveree.io/appmodel-buck.php" dtitle="part will show as basis of availability in input file" class="href"><li>Simulate Multiple Parts</li></a>
                            <?}?>
                            <?if(strpos($_SESSION['page_access'],'appmodel-multiple-new')!==false){?>
                            <a href="https://www.fet.discoveree.io/appmodel-multiple-new.php" dtitle="Default simulate will show as basis of output file" class="href"><li>Simulate Multiple Parts (Pre-loaded)</li></a>
                            <?}?>
                            <?if($_SESSION['user_name']=="srai" || $_SESSION['user_name']=="dnp1976"){?>
                             <a href="https://www.fet.discoveree.io/appmodel-single-new2.php" dtitle="basis of output file" class="href"><li>Simulate Single Part (Pre-loaded)</li></a>
                             <a href="https://www.fet.discoveree.io/spicemodel-report.php" dtitle="As basis of file in input folder" class="href"><li>Spice Model Availability</li></a>
                             <?}?>
                             



                                    </ul>
                                    </div>
                            </li>
                            <?}?>
                            
<?if(strpos($_SESSION['page_access'],'calculation.php?id=')!==false){?>

 <li class="dropdown"><a href="#" oenmouseover="$(this).trigger('click');" data-toggle="dropdown" data-hover="dropdown">
     Calculation<span class="caret"></span>
  </a>
  <div class="dropdown-menu solutions" style="left: 0px;">
                            <ul>
                      <?      $sql="SELECT * FROM manage_spicemodel_lists_user_input WHERE NOT nav='' ORDER BY order_by";
                    $rnav=mysql_query($sql);
                    while($rownav=mysql_fetch_array($rnav)){
                    if(strpos($_SESSION['page_access'],'calculation.php?id='.$rownav['netlist_id'].':')!==false){
                    ?>
                             <a href="https://www.discoveree.io/calculation.php?id=<?=$rownav['netlist_id'];?>" class="href"><li><?=stripslashes($rownav['nav']);?></li></a>
                     <?}}?>
                         </ul>
  </div>
  </li>

<?}?>



                             
                               <li class="dropdown"><a href="#" oenmouseover="$(this).trigger('click');" data-toggle="dropdown" data-hover="dropdown">
     Resources<span class="caret"></span>
  </a>
  <div class="dropdown-menu solutions" style="left: 0px;">
                            <ul>
                            <?if(strpos($_SESSION['page_access'],'kb-package-category')!==false){?>
                           <a target="_blank" href="https://www.discoveree.io/kb-package-category.php" class="href"><li>Package Categorization Report</li></a>
                           <?}?>
                           
                           <?if($_SESSION['user_name']=="srai" || $_SESSION['user_name']=="dnp1976" || strpos($_SESSION['page_access'],'discoveree_database_search')!==false){?>
                            <a href="https://www.discoveree.io/discoveree_database_search.php" class="href"><li>DiscoverEE DB Search</li></a>
                            <?}?>
                           
                           <?if($_SESSION['user_name']=="srai" || $_SESSION['user_name']=="dnp1976"){?>
                            <a target="_blank" href="https://www.discoveree.io/pricing.php" class="href"><li>Pricing</li></a>
                            <?}?>
                            <?if(strpos($_SESSION['page_access'],'our-technology')!==false){?>
                             <a dtarget="_blank" href="https://www.discoveree.io/our-technology.php" class="href"><li>Our Technology</li></a>
                             <?}?>
                            <a target="_blank" href="https://www.discoveree.io/press-release.php" class="href"><li>Industry News</li></a>
                            <?if($_SESSION['user_name']!=""){?>
                            <a dtarget="_blank" href="https://www.discoveree.io/user_guide.php" class="href"><li>User Guide</li></a>
                            <?}?>

                            <a starget="_blank" href="https://www.discoveree.io/explainer_video.php" class="href"><li>Explainer Video</li></a>
                              <?if($_SESSION['user_name']==""){?>
                            <a target="_blank" href="https://www.discoveree.io/about-us.php" class="href"><li>About Us</li></a>
                            <?}?>
                            <a target="_blank" href="https://www.discoveree.io/contact-us.php" class="href"><li>Contact Us</li></a>
                                    </ul>
                                    </div>
                            </li>
                            
                            
                            
         </div>
        <div class="col-md-<?if($_SESSION['user_name']==""){?>3<?}else{?>1<?}?>" style="padding-left:0px;padding-right:0px;<?if(strpos($_SERVER['REQUEST_URI'],"index")===false && $_SERVER['REQUEST_URI']!="/"){?>margin-top:5px;<?}?>">

        <ul class="globalNavActions">

        <?if($_SESSION['user_id']!=""){
        $user_first_name=$_SESSION['user_name'];
        if($_SESSION['user_first_name']!=""){
         $user_first_name=trim($_SESSION['user_first_name']);
         if(strpos($user_first_name,' ')!==false){
          $user_first_name=explode(' ',$user_first_name);
          $user_first_name=trim($user_first_name[0]);
         }
        }
        ?>
       <li class="globalNavActions__signup">
       <a class="zebra_tooltips ctaPrimary ctaPrimary--nav" style="cursor:pointer;" title="<center><?if($_SESSION['showkp']=="1" && abs($_SESSION['kp2_credit'])>111110){?><h5 style='line-height:25px;'>Total&nbsp;Credits:&nbsp;$<?=$_SESSION['kp2_credit'];?><br />Credits&nbsp;Used:&nbsp;$<?=$_SESSION['kp2_credit_used'];?><br />Credits&nbsp;Balance:&nbsp;$<?=($_SESSION['kp2_credit']-$_SESSION['kp2_credit_used']);?></h5><br /><?}?><?if($_SESSION['showkp']=="1" && abs($_SESSION['kp2_credit'])==0){?><h5>Total&nbsp;Use:&nbsp;$<?=$_SESSION['credituse'];?></h5><br /><?}?><?if($_SESSION['user_type']=="RUser"){?><h5>Available&nbsp;:&nbsp;$<?=$_SESSION['credit'];?></h5><br /><?}?><ul class='nav'>
       <?if($_SESSION['user_type']=="RUser" && $_SESSION['reg_type']==1 && $_SESSION['survey_completion']==0){?>
       <li class='active' style='background-color: #FFF;color:#000;margin-bottom:5px;'><a href='https://www.discoveree.io/survey/index-reg.php?s=<?=encrypt($_SESSION['user_id']);?>'>Survey Form</a></li>
       <?}?>
       <li class='active' style='background-color: #FFF;color:#000;margin-bottom:5px;'><a href='https://www.discoveree.io/user_landing_page_setting.php'>Landing Page</a></li>
       <?if($_SESSION['user_company']!="" && $_SESSION['admin_status']==1){?>
       <li class='active' style='background-color: #FFF;color:#000;margin-bottom:5px;'><a href='https://www.discoveree.io/credit_usage_report.php' style='padding:6px;'>Credit Usage Report</a></li>
       <?}?>
       <li class='active' style='background-color: #FFF;color:#000;margin-bottom:5px;'><a href='https://www.discoveree.io/my_activity.php' style='padding:6px;'>My Activity</a></li>
       <?if($_SESSION['xref_report']!=""){?>
       <li class='active' style='background-color: #FFF;color:#000;margin-bottom:5px;'><a href='https://www.discoveree.io/usage_report_crossreference.php'>XRef. Report</a></li>
       <?}?>
       <?if($_SESSION['user_name']=="Stephane@Inf" || $_SESSION['user_name']=="dnp1976" || $_SESSION['user_name']=="srai" || strpos($_SESSION['page_access'],'user_activity_report')!==false){?>
       <li class='active' style='background-color: #FFF;color:#000;margin-bottom:5px;'><a href='https://www.discoveree.io/user_activity_report.php'>User Activity Report</a></li>
       <?}?>

       <?if($_SESSION['user_name']=="dnp1976" || $_SESSION['user_name']=="srai"){?>
       <li class='active' style='background-color: #FFF;color:#000;margin-bottom:5px;'><a href='https://www.discoveree.io/report_crossreference_private.php'>Cross Report [Internal]</a></li>
       <?}?>
       <li class='active' style='background-color: #FFF;color:#000;margin-bottom:5px;'><a href='https://www.discoveree.io/change_password.php'>Change Password</a></li>
       <?if($_SESSION['user_name']=="dnp19716" || $_SESSION['user_name']=="sra1i"){?><?if($_SESSION['user_name']=="obselete"){?><li class='active' style='background-color: #FFF;color:#000;margin-bottom:5px;'><a href='https://www.discoveree.io/dashboard_setting.php'>Dashboard&nbsp;Settings</a></li><?}?>
       <li class='active' style='background-color: #FFF;color:#000;margin-bottom:5px;'><a href='https://www.discoveree.io/activity_report.php'>Activity Report</a></li><?}?>
       <li class='active' style='background-color: #FFF;color:#000;'><a href='https://www.discoveree.io/signout.php'>Signout</a></li></ul></center>"> <?=$user_first_name;?></a></li>
        <?}else{?>
        <li class="globalNavActions__login">
    <a href="https://www.discoveree.io/login.php" class="ctaSecondary ctaSecondary--nav">
        Log In    </a>
</li>
<li class="globalNavActions__signup">
<a href="https://www.discoveree.io/contribute_as_an_expert.php" class="zebra_tooltipss ctaPrimary ctaPrimary--nav"  stitle="<center>Dashboard<br />Market&nbsp;Intelligence<br />Detailed&nbsp;Comparisons<br />View&nbsp;Normalized&nbsp;Data<br />Application&nbsp;Modeling</center>">
        Contribute    </a>
</li>
  <?}?>

    </ul>

     </div>
          </div> </div>
          <?if((strpos($_SERVER['REQUEST_URI'],"index")===false || strpos($_SERVER['REQUEST_URI'],"survey")!==false) && $_SERVER['REQUEST_URI']!="/"){?>
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
<script src="/assets/zebra_tooltips.min.js"></script>
<link rel="stylesheet" href="/assets/css/bubble/zebra_tooltips.css" type="text/css">
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
<?}?>

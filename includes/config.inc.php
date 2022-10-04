<?php
 error_reporting("E_ERROR");
//ini_set("session.gc_maxlifetime", 7200);
//ini_set("session.cookie_lifetime", 7200);
// ini_set('memory_limit', '4096M');
//ini_set('session.cookie_domain', '.discoveree.io');
header("Content-Type: text/html; charset=ISO-8859-1");

//if(strpos($_SERVER['REQUEST_URI'],"/discoveree_url_")===false && strpos($_SERVER['HTTP_HOST'],".fet.")!==false && (strpos($_SERVER['REQUEST_URI'],"/user/")!==false || strpos($_SERVER['REQUEST_URI'],"/discoveree_cat/")!==false)){
//header("Location: https://www.discoveree.io".$_SERVER['REQUEST_URI']);
//exit;
//}

	session_start();
    $allow[]="checkout.php";
    $allow[]="survey_user.php";
    $allow[]="errorreporting.php";
    $allow[]="discover.php";
     $allow[]="discover-vds.php";
      $allow[]="dashboard-1.php";
      $allow[]="discover-rdson.php";
       $allow[]="dashboard-pl.php";
       $allow[]="dashboard-package-trends.php";
       $allow[]="dashboard-igbt-pl.php";
       $allow[]="dashboard-unified-pl.php";
        $allow[]="easysearch.php";
         $allow[]="advancesearch.php";
          $allow[]="cross_reference.php";
           $allow[]="compare.php";
           $allow[]="walk_through.php";
           $allow[]="cross_reference_epc.php";
           $allow[]="sync-buck-compare.php";
            $allow[]="sync-buck-compare-v5.php";
            $allow[]="packagereport.php";
             $allow[]="recent-activity.php";
             $allow[]="process-graph-report.php";
              $allow[]="part_details.php";
              if(strpos($_SERVER['REQUEST_URI'],"/discoveree_cat/")===false)
              $allow[]="datasheet_details.php";

              $allow[]="web-simulation-concept.php";
              $allow[]="appmodel-single-new2.php";
              $allow[]="spicemodel-report.php";
              $allow[]="appmodel-buck.php";
              $allow[]="appmodel-multiple-new.php";
               $allow[]="sync-buck-converter.php";
               $allow[]="syncbuckconverter.php";
               $allow[]="extraactions.php";
              $notallowforshift[]="refineload.php";
              $notallowforshift[]="refineload-pl.php";
              $notallowforshift[]="login.php";
              
if(strpos($_SERVER['REQUEST_URI'],"/discoveree_url_")===false && strpos($_SERVER['HTTP_HOST'],".fet.")===false && in_array(str_replace("/","",$_SERVER['SCRIPT_NAME']),$allow) && strpos($_SERVER['REQUEST_URI'],"/admin/")===false && strpos($_SERVER['REQUEST_URI'],"/user/")===false && strpos($_REQUEST['pageurl'],"discoveree_cat")===false){
//header("Location: https://www.fet.discoveree.io".$_SERVER['REQUEST_URI']);
//exit;
}else if(strpos($_SERVER['REQUEST_URI'],"/discoveree_url_")===false && strpos($_SERVER['HTTP_HOST'],".fet.")!==false && !in_array(str_replace("/","",$_SERVER['SCRIPT_NAME']),$allow) && !in_array(str_replace("/","",$_SERVER['SCRIPT_NAME']),$notallowforshift) && strpos($_SERVER['REQUEST_URI'],"/admin/")===false && strpos($_SERVER['REQUEST_URI'],"/user/")===false){
//header("Location: https://www.discoveree.io".$_SERVER['REQUEST_URI']);
//exit;
}

	@extract($_POST);
	@extract($_GET);
	@extract($_FILES);
	@extract($_SESSION);
	@extract($_SERVER);

		$db_host = 'localhost';
		$db_name = 'srai_discoveree';
		$db_user = 'root';
		$db_pass = '';


		define('SITE_SUB_PATH', '/discoveree_google_cloud/');
        define('tb_Prefix', '');
        define('pagelimit', '20');

        $dbcon =	mysql_connect($db_host, $db_user, $db_pass);
		mysql_select_db($db_name) or die("Could not connect to database. Please check configuration and ensure MySQL is running.");



      //   if(strtolower($_SESSION['user_company'])=="infineon")
      //  $_SESSION['data_condition']=" AND (((vds>0 AND vds<=400) OR vds<0) AND vthmax>=-7 AND vthmax<=7 AND LEAST(IF(rdson1max,rdson1max,999999),IF(rdson2max,rdson2max,999999),IF(rdson3max,rdson3max,999999),IF(rdson4max,rdson4max,999999))>0 AND LEAST(IF(rdson1max,rdson1max,999999),IF(rdson2max,rdson2max,999999),IF(rdson3max,rdson3max,999999),IF(rdson4max,rdson4max,999999))<=1000 AND NOT config like '%dual compl%' AND NOT config like '%dual asy%' AND NOT config like '%dual half%' AND NOT config like '%8%' AND NOT config like '%6%' AND NOT config like '%4%' AND NOT config like '%common drain%') ";
       //  else

//        $spcond=" AND ( vthmax>=-7 AND vthmax<=7 AND LEAST(IF(rdson1max,rdson1max,999999),IF(rdson2max,rdson2max,999999),IF(rdson3max,rdson3max,999999),IF(rdson4max,rdson4max,999999))>0 AND LEAST(IF(rdson1max,rdson1max,999999),IF(rdson2max,rdson2max,999999),IF(rdson3max,rdson3max,999999),IF(rdson4max,rdson4max,999999))<=1000 AND NOT config like '%dual compl%' AND NOT config like '%dual asy%' AND NOT config like '%dual half%' AND NOT config like '%8%' AND NOT config like '%6%' AND NOT config like '%4%' AND NOT config like '%common drain%') AND NOT consider_field like '%NOTMOSFET%' ";

        if($_SESSION['user_name']=="srai" || $_SESSION['user_name']=="Ritesh" || $_SESSION['user_name']=="dnp1976")
        $spcond=" AND abs(vds)>0 AND ( NOT config like '%dual half%' AND NOT config like '%8%' AND NOT config like '%6%' AND NOT config like '%4%' AND NOT config like '%common drain%') AND NOT consider_field like '%NOTMOSFET%' AND NOT consider_field like '%MISMATCH%' ";
        else if($_SESSION['user_name']=="Dongsup@ON")
        $spcond=" AND abs(vds)>0 AND ((((config like '%dual comp%' OR config like '%dual asy%') and manf='onsemi') OR config like 'single cascode' OR config like 'single' OR config like 'dual' OR config like '%single plus schottky%' OR config like '%dual plus%')) AND NOT consider_field like '%NOTMOSFET%' AND NOT consider_field like '%MISMATCH%' AND error_review=0 ";
        else
        $spcond=" AND abs(vds)>0 AND ((config like 'single cascode' OR config like 'single' OR config like 'dual' OR config like '%single plus schottky%' OR config like '%dual plus%')) AND NOT consider_field like '%NOTMOSFET%' AND NOT consider_field like '%MISMATCH%' AND error_review=0 ";


        if($_SESSION['material_access']=="" || $_SESSION['material_access']=="Si:")
        $_SESSION['data_condition']=" AND material in ('Si','') ".$spcond;
        else if($_SESSION['material_access']=="SiC:")
        $_SESSION['data_condition']=" AND material in ('SiC') ".$spcond;
        else if($_SESSION['material_access']=="GaN:")
        $_SESSION['data_condition']=" AND material in ('GaN') ".$spcond;
        else if($_SESSION['material_access']=="Si:SiC:")
        $_SESSION['data_condition']=" AND material in ('Si','','SiC') ".$spcond;
        else if($_SESSION['material_access']=="Si:GaN:")
        $_SESSION['data_condition']=" AND material in ('Si','','GaN') ".$spcond;
        else if($_SESSION['material_access']=="SiC:GaN:")
        $_SESSION['data_condition']=" AND material in ('SiC','GaN') ".$spcond;
        else if($_SESSION['material_access']=="Si:SiC:GaN:")
        $_SESSION['data_condition']=" AND material in ('Si','','SiC','GaN') ".$spcond;




        if($_SESSION['manf_access']==""){
        $_SESSION['manf_access_condition']=" AND manf in ('aos','vishay','onsemi','renesas','rohm','infineon','toshiba','st','microsemi','nexperia','diodes') ";
        $_SESSION['data_condition'].=$_SESSION['manf_access_condition'];
        }else{
        $manf_access_cond=$_SESSION['manf_access'];
        $manf_access_cond=str_replace(":","','",$manf_access_cond);
        $manf_access_cond=substr($manf_access_cond,2,strlen($manf_access_cond)-4);
        $_SESSION['manf_access_condition']=" AND manf in (".$manf_access_cond.") ";
        $_SESSION['data_condition'].=$_SESSION['manf_access_condition'];
        }
        
        if($_SESSION['show_vds']!=""){

         $dvds_1=explode("*",$_SESSION['show_vds']);
    $vdscond1=" AND (";
 foreach($dvds_1 as $vds){
 if($vds!=""){
 $_REQUEST['vds_user']=$vds;
  $channel=substr($_REQUEST['vds_user'],0,1);
  $_REQUEST['vds_user']=str_replace(":","",str_replace("Pch","",str_replace("Nch","",$_REQUEST['vds_user'])));
  $vdsminmax=explode("-",$_REQUEST['vds_user']);
  $vdsmin=$vdsminmax[0];
   $vdsmax=$vdsminmax[1];
    // $manfcond.=" AND channel LIKE '%".$channel."%'";
   if($channel=="N"){
   $vdscond1.=" (vds>=".$vdsmin."";
   $vdscond1.=" AND vds<=".$vdsmax.") ";
   }
   if($channel=="P"){
   $vdscond1.=" (vds<=-".$vdsmin."";
   $vdscond1.=" AND vds>=-".$vdsmax.") ";
   }
    $vdscond1.=" OR ";
 } }
 $vdscond1=substr($vdscond1,0,strlen($vdscond1)-3);
    $vdscond1.=") ";
    $vdscond1=str_replace("AND vds<=)",")",$vdscond1);
   $_SESSION['data_condition'].=$vdscond1;

        }
        
        if($_SESSION['show_qualification']!=""){
        $qualcond="";
        if(strpos($_SESSION['show_qualification'],"Yes:No:")!==false || strpos($_SESSION['show_qualification'],"No:Yes:")!==false)
   $qualcond.=" AND (auto='Auto' OR auto='Yes' OR auto='' OR auto='0' OR auto='No') ";
  else if(strpos($_SESSION['show_qualification'],"No:")!==false && strpos($_SESSION['show_qualification'],"Yes:No:")===false && strpos($_SESSION['show_qualification'],"No:Yes:")===false)
$qualcond.=" AND (auto='' OR auto='0' OR auto='No') ";
  else if(strpos($_SESSION['show_qualification'],"Yes:")!==false && strpos($_SESSION['show_qualification'],"Yes:No:")===false && strpos($_SESSION['show_qualification'],"No:Yes:")===false)
$qualcond.=" AND (auto='Auto' OR auto='Yes') ";

    $_SESSION['data_condition'].=$qualcond;
        }
        

        //else
        //$_SESSION['data_condition']=" AND NOT manf in ('epc','transphorm') AND (((vds>0 AND vds<1500) OR vds<0) AND vthmax>=-7 AND vthmax<=7 AND LEAST(IF(rdson1max,rdson1max,999999),IF(rdson2max,rdson2max,999999),IF(rdson3max,rdson3max,999999),IF(rdson4max,rdson4max,999999))>0 AND LEAST(IF(rdson1max,rdson1max,999999),IF(rdson2max,rdson2max,999999),IF(rdson3max,rdson3max,999999),IF(rdson4max,rdson4max,999999))<=1000 AND NOT config like '%dual compl%' AND NOT config like '%dual asy%' AND NOT config like '%dual half%' AND NOT config like '%8%' AND NOT config like '%6%' AND NOT config like '%4%' AND NOT config like '%common drain%') ";


//require_once("Mail/Mail.php");
// require_once("Mail/Mail/mime.php");

   $str_client="";
   $clu="";
   if($_SESSION['user_type']=="User"){
   foreach(split(":",$_SESSION['user_client']) as $clt){
   if($clt>0)
   $clu.=$clt.",";
   }
   if($clu!="")
     $str_client=" AND client_id in (".substr($clu,0,strlen($clu)-1).")";
   }

  if($_SESSION['user_name']==""){
 $checklog=mysql_fetch_array(mysql_query("SELECT user_ip FROM `users_log` where user_ip='".$_SERVER['REMOTE_ADDR']."' AND username='' AND access_time LIKE '".date("Y-m-d ")."%' GROUP BY user_ip HAVING count(*)>150"));
  if($checklog[0]!=""){
   echo "<br /><br /><center>Suspicious activity is detected. You will not able to access website today. Please consult to support@discoveree.io for more details.</center>";
   exit;
  }
  }
 
        function sqlquery($rs='exe',$tablename,$arr,$update='',$id='',$update2='',$id2=''){

	$sql = mysql_query("DESC ".tb_Prefix."$tablename");
 	if($update == '')
		$makesql = "insert into ";
	else
		$makesql = "update " ;


	$makesql .= tb_Prefix."$tablename set ";

	$i = 1;
	while($row = mysql_fetch_assoc($sql)){
		if(array_key_exists($row['Field'], $arr)){


    if(ms_addslashes((is_array($arr[$row['Field']]))?implode(":",$arr[$row['Field']]):$arr[$row['Field']])!="nochange") {
    if($i != 1)
                 $makesql .= ", ";
				$makesql .= $row['Field']."='".ms_addslashes((is_array($arr[$row['Field']]))?implode(":",$arr[$row['Field']]):$arr[$row['Field']])."'";
				}
				$i++;
			}
		}

	if($update)
		$makesql .= " where ".$update."='".$id."'".(($update2 && $id2)?" and ".$update2."='".$id2."'":"");

 	mysql_query($makesql);

	return ($update)?$id:mysql_insert_id();
}
function ms_addslashes($var)
{
	return is_array($var) ? array_map('ms_addslashes', $var) : addslashes(stripslashes(trim($var)));
}
function addlogs($b,$t,$s=0){
 mysql_query("INSERT INTO logs SET batch_id='".$b."',title='".$t."',user='".$_SESSION['user_name']."',steps='".$s."'");
}
function manflabel($str){
$str=strtolower($str);
if($str=="aosmd" || $str=="aos")
$str="AOS";
else if($str=="st")
$str="ST";
else if($str=="onsemi")
$str="ON Semi";
else if($str=="ti")
$str="TI";
else if($str=="unitedsic")
$str="UnitedSiC";
else if($str=="hestia-power")
$str="HestiaPower";
else if($str=="epc")
$str="EPC";
else if($str=="non-automotive")
$str="Non-Automotive";
else if($str=="single + diode")
$str="Single with Diode";
else
$str=ucfirst($str);
return $str;
}


function encrypt($text)
{
    if(trim($text)==""){
    return "";
    }else{
$key = '12345678011120';
$iv = (openssl_cipher_iv_length('aes-256-cbc'));
$encrypted = openssl_encrypt($text, 'aes-256-cbc', $key, 0, $iv);
return $garble=base64_encode($encrypted . '::' . $iv);
  }
}

function decrypt($text)
{
    if(trim($text)==""){
    return "";
    }else{
$key = '12345678011120';
list($encrypted_data, $iv) = explode('::', base64_decode($text), 2);
return openssl_decrypt($encrypted_data, 'aes-256-cbc', $key, 0, $iv);
    }
}
?>

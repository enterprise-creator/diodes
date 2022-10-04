<?php include "includes/config.inc.php";?>
<?php
//if($_SESSION['user_id']==""){
//   header("Location: login.php?page=press-release");
//exit;
//}
mysql_query("SET NAMES utf8");
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

	</style>
<section id="services">
<div class="container">


<div class="row">
    <center> <h1 style="margin:0px;padding:0px;">Press Releases</h1>  </center>

    <?
$month=date("M");
$month1=date("M",strtotime("-1 months"));

$sql="SELECT manf FROM news GROUP BY manf ORDER BY FIELD(manf,'infineon','onsemi','vishay','st','renesas','aosmd','toshiba','nexperia','rohm','microsemi')";
$result=mysql_query($sql);
$cnt=0;
while($row=mysql_fetch_array($result)){
$cnt++;
?>
<div style="margin-top:10px;margin-left:10px;margin-right:10px;background-color:#f8f8f8;margin-bottom:10px;">
<div style="padding:10px;">
                <div  style="border-bottom:1px solid #CCC;">
                     <h4>
                         <?=manflabel($row[0]);?>
                     </h4>
                    </div>
                      <?
//                    $sql="SELECT * FROM news WHERE (date like '%".$month."%' OR date like '%".$month1."%') AND date like '%".date("Y")."%' AND manf='".$row['manf']."' ORDER BY STR_TO_DATE(date, '%M %d, %Y') DESC LIMIT 3";
                    $sql="SELECT * FROM news WHERE  manf='".$row['manf']."' ORDER BY STR_TO_DATE(date, '%M %d, %Y') DESC LIMIT 3";
$results=mysql_query($sql);
while($rows=mysql_fetch_array($results)){
?>
<b><?=$rows['date'];?></b><br />
<a href="<?=$rows['url'];?>" target="manf<?=$cnt;?>"><?=$rows['title'];?></a>
<br /><br />
<?}?>
<a href="morepressrelease/<?=$row[0];?>">View More</a>

                </div>
                </div>
                <?}?>
                
    </div>

</div>
 </section>
<!-- Service Section End -->
<?include("footer.php");?>

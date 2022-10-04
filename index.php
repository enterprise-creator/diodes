<?php
include "includes/config.inc.php";
mysql_query("SET NAMES utf8");
if($_REQUEST['clicklog']=="yes"){


$sql="INSERT INTO users_log SET username='".($_SESSION['user_name']!=''?$_SESSION['user_name']:'Guest')."',page_url='index.php/?nav=".$_REQUEST['title']."',user_ip='".$_SERVER['REMOTE_ADDR']."',user_country='".$_SESSION['user_country_name']."',user_city='".$_SESSION['user_city']."'";
mysql_query($sql);
exit;
}
?>
<?
include("header.php");
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
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

.card-overlay {
  background: rgba(0, 0, 0, 0.5);
}
.personailse-helper-text {
    font-weight: bold;
    font-size: 32px!important;
    line-height: 1.5em;
    margin-bottom: 30px;
}
p.dark-text {
    font-size: 20px;
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
h3 {text-align:center;}
.shadow {
  -webkit-box-shadow: 4px 4px 0px 0px #000;  /* Safari 3-4, iOS 4.0.2 - 4.2, Android 2.3+ */
  -moz-box-shadow:    4px 4px 0px 0px #000;  /* Firefox 3.5 - 3.6 */
  box-shadow:         4px 4px 0px 0px #000;  /* Opera 10.5, IE 9, Firefox 4+, Chrome 6+, iOS 5 */
}


</style>
<!-- Service Section -->
<section id="services">
<!-- start -->
<link rel="stylesheet" href="assets/fonts/font-awesome/font-awesome.min.css">
<div class="container  product_details" style="height:700px;padding:0px;background-color:#000;background:url('images/DiscoverEE_Product_Intelligence_Platform.jpg'), rgba(0,0,0,1);background-position: right -220px top; background-repeat: no-repeat;margin-bottom:20px;">
<br /><br /><br /><br /><br />
<center>
<h1 class="mb10 heading-title personailse-helper-text" style="font-size:42px!important;color:#FFF;">DiscoverEE<br />
is a Product Intelligence Platform<br />
that enables <span style="color:#ff9300;"><i>you</i></span> to</h1>
<br /><br /><br />
<hr style="width:80%;" />
 <div class="mb10 heading-title personailse-helper-text" style="font-size:32px!important;color:#ff9300;margin-top:70px;height:90px;">

 <span
     class="txt-rotate"
     data-period="3000"
     data-rotate='[  "SELL MORE FASTER", "GROW FASTER", "DESIGN LEADING PRODUCTS", "SECURE YOUR SUPPLY CHAINS", "ACHIEVE COST SAVINGS", "RELAX A LITTLE", "DO MORE FASTER" ]'>DO MORE FASTER</span>
  </div>
</center>
<div style="float:right;color:#FFF;margin-right: 2%;margin-top:0px;"><div class="findouthow" anchor="start" style="ddisplay:none;cursor:pointer;color:#ff9300;font-size:16px;">Find out how <i class="fa fa-angle-double-down"></i></div></div>
</div>
<script>
var TxtRotate = function(el, toRotate, period) {
  this.toRotate = toRotate;
  this.el = el;
  this.loopNum = 0;
  this.period = parseInt(period, 10) || 2000;
  this.txt = '';
  this.tick();
  this.isDeleting = false;
};

TxtRotate.prototype.tick = function() {
  var i = this.loopNum % this.toRotate.length;
  var fullTxt = this.toRotate[i];

  if (this.isDeleting) {
    this.txt = fullTxt.substring(0, this.txt.length - 1);
  } else {
    this.txt = fullTxt.substring(0, this.txt.length + 1);
  }

  this.el.innerHTML = '<span class="wrap">'+this.txt+'</span>';

  var that = this;
  var delta = 200 - Math.random() * 100;

  if (this.isDeleting) { delta /= 2; }

  if (!this.isDeleting && this.txt === fullTxt) {
    delta = this.period;
    this.isDeleting = true;
  } else if (this.isDeleting && this.txt === '') {
    this.isDeleting = false;
    this.loopNum++;
    delta = 200;
  }

  setTimeout(function() {
    that.tick();
  }, delta);
};

window.onload = function() {
  var elements = document.getElementsByClassName('txt-rotate');
  for (var i=0; i<elements.length; i++) {
    var toRotate = elements[i].getAttribute('data-rotate');
    var period = elements[i].getAttribute('data-period');
    if (toRotate) {
      new TxtRotate(elements[i], JSON.parse(toRotate), period);
    }
  }
  // INJECT CSS
  var css = document.createElement("style");
  css.type = "text/css";
  css.innerHTML = ".txt-rotate > .wrap { border-right: 0px solid #000 }";
  document.body.appendChild(css);
  $('.findouthow').show();
};

</script>

<!-- end -->

<!-- start -->
<style>
.link_s{margin-left:40px;margin-top:60px;width:370px;padding:20px;background-color:#000;opacity: 0.8;color:#FFF;font-size:20px;line-height:36px;}
@media screen and (max-width:620px) {
.link_s{margin-left:0px!important;}
}
.link_s a{color:#FFF;}
.link_s a:hover{color:#ff9300;}
</style>
<div style="clear:both;"></div>
<div  class="container product_details start" style="height:600px;background-image: url('images/PRODUCT_LANDSCAPE_VISUALIZATIONS.jpg');background-repeat: no-repeat;background-size: 100% 100%;margin-bottom:20px;">
<div class="link_s">
<br />
<h1 class="mb10 heading-title personailse-helper-text" style="font-size:28px!important;">PRODUCT LANDSCAPE VISUALIZATIONS</h1>
<hr style="border-top: 1px solid #2196f3;" />
<a target="_blank" href="https://www.discoveree.io/product_tracking.php">Discovering New Products</a><br />
<a target="_blank" href="https://www.discoveree.io/page/market_research/">Market Trends</a><br />
<a target="_blank" href="https://www.discoveree.io/page/market_research/">Competitor Analysis</a><br />
<a target="_blank" href="https://www.discoveree.io/page/datasheet_comparisons/">Detailed Product Comparisons</a><br />
<a target="_blank" href="https://www.discoveree.io/power_loss_dashboard.php">Performance Estimates</a><br />
<a target="_blank" href="https://www.discoveree.io/product_database.php">Creating Best Possible Designs</a><br /><br />

</div>
</div>
<!-- end -->

<!-- start -->
<div style="clear:both;"></div>
<div  class="container product_details" style="height:600px;background-image: url('images/INDUSTRY_BEST_CROSS-REFERENCES.jpg');background-repeat: no-repeat;background-size: 100% 100%;margin-bottom:20px;">
<div class="link_s" xstyle="margin-left:40px;margin-top:100px;width:370px;padding:20px;background-color:#000;opacity: 0.8;color:#FFF;font-size:20px;line-height:36px;">
<br />
<h1 class="mb10 heading-title personailse-helper-text" style="font-size:28px!important;">INDUSTRY BEST CROSS-REFERENCES</h1>
<hr style="border-top: 1px solid #2196f3;" />
<a target="_blank" href="https://www.discoveree.io/page/cross-reference_search/">Finding Alternative Products</a><br />
<a target="_blank" href="https://www.discoveree.io/page/cross-reference_search/">Securing Your Supply Chains</a><br />
<a target="_blank" href="https://www.discoveree.io/product_database.php">Optimizing BoM Costs</a><br />
<a target="_blank" href="https://www.discoveree.io/product_database.php">Finding Best Fit Products</a>
<br /><br />

</div>
</div>
<!-- end -->
<!-- start -->
<style>
.p{border-bottom:1px solid #2196f3;bordevr-radius:  5px;bbox-shadow: 5px -5px 2px #8f8f8f;cursor:pointer;float:left;margin-left:6%;margin-top:50px;width:25%;padding:2px;dbackground-color:#ff9300;color:#FFF;font-size:16px;line-height:30px;}
.box .p:hover,.box .active{color:#ff9300;}
@media screen and (max-width:620px) {
.box .p{display:block;width:100%;margin-left:0px;}
}
</style>
<div style="clear:both;"></div>
<div  class="container product_details box" style="margin-top:10px;background-color:#000;margin-bottom:20px;padding-bottom:20px;">
<div class="p active" anchor="single-item-componentthmb">
<h1 class="mb10 heading-title personailse-helper-text" style="text-align:center;font-size:28px!important;">DiscoverEE For Component Manufacturers</h1>
</div>
<div class="p" anchor="single-item-hardwarethmb">
<h1 class="mb10 heading-title personailse-helper-text" style="text-align:center;font-size:28px!important;">DiscoverEE For Hardware/System Manufacturers</h1>
</div>
<div class="p" anchor="single-item-distributorsthmb">
<h1 class="mb10 heading-title personailse-helper-text" style="text-align:center;font-size:28px!important;padding:20px;">DiscoverEE For Distributors</h1>
</div>


</div>
<!-- end -->

<style>
.thmb{cursor:pointer;color:#FFF;padding:10px;font-size:20px!important;font-weight:bold;}
.thmb:hover,.thmb-active{color:#ff9300;}
.leftblock{float:left;width:400px;height:600px;font-size:28px;}
.singleitem{margin-top:0px;padding:20px;}
@media screen and (max-width:620px) {
.link_s{margin-left:0px!important;}
.fadeind{display:inline-block;width:100%;}
.leftblock{width:100%;}
}
</style>

<!-- start -->
<div id="single-item-componentthmb" class="sl">
<div class="container product_details" style="padding:0px;">
<div class="leftblock">

<div style="width:100%;background-color: rgba(0,0,0,1);height:600px;border-bottom:1px solid #EFEFEF;">
<div style="color:#FFF;height:100%;">
<div xstyle="position:absolute;">
<div class="singleitem single-item-componentthmb">
<h1 class="mb10 heading-title personailse-helper-text" style="font-size:22px!important;">DiscoverEE For Component Manufacturers</h1>
<hr style="border-top: 1px solid #2196f3;" />

<div id="thmb0" slideid="0" class="thmb thmb-active">
<div>You Design, Manufacture and Market Leading Components</span></div>
</div>
<div id="thmb1" slideid="1" class="thmb">
<div>We help You Visualize The Entire Product Landscape</span></div>
</div>
<div id="thmb2" slideid="2" class="thmb">
<div>By Creating A Standardized Product Database For Your Components</span></div>
</div>
<div id="thmb3" slideid="3" class="thmb">
<div>So You Can Quickly Identify Market Opportunities, Design Leading Products and Grow Your Business</span></div>
</div>

</div>
</div>
</div>


</div>
</div>
<div class="fadeind single-item-component"  style="overflow: hidden; ">
    <img src="images/Component_Manufacturers-you_design.jpg" style="background-size:cover;width:100%;height:600px;" />

          <img src="images/Component_Manufacturers-we_help.jpg" style="background-size:cover;width:100%;height:600px;" />

          <img src="images/Component_Manufacturers-By_Creating.jpg" style="background-size:cover;width:100%;height:600px;" />

          <img src="images/Component_Manufacturers-Quickly_Identify.jpg" style="background-size:cover;width:100%;height:600px;" />

</div>
</div>
</div>
<!-- end -->
<!-- start -->
<div id="single-item-hardwarethmb" class="sl" style="display:none;">
<div class="container product_details" style="padding:0px;">
<div class="leftblock" dstyle="float:left;width:400px;height:600px;font-size:28px;">

<div style="width:100%;background-color: rgba(0,0,0,1);height:600px;border-bottom:1px solid #EFEFEF;">
<div style="color:#FFF;height:100%;">
<div xstyle="position:absolute;">
<div dstyle="margin-top:0px;padding:20px;width:54%;" class="singleitem single-item-hardwarethmb">
<h1 class="mb10 heading-title personailse-helper-text" style="font-size:22px!important;">DiscoverEE For Hardware Design Engineers</h1>
<hr style="border-top: 1px solid #2196f3;" />

<div id="thmb0" slideid="0" class="thmb thmb-active">
<div>You Design, Manufacture and Market Leading Hardware Products</span></div>
</div>
<div id="thmb1" slideid="1" class="thmb">
<div>We help You Visualize The Entire Product Landscape</span></div>
</div>
<div id="thmb2" slideid="2" class="thmb">
<div>By Creating A Standardized Product Database For Your Components</span></div>
</div>
<div id="thmb3" slideid="3" class="thmb">
<div>So You Can Design And Sell Awesome Products</span></div>
</div>

</div>
</div>
</div>


</div>
</div>
<div class="fadeind single-item-hardware"  style="overflow: hidden; ">
          <img src="images/Hardware_Design_Engineers-you_design.jpg" style="background-size:cover;width:100%;height:600px;" />

          <img src="images/Hardware_Design_Engineers-we_help.jpg" style="background-size:cover;width:100%;height:600px;" />

          <img src="images/Hardware_Design_Engineers-By_Creating.jpg" style="background-size:cover;width:100%;height:600px;" />

          <img src="images/Hardware_Design_Engineers-you_can_design_sell.jpg" style="background-size:cover;width:100%;height:600px;" />

</div>
</div>
</div>
<!-- end -->
<!-- start -->
<div id="single-item-distributorsthmb" class="sl" style="display:none;">
<div class="container product_details" style="padding:0px;">
<div class="leftblock" dstyle="float:left;width:400px;height:600px;font-size:28px;">

<div style="width:100%;background-color: rgba(0,0,0,1);height:600px;border-bottom:1px solid #EFEFEF;">
<div style="color:#FFF;height:100%;">
<div xstyle="position:absolute;">
<div dstyle="margin-top:0px;padding:20px;width:50%;" class="singleitem single-item-distributorsthmb">
<h1 class="mb10 heading-title personailse-helper-text" style="font-size:22px!important;">DiscoverEE For Distributors</h1>
<hr style="border-top: 1px solid #2196f3;" />


<div id="thmb0" slideid="0" class="thmb thmb-active">
<div>You Market And Sell Products From Your Preferred Suppliers</span></div>
</div>
<div id="thmb1" slideid="1" class="thmb">
<div>We Help You Identify The Best Products And Manufacturers To Stock</span></div>
</div>
<div id="thmb2" slideid="2" class="thmb">
<div>By Providing You With Industry Best Products and Cross-References</span></div>
</div>
<div id="thmb3" slideid="3" class="thmb">
<div>So Your Teams Can Achieve Their Best Performance And Grow Your Profits</span></div>
</div>

</div>
</div>
</div>


</div>
</div>


<div class="fadeind single-item-distributors"  style="overflow: hidden; ">
    <img src="images/Distributors-Market_And_Sell.jpg" style="background-size:cover;width:100%;height:600px;" />

          <img src="images/Distributors-we_help.jpg" style="background-size:cover;width:100%;height:600px;" />

          <img src="images/Distributors-By_Providing.jpg" style="background-size:cover;width:100%;height:600px;" />

          <img src="images/Distributors-Teams_Can_Achieve.jpg" style="background-size:cover;width:100%;height:600px;" />

</div>
</div>
</div>
<!-- end -->
<!-- start -->
<div style="clear:both;"></div>
<div  class="container product_details" style="margin-bottom:14px;">
<center><h1 class="mb10 heading-title personailse-helper-text" style="font-size:22px!important;margin-top:0px;">Watch our 2-minute explainer video to find out how DiscoverEE can help you</h1></center>
 <video id="video" controls width="100%" height="100%">
    <source src="collateral/EU/DiscoverEE_Explainer_Video_v1.mp4" type="video/mp4">
    <track src="collateral/EU/DiscoverEE_Explainer_Video_v1.srt" kind="subtitle" srclang="en-US" label="English" />
    Sorry, your browser doesn't support embedded videos,
but don't worry, you can <a href="collateral/EU/DiscoverEE_Explainer_Video_v1.mp4">download it</a>
and watch it with your favorite video player!
</video>
</div>
<!-- end -->

<!-- start -->
<style>
.signup-price-helper {
    font-size: 25px;
    color: #161616;
    line-height: 1.6;
}
            .index-header-signup-form {
    display: table-cell;
    padding-right: 2px;
    float: none;
}
.index-header-signup-form .email {
    padding: 10px;
    font-size: 16px;
}
.index-header-signup-form  .email {
    background-color: #fff;

    color: #797676;
}

 .index-header-signup-form .email {
    margin-bottom: 0!important;
}
  .index-header-signup-form input,.index-header-signup-form select {

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
    padding: 10px;
    font-size: 20px;
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
     background-color: #ff9300;
    color:#000;

    text-transform: uppercase;
    cursor: pointer;
    position: relative;
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
    background-color: #000;
    color:#FFF;
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
.col-half-offset{
    width:21.75%;
    padding: 0px;
    padding-right:5px;
}
.col-half-offset2{
    width:20.50%;
    padding: 0px;
    padding-right:5px;
}
.rbtn{width:18%;padding: 0px;}
@media screen and (max-width:620px) {
.col-half-offset2{display:inline-block;width:100%;padding:10px;}
.rbtn{width:100%;padding: 10px;}
}
</style>
<div class="container product_details" style="margin-bottom:20px;">
<div class="col-md-12 sub-form-container-1">
   <center>
   <h3 style="padding:0px;padding-bottom:10px;">Try Us Out. Request A Demo.</h3>

<div class="index-header-signup-form" style="padding-right: 0px;">

<div class="col-xs-2 col-half-offset2">
<input class="email input-standard-grey input-white" id="requestdemofn" name="first_name" required="" placeholder="Name" type="text">
</div>
<div class="col-xs-2 col-half-offset2">
<input class="email input-standard-grey input-white" id="requestdemocn" name="company_name" required="" placeholder="Company Name" type="text">
</div>
<div class="col-xs-2 col-half-offset2">
<input class="email input-standard-grey input-white" id="emailrequestdemo" name="email" required="" placeholder="Your email address" type="email">
</div>
<div class="col-xs-2 col-half-offset2">
<select id="requestdemojf" class="email input-standard-grey input-white" name="job_function" style="width: 100%;">
 <option value="">--Job Function--</option>
 <option value="Hardware">Hardware</option>
<option value="Purchasing">Purchasing</option>
<option value="Marketing">Marketing</option>
<option value="Business Management">Business Management</option>
<option value="Applications">Applications</option>
<option value="FAE">FAE</option>
<option value="Sales">Sales</option>
<option value="Management">Management</option>
<option value="Others">Others</option>
 </select>
</div>
<div class="col-xs-2 rbtn">

											<div class="index-header-signup-form">
           <button class="subscr-btn ellipses bold request_demo" style="padding-left:15px!important;padding-right:15px!important;">Request Demo</button>
											</div>
</div> </div>
											<span class="demomsg" style="color:#FF0000;"></span>
 <br /></center>

</div>
</div>

<!-- end -->
<!-- start -->
<link rel='stylesheet' href='css/slick.css'>
<link rel='stylesheet' href='css/slick-theme.css'>
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
  fill: #ff9300;
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

.slick-dotsx li button:before {
  color: rgba(255, 255, 255, 0.4);
  opacity: 1;
  font-size: 8px;
  content: none;
}

.slick-dotsx li.slick-active button:before {
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
<div class="container product_details" style="margin-bottom:20px;">
   <div class="col-md-12" style="background-color: #4e6978;text-align:center;color:#FFF;">
 <h3 class="mb10 heading-title personailse-helper-text">Customer Testimonials</h3>
  <div class='content'>
  <div class='slider single-item'>
<!-- start 1 -->
    <div class='quote-container'>
     <div class='quote'>
        <blockquote style="border:0px;font-size:22px;color:#FFF;">
          <p>
          "DiscoverEE provides visual analysis of Power MOSFET landscape of over 25,000 products using their easy to use industry specific dashboards.

My product marketing team and I have been using DiscoverEE for over two years. We are very satisfied since it provides us product and market insights while saving us valuable time."
 <br />
</p>
          <cite style="border:0px;font-size:22px;">
            Stéphane Ernoux
            <br>
            Senior Marketing Director
            <br>
            Infineon Technologies
          </cite>
        </blockquote>
      </div>
    </div>
    </div>
<!-- end 1 -->

  </div>
</div>


<script src='js/slick.min.js'></script>
<script id="rendered-js" >

$('.findouthow,.p,.thmb').click(function(){

if($(this).attr("class")=="p" || $(this).attr("class")=="p active"){
 $('.p').removeClass('active');
 $(this).addClass('active');

 $('.'+$(this).attr("anchor")+' .thmb').removeClass('thmb-active');
 $('.'+$(this).attr("anchor")+' #thmb0').addClass('thmb-active');

$('.'+$(this).attr("anchor").replace('thmb','')).slick('slickGoTo', 0);

$('.sl').hide();
$('#'+$(this).attr("anchor")).show();

$('html, body').animate({
        scrollTop: $("."+$(this).attr("anchor")).offset().top
    }, 1000);
}

if($(this).attr("class")=="findouthow"){
$('html, body').animate({
        scrollTop: $("."+$(this).attr("anchor")).offset().top
    }, 1000);
}
nav=$(this).text();

if($(this).attr("class")!="p" && $(this).parent().find('.heading-title').text()!="" && $(this).parent().find('.heading-title').text()!="undefined" && $(this).parent().find('.heading-title').text()!=undefined)
nav=$(this).parent().find('.heading-title').text()+"->"+nav;


  $.ajax({
    type: 'GET',
    url: 'index.php?clicklog=yes&title='+nav,
    success: function (data) {
 }
});
});


var prevButton = '<button type="button" data-role="none" class="slick-prev" aria-label="prev"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" version="1.1"><path fill="#000" d="M 16,16.46 11.415,11.875 16,7.29 14.585,5.875 l -6,6 6,6 z" /></svg></button>',
nextButton = '<button type="button" data-role="none" class="slick-next" aria-label="next"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="#000" d="M8.585 16.46l4.585-4.585-4.585-4.585 1.415-1.415 6 6-6 6z"></path></svg></button>';

  var item_length = $('.single-item > p').length - 1;

  $('.single-item').slick({
  infinite: false,
  dotsClass:'slick-dots',
  dots: true,
  autoplay: true,
  autoplaySpeed: 4000,
  speed: 1000,
  pauseOnHover:false,
  pauseOnFocus:false,
   });

  $(document).ready(function(){
     $('.single-item').on('afterChange', function(event, slick, currentSlide) {
      //    $('.thmb').removeClass('thmb-active');
  // $('#thmb'+currentSlide).addClass('thmb-active');
     });
     $('.single-item').on('afterChange', function(event, slick, currentSlide) {

if (currentSlide == 3) {
   // $('.single-item').slickPause();
   // $('.single-item').slick('slickPause');
  }
})
})




  $('.single-item-component').slick({
  infinite: true,
  dotsClass:'slick-dots',
  dots: true,
  autoplay: true,
  autoplaySpeed: 4000,
  speed: 1000,
  pauseOnHover:false,
  pauseOnFocus:false,
  rtl: false
   });

  $(document).ready(function(){
     $('.single-item-component').on('afterChange', function(event, slick, currentSlide) {
          $('.single-item-componentthmb .thmb').removeClass('thmb-active');
   $('.single-item-componentthmb #thmb'+currentSlide).addClass('thmb-active');
     });
})

$('.single-item-componentthmb .thmb').click(function(){
$('.single-item-component').slick('slickPause');
$('.single-item-component').slick('slickGoTo', $(this).attr('slideid'));
});



  $('.single-item-hardware').slick({
  infinite: true,
  dotsClass:'slick-dots',
  dots: true,
  autoplay: true,
  autoplaySpeed: 4000,
  speed: 1000,
  pauseOnHover:false,
  pauseOnFocus:false,
   });

  $(document).ready(function(){
     $('.single-item-hardware').on('afterChange', function(event, slick, currentSlide) {
          $('.single-item-hardwarethmb .thmb').removeClass('thmb-active');
   $('.single-item-hardwarethmb #thmb'+currentSlide).addClass('thmb-active');
     });
})

$('.single-item-hardwarethmb .thmb').click(function(){
$('.single-item-hardware').slick('slickPause');
$('.single-item-hardware').slick('slickGoTo', $(this).attr('slideid'));
});


 $('.single-item-distributors').slick({
  infinite: true,
  dotsClass:'slick-dots',
  dots: true,
  autoplay: true,
  autoplaySpeed: 4000,
  speed: 1000,
  pauseOnHover:false,
  pauseOnFocus:false,
   });

  $(document).ready(function(){
     $('.single-item-distributors').on('afterChange', function(event, slick, currentSlide) {
          $('.single-item-distributorsthmb .thmb').removeClass('thmb-active');
   $('.single-item-distributorsthmb #thmb'+currentSlide).addClass('thmb-active');
     });
})

$('.single-item-distributorsthmb .thmb').click(function(){
$('.single-item-distributors').slick('slickPause');
$('.single-item-distributors').slick('slickGoTo', $(this).attr('slideid'));
});


 $('.request_demo').click(function(){

if($('#emailrequestdemo').val()==""){
$('.demomsg').html('Please provide email<br />');
return false;
}
 $(this).attr('disabled','disabled');
 $('.demomsg').html('<center>please wait..</center>');
  $.ajax({
    type: 'GET',
    url: 'product_database.php?requestdemo=yes&landfrom=homepage&email='+$('#emailrequestdemo').val()+'&first_name='+$('#requestdemofn').val()+'&c_name='+$('#requestdemocn').val()+'&job_function='+$('#requestdemojf').val(),
    success: function (data) {
    $('#emailrequestdemo,#requestdemofn,#requestdemocn,#requestdemojf').val('');
     $('.demomsg').html(data);
     $('.requestdemo').removeAttr('disabled');
 }
});
});



    </script>
 </div>

<!-- end -->
<style>
@media screen and (max-width:620px) {
.aboutus{width:100%;padding: 10px;}
.bannder .col-md-12{padding-bottom:30px;}
}
</style>
<div class="container product_details bannder" style="margin-bottom:20px;">

                <div class="col-md-12" style="background-color:#000;color:#FFF;">
            <div class="col-md-6 banner_left roboto aboutus" style="text-align:left;">
          <h2>About Us</h2>
         <br>
We are a team of software engineers, data scientists, device physicists, hardware engineers & business managers and are working on seamlessly combining the expertise in the areas of software and hardware, one product category at a time.

<br /><br />

At DiscoverEE, we believe in providing powerful, data-driven, product and market insights to hardware designers, engineers, marketers, sellers, and purchasers so they can design awesome products that make everyone’s life better.

<br /><br />

We enable this by creating detailed and objective "standardized product databases" and organize the unstructured hardware product landscape. Such a database offers unprecedented level of insights to makers and users of hardware products enabling them to focus on things that matter most to them.
<br /><br />
<b>Contact</b>:<br />
DiscoverEE Inc.<br />
100 Pine St., Suite 1250, San Francisco, CA 94111, USA<br />
Email: <a href="mailto:info@discoveree.io">info@discoveree.io</a><br />
      <br>
          </div>
            <div class="col-md-6 banner_left" style="text-align:left;">
          <h2>Get In Touch</h2>
         <br>
          <form role="form" id="contactform" class="contact-form" method="post" siq_id="autopick_8591">
    <div class="form-group">
    <div class="controls">
    <input type="text" class="form-control" placeholder="Name" name="name">
    </div>
    </div>
    <div class="form-group">
    <div class="controls">
    <input type="email" class="form-control email" placeholder="Email" name="email">
    </div>
    </div>
    <div class="form-group">
    <div class="controls">
    <input type="text" class="form-control requiredField" placeholder="Subject" name="subject">
    </div>
    </div>

    <div class="form-group">

    <div class="controls">
    <textarea rows="6" class="form-control" placeholder="Message" name="message"></textarea>
    </div>
    </div>
    <div class="form-group">

    <div class="controls">
    <button type="submit" id="submit" class="subscr-btn ellipses bold requestdemo" style="padding: 10px!important;background-color: #ff9300;color:#000;">Submit</button><div id="success" style="color:#FF0000;">
    </div>
    </div>






    <p></p>
    </div></form>
    </div>
 </div> </div>

 </section>

<?include("footer.php");?>
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

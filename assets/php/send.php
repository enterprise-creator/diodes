<?php 
$name = $_POST['name'];
$email = $_POST['email'];
$message = $_POST['message'];
$subject = $_POST['subject'];
$username = $_POST['username'];
$form_type = $_POST['form_type'];

$company = $_POST['company'];
$phone = $_POST['phone'];
$function = $_POST['function'];
if($function=="Others")
$function = $_POST['other_function'];

$contact_type = $_POST['contact_type'];

$to = 'info@discoveree.io';
if($username!="")
$to=$email="sales@discoveree.io";

$message = '<b>From</b>: '.$name.'<br /><b>Email</b>: '.$email.'<br /><b>Message</b>: '.$message;
if($form_type=="demo")
$message .= '<br /><b>Company</b>: '.$company.'<br /><b>Phone</b>: '.$phone.'<br /><b>Job Function</b>: '.$function.'<br />';

 $curl_handle=curl_init();
  curl_setopt($curl_handle,CURLOPT_URL,'http://api.ipstack.com/'.$_SERVER['REMOTE_ADDR'].'?access_key=2f8ea8267d188c5b28576707727de95a&format=1');
  curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
  curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
  $location = json_decode(curl_exec($curl_handle));
  curl_close($curl_handle);

  $message .= '<b>Location</b>: '.addslashes($location->city." ".$location->country_name)."<br />";
  $message .= '<b>IP Address</b>: '.$_SERVER['REMOTE_ADDR']."<br />";



$headers = 'From: noreply@discoveree.io' . "\r\n";

$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

if($name=="" && $contact_type!="fordownloadpowerloss"){
echo "<br />Please provide name.";
exit;
}
if($form_type=="demo" && $company==""){
echo "<br />Please provide your company name.";
exit;
}
if($form_type=="demo" && $email==""){
echo "<br />Please provide your company email.";
exit;
}
if($form_type=="demo" && $function==""){
echo "<br />Please provide your job function.";
exit;
}
if($message=="" && $contact_type==""){
echo "<br />Please provide your message.";
exit;
}

if($contact_type=="subscribe"){
$subject="DiscoverEE New Subscriber";
$message = 'FROM: '.$name.'\nEmail: '.$email.'\n';
}
if($contact_type=="fordownloadpowerloss"){
$subject="DiscoverEE Demo Request - power device intelligence and product updates.";
$message = '<b>Email</b>: '.$email.'<br />';
}

if (filter_var($email, FILTER_VALIDATE_EMAIL) || $username!="") { // this line checks that we have a valid email address
mail($to, $subject, $message, $headers); //This method sends the mail.
if($contact_type==""){
$subscribe_type=$contact_type="DiscoverEE Demo Request";
echo "<br />Thank You. We will get back to you as soon as possible."; // success message
}
if($contact_type=="subscribe" || $contact_type=="fordownloadpowerloss" || $contact_type=="DiscoverEE Demo Request"){
if($contact_type=="subscribe"){
$subscribe_type="subscribe";
echo "<br />Thanks for subscribing to our newsletter."; // success message
}
if($contact_type=="fordownloadpowerloss"){
$subscribe_type="power device intelligence and product updates";
echo "<br />Thanks for subscribing to receive email from DiscoverEE on power device intelligence and product updates.<br />"; // success message
}
include "../../includes/config.inc.php";

 $curl_handle=curl_init();
  curl_setopt($curl_handle,CURLOPT_URL,'http://api.ipstack.com/'.$_SERVER['REMOTE_ADDR'].'?access_key=2f8ea8267d188c5b28576707727de95a&format=1');
  curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
  curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
  $location = json_decode(curl_exec($curl_handle));
  curl_close($curl_handle);
  
  $sql="INSERT IGNORE INTO subscribers SET
email_id='".addslashes($_REQUEST['email'])."',subscribe_type='".$subscribe_type."',
subscribers_name='".addslashes($_REQUEST['name'])."',
location='".addslashes($location->city." ".$location->country_name)."',
job_function='".addslashes($function)."',
company='".addslashes($company)."',
ip_address='".$_SERVER['REMOTE_ADDR']."'
";
mysql_query($sql);
}
}else{
echo "<br />Invalid Email. Please provide correct email.";
}

?>

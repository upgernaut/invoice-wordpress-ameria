<?php 

session_start();
$content_post = get_post($_SESSION['invid']);

$invoice_information = array(
	'inv_title' => get_the_title($_SESSION['invid']),
	'inv_description' => $content_post->post_content,
	'inv_amount' => get_post_meta($_SESSION['invid'], "invoice_amount", $single = true),
	'inv_amount_amd' => get_post_meta($_SESSION['invid'], "invoice_amount_amd", $single = true)
);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>
Ameriabank vPOS example
</title>
<body>
<?php



//get orderID for request
// var_dump(get_option('invoice_plugin_last_insert_id')); 
if(get_option('invoice_plugin_last_insert_id'))
{
	$last_insert_id = get_option('invoice_plugin_last_insert_id') + 1;
}
else
{
	$last_insert_id = 1;
}
// var_dump($last_insert_id); die;
// $last_insert_id = 12346;

// $db1->request("INSERT INTO orders_history VALUES ('','".$_SESSION['all']."','".$_SESSION['validuserid']."','".$_SESSION['total_price']."','".date("Y-m-d")."','','0','','".$_POST['branches']."','".$_POST['date_time']."','".$_POST['persons']."','".$last_insert_id."','')");


try{

$options = array( 

            'soap_version'    => SOAP_1_1, 

            'exceptions'      => true, 

            'trace'           => 1, 

            'wdsl_local_copy' => true

            );

            //header('Content-Type: text/plain');

$client = new SoapClient("https://payments.ameriabank.am/webservice/PaymentService.svc?wsdl", $options);

 

 

// Set parameters

$parms['paymentfields']['ClientID'] = ''; // clientID from Ameriabank

// $parms['paymentfields']['Currency'] = ''; 

$parms['paymentfields']['Description'] = "Invoice for " . $invoice_information['inv_description'];

$parms['paymentfields'] ['OrderID']= $last_insert_id;// orderID wich must be unique for every transaction;

$parms['paymentfields'] ['Password']= ""; // password from Ameriabank

$parms['paymentfields'] ['PaymentAmount']= $invoice_information['inv_amount_amd']; // payment amount of your Order

$parms['paymentfields'] ['Username']= ""; // username from Ameriabank

$parms['paymentfields'] ['backURL']= get_bloginfo('url')."/invret"; // your backurl after transaction rediracted to this url

 

// Call web service PassMember methord and print response

$webService = $client-> GetPaymentID($parms);

echo($webService->GetPaymentIDResult->Respcode." ");

echo($webService->GetPaymentIDResult->Respmessage." ");

echo($webService->GetPaymentIDResult->PaymentID." ");


if($webService->GetPaymentIDResult->Respcode == '1' && $webService->GetPaymentIDResult->Respmessage =='OK')
{
	// var_dump($webService->GetPaymentIDResult->PaymentID); die;
	//rediract to Ameriabank server or you can use iFrame to show on your page
	echo "<script type='text/javascript'>\n";
	echo "window.location.replace('https://payments.ameriabank.am/forms/frm_paymentstype.aspx?clientid={$parms[paymentfields][ClientID]}&clienturl={$parms[paymentfields][backURL]}&lang=en&paymentid=".$webService->GetPaymentIDResult->PaymentID."');\n";
	echo "</script>";

}
else
{
	//Show your exception page
	echo "<script type='text/javascript'>\n";
	echo "window.location.replace(document.getElementsByTagName('base')[0].href+"."'".$langs_id."'"."+'/error.html');";
	echo "</script>";
}

       } catch (Exception $e) {

       echo 'Caught exception:',  $e->getMessage(), "\n";

} 
// End Send Receive Code //

//}
//}
?>
</body>
</head>
</html>

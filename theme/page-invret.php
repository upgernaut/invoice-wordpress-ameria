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
<title>Ameriabank vPOS example backURL</title>
<body>

<?
// Send Receive Code //



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

$parms['paymentfields']['Description'] ="Invoice for " . $invoice_information['inv_description'];

$parms['paymentfields'] ['OrderID']= $_POST['orderID'];// orderID wich must be unique for every transaction;

$parms['paymentfields'] ['Password']= ""; // password from Ameriabank

$parms['paymentfields'] ['PaymentAmount']= $invoice_information['inv_amount_amd']; // payment amount of your Order

$parms['paymentfields'] ['Username']= ""; // username from Ameriabank



 

// Call web service PassMember methord and print response

$webService = $client-> GetPaymentFields($parms);

echo($webService->GetPaymentFieldsResult->amount." ");

echo($webService->GetPaymentFieldsResult->respcode." ");

echo($webService->GetPaymentFieldsResult ->cardnumber." ");

echo($webService->GetPaymentFieldsResult ->paymenttype." ");
 	
echo($webService->GetPaymentFieldsResult ->authcode." ");



if($webService->GetPaymentFieldsResult->respcode == '00')
{
	if($webService->GetPaymentFieldsResult ->paymenttype == '1')
	 {
		$webService1 = $client-> Confirmation($parms);
		if($webService1->ConfirmationResult->Respcode == '00')
		 {
		 	// you can print your check or call Ameriabank check example
		   echo 	'<iframe id="idIframe" src="https://payments.ameriabank.am/forms/frm_checkprint.aspx?lang=am&paymentid='.$_POST['paymentid'].'" width="560px" height="820px" frameborder="0"></iframe>';
		 }
		 else
		 {
		 	// Rediract to Exception Page
			echo "<script type='text/javascript'>\n";
			echo "window.location.replace(document.getElementsByTagName('base')[0].href+"."'".$langs_id."'"."+'/error.html');";
			echo "</script>";
		 }
	 }
	 else
	 {
	 	// you can print your check or call Ameriabank check example
	   echo 	'<iframe id="idIframe" src="https://payments.ameriabank.am/forms/frm_checkprint.aspx?lang=en&paymentid='.$_POST['paymentid'].'" width="560px" height="820px" frameborder="0"></iframe>';
	 }
//$db1->request("
//			UPDATE orders_history	
//			SET    orders_history.payment_id = '".$webService->GetPaymentIDResult->PaymentID."'
//			WHERE  orders_history.order_id = '".$_POST['view_id']."'");	
update_option('invoice_plugin_last_insert_id',$_POST['orderID']);
update_post_meta($_SESSION['invid'], 'invoice_payed', '1');
update_post_meta($_SESSION['invid'], 'invoice_payment_id', $_POST['paymentid']);
}
else
{
	// Rediract to Exception Page
 	echo "<script type='text/javascript'>\n";
	echo "window.location.replace(document.getElementsByTagName('base')[0].href+"."'".$langs_id."'"."+'/error.html');";
	echo "</script>";

}
       } catch (Exception $e) {

       echo 'Caught exception:',  $e->getMessage(), "\n";

} 
// End Send Receive Code //

?>
</body>
</html>

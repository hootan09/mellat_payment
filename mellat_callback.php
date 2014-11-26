<?php
	/************************************************************************************
	 *                                                                             		*
	 * @version   mellat_payment.php version 0.1 in 2014/11/26                     		*
	 * @copyright Copyright (c) 2014.                                               	*
	 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License. 	*
	 * @author    nasser niazy  nasser@niazy.ir .                                  		*                            *
	 * @url    http://niazy.ir/Mellat_Payment .                                  		*                            *
	 ************************************************************************************
*/

	@$_SESSION["prv"] = $_SERVER['HTTP_REFERER'];

	if (isset($_POST['PayRequestButton'])) 
	{ 
		include ('mellat_payment.class.php');
		
		$mellat = new Mellat_Payment(
							$config['payment_mellat_terminal_id'], 
							$config['payment_mellat_user'], 
							$config['payment_mellat_pass'], 
							$config['payment_mellat_return_url']
							);
							
		$RefId = $_POST['RefId'];
		$ResCode = $_POST['ResCode'];
		$SaleOrderId = $_POST['SaleOrderId'];
		$SaleReferenceId = $_POST['SaleReferenceId'];
		
		$mellat->receiverParams($RefId, $ResCode, $SaleOrderId , $SaleReferenceId);
		$message =$mellat->getMsg('display');
	}
?>
<html>
<head>
</head>
<title>نتیجه پرداخت</title>
<meta http-equiv="Content-Language" content="fa">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<body>
<?php 
	if (isset ($message ))
		echo $message; 
?>

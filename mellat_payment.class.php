<?php

	/************************************************************************************
	 *                                                                             		*
	 * @version   mellat_payment.php version 0.1 in 2014/11/26                     		*
	 * @copyright Copyright (c) 2014.                                               	*
	 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License. 	*
	 * @author    nasser niazy  nasser@niazy.ir .                                  		*                            *
	 * @url    http://niazy.ir/Mellat_Payment .                                  		*                            *
	 ************************************************************************************

CREATE TABLE IF NOT EXISTS 
`tbl_internettransaction` (
	  `Internet_Transition_Id` bigint(20) NOT NULL AUTO_INCREMENT,
	  `res_num` varchar(20) NOT NULL,
	  `ref_num` varchar(50) DEFAULT NULL,
	  `total_amount` int(11) NOT NULL,
	  `payment` int(11) DEFAULT NULL,
	  `date_start` varchar(50) NOT NULL,
	  `LastUrl` longtext NOT NULL,
	  `IP_Address` varchar(20) NOT NULL,
	  `time_start` varchar(50) NOT NULL,
	  `email` varchar(50) NOT NULL,
	  `Fname` varchar(50) DEFAULT NULL,
	  `Phone` varchar(20) DEFAULT NULL,
	  `comments` longtext,
	  PRIMARY KEY (`Internet_Transition_Id`)
	  unique(res_num),
	  index(ref_num)
) ;

*/

class Mellat_Payment 
{
	public $action = 'https://pgw.bpm.bankmellat.ir/pgwchannel/startpay.mellat';

	public $webMethodURL = 'https://pgws.bpm.bankmellat.ir/pgwchannel/services/pgw?wsdl';
	
	public $WSDLnamespace='http://interfaces.core.sw.bps.com/';
	
	private $db_host = 'localhost';
	
	private $db_user = 'root';
	
	private $db_pass = '';
	
	private $db_name = 'mydb' ;
	
	public $callBackUrl = 'http://mysite.ir/mellat_callback.php';

	public $totalAmont = 0;

	public $refNum = '';
	public $IpAddress ='';
	public $lastUrl ='';

	public $resNum='';
	public $payerId =0;
	public $Fname='';
	public $phone='';
	public $email= '';
	public $comments='';
	public $conn;
	
	protected $userName = '';
	protected $terminalID='';
	protected $password='';
	
	protected $msg = array ();
	
	protected $errorReturn = array (
	
		'0'		=>	'تراكنش با موفقيت انجام شد 					   '    ,
		'1'		=>	'شماره كارت نامعتبر است 						   '    ,
		'12'	=>	'موجودي كافي نيست 							   '    ,
		'13'	=>	'رمز نادرست است 								'   ,
		'14'	=>	'تعداد دفعات وارد كردن رمز بيش از حد مجاز است   '    ,
		'15'	=>	'كارت نامعتبر است 							   '    ,
		'17'	=>	'كاربر از انجام تراكنش منصرف شده است 			'   ,
		'18'	=>	'تاريخ انقضاي كارت گذشته است 				   '    ,
		'111'	=>	'صادر كننده كارت نامعتبر است 					'   ,
		'112'	=>	'خطاي سوييچ صادر كننده كارت 					'   ,
		'113'	=>	'پاسخي از صادر كننده كارت دريافت نشد 		   '    ,
		'114'	=>	'دارنده كارت مجاز به انجام اين تراكنش نيست 	   '    ,
		'21'	=>	'پذيرنده نامعتبر است 							'   ,
		'22'	=>	'ترمينال مجوز ارايه سرويس درخواستي را ندارد. 	   '    ,
		'23'	=>	'خطاي امنيتي رخ داده است 						'   ,
		'24'	=>	'اطلاعات كاربري پذيرنده نامعتبر است 			   '    ,
		'25'	=>	'مبلغ نامعتبر است 							   '    ,
		'31'	=>	'پاسخ نامعتبر است 							   '    ,
		'32'	=>	'فرمت اطلاعات وارد شده صحيح نمي باشد 			   '    ,
		'33'	=>	'حساب نامعتبر است 							   '    ,
		'34'	=>	'خطاي سيستمي 								   '    ,
		'35'	=>	'تاريخ نامعتبر است 							   '    ,
		'41'	=>	'شماره درخواست تكراري است 					   '    ,
		'42'	=>	'تراكنش Sale يافت نشد 						   '    ,
		'43'	=>	'قبلا درخواست Verify داده شده است 			   '    ,
		'44'	=>	'درخواست Verfiy يافت نشد 					   '    ,
		'45'	=>	'تراكنش Settle شده است 						   '    ,
		'46'	=>	'تراكنش Settle نشده است 						'   ,
		'47'	=>	'تراكنش Settle يافت نشد 						'   ,
		'48'	=>	'تراكنش Reverse شده است 						'   ,
		'49'	=>	'تراكنش Refund يافت نشد 						'   ,
		'412'	=>	'شناسه قبض نادرست است 						   '    ,
		'413'	=>	'شناسه پرداخت نادرست است 					   '    ,
		'414'	=>	'سازمان صادر كننده قبض نامعتبر است 			   '    ,
		'415'	=>	'زمان جلسه كاري به پايان رسيده است 			   '    ,
		'416'	=>	'خطا در ثبت اطلاعات 							   '    ,
		'417'	=>	'شناسه پرداخت كننده نامعتبر است 				   '    ,
		'418'	=>	'اشكال در تعريف اطلاعات مشتري 					'   ,
		'419'	=>	'تعداد دفعات ورود اطلاعات از حد مجاز گذشته است   '    ,
		'40'	=>	'است نامعتبرIP 								   '    ,
		'51'	=>	'تراكنش تكراري است					 		   '    ,
		'52'	=>	'سرويس درخواستي موجود نمي باشد 				   '    ,
		'54'	=>	'تراكنش مرجع موجود نيست 						'   ,
		'55'	=>	'تراكنش نامعتبر است 							   '    ,
		'61'	=>	'خطا در واريز 								   '    
);                                                             

	function __construct($terminal = '', $user = '', $pass = '',$url='')
	{
		date_default_timezone_set('Asia/Tehran');
		$this->terminalID = $terminal;
		$this->userName = $user;
		$this->password = $pass;
		$this->db = $db;
		$this->callBackUrl = $url;
		$this->conn = mysqli_connect($this->db_host, $this->db_user, $this->db_pass, $this->db_name);
		mysqli_query($this->conn, "SET NAMES utf8");
	}
	
	/*
		payRequest request pay info[Ref Number] from bank 
	*/
	function payRequest()
	{
		if (
			$this->totalAmont <= 0 or 
			empty($this->action) or 
			empty($this->callBackUrl) or 
			empty($this->resNum) or
			empty($this->terminalID)
			) 
		{
			$this->setMsg("خطا در مبالغ ورودی");

			return false;
		}
		
		$query="select `Internet_Transition_Id` from `tbl_internettransaction` where `res_num` ='$this->resNum'";
		$res = mysqli_query($this->conn, $query) ;
		if( mysqli_error($this->conn))
		{
			$this->setMsg(mysqli_error($this->conn));
			return false;
		}
		$Return = mysqli_fetch_assoc($res);		
	
		if(count($Return) == 0 || !(isset($Return['Internet_Transition_Id'])))
		{
			$this->setMsg('تراکنش پیدا نشد');
			return false;
		}
		$Transition_Id=$Return['Internet_Transition_Id'];
		//$orderId =rand();

		$localDate = date("Ymd");
		$localTime = date("His");
		$additionalData = '';
		
		
		include '../nusoap/nusoap.php';
		$soapClient2 = new nusoap_client($this->webMethodURL, 'wsdl');
		// Check for an error
		$err = $soapClient2->getError();
		if ($err) 
		{
			$this->setMsg($err);
			return false;
		}
		
		$parameters = array(
			'terminalId' => $this->terminalID,
			'userName' => $this->userName,
			'userPassword' => $this->password,
			'orderId' => $Transition_Id,
			'amount' => $this->totalAmont,
			'localDate' => $localDate,
			'localTime' => $localTime,
			'additionalData' => $additionalData,
			'callBackUrl' => $this->callBackUrl,
			'payerId' => $this->payerId);
		
		
		// Call the SOAP method
		
		$result = $soapClient2->call('bpPayRequest', $parameters, $this->WSDLnamespace);


			
		// Check for a fault
		if ($soapClient2->fault) 
		{
			$message = '<font color="red">در اتصال به درگاه بانک ملت مشکلی به وجود آمد٬ لطفا از درگاه سایر بانک‌ها استفاده نمایید.</font> خطا: <br />خطا در اتصال به بانک ملت<br />';
			$this->setMsg($message);

			$message='';
			foreach ($result as $error)
				$message .= $error;
				
			$this->setMsg($message );
			
			return false;
			
		} 
		else 
		{
			// Check for errors
			
			$resultStr  = $result;

			$err = $soapClient2->getError();
			if ($err) 
			{
				// Display the error
				$message ='<font color="red">در اتصال به درگاه بانک ملت مشکلی به وجود آمد٬ لطفا از درگاه سایر بانک‌ها استفاده نمایید.</font> خطا: <br /><pre>'.$err.'</pre><br />';
				$this->setMsg($message);
				return false;
			} 
			else 
			{
				// Display the result

				$resultStr = explode (',',$resultStr['return'].',');
				
				$ResCode = $resultStr[0];
				
				if ($ResCode == "0") 
				{
					// Update table, Save RefId
					$this->RefId=$resultStr[1];
					$query="update tbl_internettransaction set ref_num='$this->RefId' where id =".$Transition_Id;
					$res = mysqli_query($this->conn, $query) ;
					if( mysqli_error($this->conn))
					{
						$this->setMsg(mysqli_error($this->conn));
						return false;
					}
					return true;	
				} 
				else 
				{
					// log error in app
					// Update table, log the error
					// Show proper message to user
					$this->setMsg($ResCode);
					return false ;
				}
			}// end Display the result
		}// end Check for errors

	}
	
	/*
		saveStoreInfo save payer info into database
	integer @pay 		pay amount
	string 	@name 		payer name 
	string 	@phone 		payer phone 
	string 	@comment 	payer comment 
	string 	@email 		payer mail address 
	*/
	function saveStoreInfo($pay, $name, $phone, $comment, $email)
	{ 
		
		$date = date('Ymd');
		$time = date('His');

		$this->createResNum();
		$this->IpAddress = $_SERVER['REMOTE_ADDR'];
		$this->lastUrl =$_SERVER['HTTP_REFERER'];
		$this->payerId = 0;
		$this->totalAmont=$pay;
		
		$this->Fname =isset($Fname)? $Fname :'';
		$this->phone =isset($Phone)? $Phone:'';
		$this->comments =isset($Comments)?$Comments:'';
		$this->email =isset($Mail)? $Mail:'';
	
		$this->createResNum();

		$query = "insert into 
						tbl_internettransaction(
												res_num,
												total_amount,
												date_start,
												LastUrl,
												IP_Address,
												Fname,
												phone,
												comments,
												email,
												time_start
												) 
						VALUES
						( 
							'$this->resNum',
							'$this->totalAmont',
							'$date',
							'$this->lastUrl' ,
							'$this->IpAddress',
							'$this->Fname',
							'$this->phone',
							'$this->comments',
							'$this->email',
							'$time'
							)";
		$value["res_num"] = $this->resNum;
		$value["total_amount"] = $this->totalAmont;
		$value["date_start"] = $date;
		$value["LastUrl"] = $this->lastUrl;
		$value["IP_Address"] = $this->IpAddress;

		//message,Logs   اتصال به سرور بانک
		try 
		{

			$res = mysqli_query($this->conn, $query) or $this->setMsg(mysqli_error($this->conn));
			//log insert data in table
			return $res;
		}
		catch (Exception $e) 
		{
			//log do not insert data in table
			return false ;
		}	

	}
	
	/*
		post pay request info for bank 
	*/
	function sendParams()
	{
		echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Language" content="fa">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<script language="javascript" type="text/javascript">    
		function postRefId (refIdValue,url) {
			var form = document.createElement("form");
			form.setAttribute("method", "POST");
			form.setAttribute("action", url);         
			form.setAttribute("target", "_self");
			var hiddenField = document.createElement("input");              
			hiddenField.setAttribute("name", "RefId");
			hiddenField.setAttribute("value", refIdValue);
			form.appendChild(hiddenField);

			document.body.appendChild(form);         
			form.submit();
			document.body.removeChild(form);
		}		
	</script>
</head><body>
'.		"<script language='javascript' type='text/javascript'>postRefId('" .
		$this->refNum ."','".
		$this->action ."');</script>
		</body></html>";		

	}
	
	/*
		save pay info revived from bank 
	integer @RefId 				pay id generated via bank
	string 	@ResCode 			pay resCode generated via $this->createResNum function
	integer @SaleOrderId 		sale order id  
	integer @SaleReferenceId 	sale referenced id  
	*/
	function receiverParams($RefId = '', $ResCode = '', $SaleOrderId = '',$SaleReferenceId ='')
	{
		$this->refNum=$RefId;
		$orderId = $SaleOrderId;
		$verifySaleOrderId = $SaleOrderId;
		$verifySaleReferenceId = $SaleReferenceId;
		
		include '../nusoap/nusoap.php';
		$client = new nusoap_client($this->webMethodURL, 'wsdl');
		
		// Check for an error
		$err = $client->getError();
		if ($err) 
		{
			$this->setMsg( $err );
			return false;
		}
	  	else
		{  
			$parameters = array(
					'terminalId' => $this->terminalId,
					'userName' => $this->userName,
					'userPassword' => $this->userPassword,
					'orderId' => $orderId,
					'saleOrderId' => $verifySaleOrderId,
					'saleReferenceId' => $verifySaleReferenceId
				);

			// Call the SOAP method
			$result = $client->call('bpVerifyRequest', $parameters,  $this->WSDLnamespace);

			// Check for a fault
			if ($client->fault) 
			{
				$msg= '<h2>Fault</h2><pre>';
				foreach ($result as $error)
								$msg .= $error;
				$msg.= '</pre>';
				$this->setMsg($msg);
			} 
			else 
			{

				$resultStr = $result;
				
				$err = $client->getError();
				if ($err) 
				{
					///////////////// INQUIRY REQUEST
					$inquirySaleOrderId = $SaleOrderId;
					$inquirySaleReferenceId = $SaleReferenceId;

					// Check for an error
					$err = $client->getError();
					if ($err) 
					{
						$this->setMsg($err );
						return false;
					}
					  
					$parameters = array(
						'terminalId' => $this->terminalId,
						'userName' => $this->userName,
						'userPassword' => $this->userPassword,
						'orderId' => $orderId,
						'saleOrderId' => $inquirySaleOrderId,
						'saleReferenceId' => $inquirySaleReferenceId);

					// Call the SOAP method
					$result = $client->call('bpInquiryRequest', $parameters, $namespace);

					// Check for a fault
					if ($client->fault) 
					{
						
						$message='';
						foreach ($result as $error)
							$message .= $error;
						$this->setMsg($message );
						return false;
						
					} 
					else 
					{
						$resultStr = $result;
						
						$err = $client->getError();
						if ($err) 
						{
							///////////////// REVERSAL REQUEST

							$reversalSaleOrderId = $SaleOrderId;
							$reversalSaleReferenceId = $SaleReferenceId;

							// Check for an error
							$err = $client->getError();
							if ($err) 
							{
								$this->setMsg($err );
								return false;
							}
							  
							$parameters = array(
								'terminalId' => $this->terminalId,
								'userName' => $this->userName,
								'userPassword' => $this->userPassword,
								'orderId' => $orderId,
								'saleOrderId' => $reversalSaleOrderId,
								'saleReferenceId' => $reversalSaleReferenceId);

							// Call the SOAP method
							$result = $client->call('bpReversalRequest', $parameters, $this->namespace);

							// Check for a fault
							if ($client->fault) 
							{
								$message='';
								foreach ($result as $error)
									$message .= $error;
								$this->setMsg($message );
								return false;
							} 
							else 
							{
								$resultStr = $result;
								
								$err = $client->getError();
								if ($err) 
								{
									// Display the error
									$this->setMsg($err );
									return false;
								} 
								else 
								{
									// Update Table, Save Reversal Status 
									// Note: Successful Reversal means that sale is reversed.
									
									if($result == 0)
									{
										$this->setMsg( '<br />در موقع پرداخت مشکلی به وجود آمد٬ مبلغ پرداخت شده به حساب شما برگشت داده شد.'.$resultStr);
									}
									else
									{
										$this->setMsg('<br />در موقع پرداخت مشکلی به وجود آمد.'.$resultStr);
									}

									return true;
								}// end Display the result
							}// end Check for errors
			
				
							// Display the error
							$this->setMsg($err );
							return false;
						} 
						else 
						{
							// Update Table, Save Inquiry Status 
							// Note: Successful Inquiry means complete successful sale was done.
							//echo "<script>alert('Inquiry Response is : " . $resultStr . "');</script>";
							///////////////// SETTLE REQUEST
					
							$settleSaleOrderId = $SaleOrderId;
							$settleSaleReferenceId = $SaleReferenceId;


							// Check for an error
							$err = $client->getError();
							if ($err) 
							{
								$this->setMsg($err );
								return false;
							}
							  
							$parameters = array(
								'terminalId' => $this->terminalId,
								'userName' => $this->userName,
								'userPassword' => $this->userPassword,
								'orderId' => $orderId,
								'saleOrderId' => $settleSaleOrderId,
								'saleReferenceId' => $settleSaleReferenceId);

							// Call the SOAP method
							$result = $client->call('bpSettleRequest', $parameters, $namespace);

							// Check for a fault
							if ($client->fault) 
							{
								$message='';
								foreach ($result as $error)
									$message .= $error;
								$this->setMsg($message );
								return false;
							} 
							else 
							{
								$resultStr = $result;
								
								$err = $client->getError();
								if ($err) 
								{
									// Display the error
									$this->setMsg($err );
									return false;
								} 
								else 
								{
									// Update Table, Save Settle Status 
									// Note: Successful Settle means that sale is settled.
									$this->saveBankInfo();
									
									//echo "Settle Response is : " . $resultStr;
								}// end Display the result
							}// end Check for errors									

							//echo "Inquiry Response is : " . $resultStr;
						}// end Display the result
					}// end Check for errors				
			
					// Display the error				
					$this->setMsg($err );
					return false;
				} 
				else 
				{
					// Display the result
					// Update Table, Save Verify Status 
					// Note: Successful Verify means complete successful sale was done.
					
					//echo "Verify Response is : " . $resultStr;
					
					///////////////// SETTLE REQUEST

					$settleSaleOrderId = $SaleOrderId;
					$settleSaleReferenceId = $SaleReferenceId;

					// Check for an error
					$err = $client->getError();
					if ($err) 
					{
						$this->setMsg($err );
						return false;
					}
					  
					$parameters = array(
						'terminalId' => $this->terminalId,
						'userName' => $this->userName,
						'userPassword' => $this->userPassword,
						'orderId' => $orderId,
						'saleOrderId' => $settleSaleOrderId,
						'saleReferenceId' => $settleSaleReferenceId);

					// Call the SOAP method
					$result = $client->call('bpSettleRequest', $parameters, $namespace);

					// Check for a fault
					if ($client->fault)
					{
						$message='';
						foreach ($result as $error)
							$message .= $error;
						$this->setMsg($message );
						return false;
					} 
					else 
					{
						$resultStr = $result;
						
						$err = $client->getError();
						if ($err) {
							// Display the error
							$this->setMsg($err );
							return false;
						} 
						else 
						{
							// Update Table, Save Settle Status 
							// Note: Successful Settle means that sale is settled.
							$this->saveBankInfo();
							

						}// end Display the result
					}// end Check for errors	

				}// end Display the result
			}// end Check for errors
			
		}
	}
	
	/*
		update ref Number come form bank 
	*/
	protected function saveBankInfo()
	{
		$this->setMsg( 'تراكنش با موفقیت انجام شد');
		return mysqli_query($this->conn, 
			"UPDATE tbl_internettransaction SET ref_num = '$this->refNum'  WHERE res_num = '$this->resNum'") 
				or $this->setMsg(mysqli_error($this->conn));
		
	}
	
	/*
		generate output message
	*/
	public function getMsg($dis = '')
	{
		if (count($this->msg) == 0) 
			$this->msg[]='';
		if ($dis == 'display') 
		{
			$msg = "<ul>\n";
			foreach ($this->msg as $v)
			{
				$msg .= "<li> $v </li>\n";
			}
			$msg .= "</ul>\n";

			return message($msg, 3);
		}
		return $this->msg;
	}
	
	/*
		save all messages
	*/
	protected function setMsg($type = '', $index = '')
	{

		if (strval($type)>0)
		{
			$this->msg[] = $this->errorReturn[$type];
		}
		elseif ($type != 'verify' and $type != 'state') 
		{
			$this->msg[] = $type;
		}
	}

	/*
		generate unique code for use in transaction.
	*/
	protected function createResNum()
	{
		do 
		{
			$m = md5(microtime());
			$resNum = substr($m, 0, 20);
			$result = mysqli_query($this->conn, "SELECT res_num FROM tbl_internettransaction WHERE res_num = '$resNum'");

			if (mysqli_num_rows($result) < 1)
			{
				break;
			}
		}
		while (true);
		$this->resNum = $resNum;
	}

}
?>

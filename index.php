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

	
	if (isset($_POST['submit'])) 
	{

		$name = $_POST["name"];
		
		$phone = $_POST["mobile"];
		$email = $_POST["email"];
		
		$pay =$_POST['totalAmont'] ;

		$comment =  $_POST["comments"];
		
		include('mellat_payment.class.php');	
			
		$mellat = new Mellat_Payment(
							$loader->config['payment_mellat_terminal_id'], 
							$loader->config['payment_mellat_user'], 
							$loader->config['payment_mellat_pass'], 
							$loader->config['payment_mellat_return_url'], 
							$loader->db_accounting
							);

		if($mellat->saveStoreInfo(	
									$pay,
									$name, 
									$phone, 
									$comment, 
									$email
									))
		{
			if ($mellat->payRequest()) 
			{
				$mellat->sendParams();
				
			}
		}
		$mellat->getMsg('display');
			
		}
		
		exit();
	}

		?>
<html>
<head>
</head>
<title>پرداخت آنلاین</title>
<meta http-equiv="Content-Language" content="fa">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<body>
		<style>

			.Invisible {

				display: none;

				font-family: yekan, b yekan, tahoma, Arial, Verdana;

				font-size: 14px;

				color: red;

			}

			.Show {

				font-family: yekan, b yekan, tahoma, Arial, Verdana;

				font-size: 14px;

				color: red;

				font-weight: bold;

			}

			.auto-style1 {

				width: 30%;

			}

			input {

				border: 1px solid;

				border-radius: 5px;

				-moz-border-radius: 5px;

				-webkit-border-radius: 5px;

				/*width:50%;*/

				text-align: center;

			}

			input[type="submit"] {

				border-color: #999;

				cursor: pointer;

				background: -webkit-linear-gradient(top, white, #E0E0E0);

				background: -moz-linear-gradient(top, white, #E0E0E0);

				background: -ms-linear-gradient(top, white, #E0E0E0);

				background: -o-linear-gradient(top, white, #E0E0E0);

				-webkit-box-shadow: 0 1px 2px rgba(0, 0, 0, 0.25), inset 0 0 3px #fff;

				-moz-box-shadow: 0 1px 2px rgba(0, 0, 0, 0.25), inset 0 0 3px #fff;

				box-shadow: 0 1px 2px rgba(0, 0, 0, 0.25), inset 0 0 3px #fff;

				margin-top: 5px;

				text-align: center;

			}

			textarea {

				border: 1px solid;

				border-radius: 5px;

				-moz-border-radius: 5px;

				-webkit-border-radius: 5px;

				width: 95%;

				height: 150px;

			}

			input:focus {

				border: 1px solid;

				border-color: #18f7f7;

				-moz-transition: .3s ease-in-out all;

			}

			textarea:focus {

				border: 1px solid;

				border-color: #18f7f7;

				-moz-transition: .3s ease-in-out all;

			}


		</style>

			</br>
		</br>

		<form id = "Form" method = "post" action = "index.php">
			<div dir = "rtl">
				<div id = "Warning" class = "Invisible">لطفا مبلغ پرداختی را وارد نمایید</div>
				<table class = "auto-style1" cellpadding = "10">
					<tr>
						<td width = "320">مبلغ واریزی به حساب(ریال)</td>
						<td><input type="text" width = "30px" id = "Txt_Mablagh" name = "totalAmont"
						           title = "مبلغ واریزی خود به ریال را وارد کنید."/></td>
					</tr>
					<tr>
						<td>نام</td>
						<td><input type="text" width = "30px" id = "name" name = "name"/></td>
					</tr>
					<tr>
						<td>تلفن تماس</td>
						<td><input type="text" width = "30px" id = "mobile" name = "mobile"/></td>
					</tr>
					<tr>
						<td>ایمیل</td>
						<td><input type="text" width = "30px" id = "email" name = "email"/></td>
					</tr>
					<tr>
						<td>شرح</td>
						<td><textarea  name = "comments"/></textarea></td>
					</tr>

					<tr>
						<td colspan = "2"><input width = "50%" id = "Submit" type = "Submit" name = "submit"
						                         value = " پرداخت "/></td>
					</tr>
				</table>
			</div>
		</form>
</body>
</html>

<?php

/*
  MangoForm Vanilla Fetch v1.0.0
  Website (https://github.com/c0fe/MangoForm-Vanilla-JS)
  Licensed under MIT (https://github.com/c0fe/MangoForm-Vanilla-JS/blob/master/LICENSE)
*/

//Load Required Components
require_once 'src/recaptcha_autoload.php';
require_once "functions.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

function validate($formData)
{

	// Initiate Array (will hold validation errors)

	$validationMSG = array();

	$pname_exp = '/^[a-zA-Z0-9\_]{2,20}/';

	if (!isset($formData['firstName'])) {
		$validationMSG['firstName'] = 'First Name is required.';
	}elseif (!preg_match($pname_exp, $formData['firstName'])){
		 $validationMSG['firstName'] = 'First Name is not valid.';
	}

	// Validate lastName
	if (!isset($formData['lastName'])) {
		$validationMSG['lastName'] = 'Last Name is required.';
	}

	// Check RegEx for Last Name
	elseif (!preg_match($pname_exp, $formData['lastName'])) {
		$validationMSG['lastName'] = 'Last Name is not valid.';
	}

	// Validate companyName
	if (!isset($formData['companyName'])) {
		$validationMSG['companyName'] = 'Company Name is required.';
	}

	// Validate companyAddress
	if (!isset($formData['companyAddress'])) {
		$validationMSG['companyAddress'] = 'Company Address is required.';
	}

	// Validate state
	if (!isset($formData['state'])) {
		$validationMSG['state'] = 'State is required.';
	}

	// Validate city
	if (!isset($formData['city'])) {
		$validationMSG['city'] = 'City is required.';
	}

	// Validate Zipcode - If Field is Empty
	if (!isset($formData['zipcode'])) {
		$validationMSG['zipcode'] = 'Zipcode is required.';
	}

	// Validate emailAddress
	if (!isset($formData['emailAddress'])) {
		$validationMSG['emailAddress'] = 'Email Address is required.';
	}

	// Check if emailAddress is a valid email address
	elseif (!filter_var($formData['emailAddress'], FILTER_VALIDATE_EMAIL)) {
		$validationMSG['emailAddress'] = 'Email address is not valid.';
	}

	//Validate phoneNumber
	if (!isset($formData['phoneNumber'])) {
		$validationMSG['phoneNumber'] = 'Phone Number is required.';
	}

	//Validate phoneNumber
	elseif (preg_match("/^[0-9]{3}-[0-9]{4}-[0-9]{4}$/", $formData['phoneNumber'])) {
		$validationMSG['phoneNumber'] = 'Must be a valid phone number.';
	}

	// Validate message
	if (!isset($formData['message'])) {
		$validationMSG['message'] = 'Message is required.';
	}

	if (!empty($validationMSG)) {
		return $validationMSG;		  
	}	
	else {
		$captcha = checkCaptcha($formData['g-recaptcha-response']);
		if(!$captcha['isSuccess']){
		$validationMSG['captcha'] = 'ReCaptcha is required.';
		
	    return $validationMSG;
		}

		//End of Validation Function
}
}

function checkCaptcha($g_recaptcha_response)
{
	$recaptcha_secret_key = 'SECRET_KEY_HERE';
//	$recaptcha = new ReCaptchaReCaptcha($recaptcha_secret_key, new ReCaptchaRequestMethodCurlPost());
	$recaptcha = new \ReCaptcha\ReCaptcha($recaptcha_secret_key);
	$resp = $recaptcha->verify($g_recaptcha_response, $_SERVER['REMOTE_ADDR']);
	return [
	    'isSuccess' =>  $resp->isSuccess(),
	    'errorCodes' => $resp->getErrorCodes(),
	    ];
}

function sendMail($formData)
{
	$mail = new PHPMailer(true); // Passing `true` enables exceptions
	// Server settings

	//$mail->SMTPDebug = 2; // Enable verbose debug output
	$mail->isSMTP(); // Set mailer to use SMTP
	$mail->Host = 'smtp.server.com'; // Specify main and backup SMTP servers
	$mail->SMTPAuth = true; // Enable SMTP authentication
	$mail->Username = 'user@server.com'; // SMTP username
	$mail->Password = 'PASSWORD_HERE'; // SMTP password
	$mail->SMTPSecure = 'ssl'; // Enable TLS encryption, `ssl` also accepted
	$mail->Port = 465; // TCP port to connect to

	// Recipients

	$mail->setFrom('user@server.com', 'Mailer');
	$mail->addAddress('user@server.com', 'Joe User'); // Add a recipient

	// Content

	$mail->isHTML(true); // Set email format to HTML
	$mail->Subject = 'New Message from Contact Form';

	// prepare email body

	$body_message = "";
	$body_message.= "Sender IP: " . get_client_ip() . "<br />";

	// @todo: make the other rows the same way, i.e. $formData['key'];

	$body_message.= "First Name: " . $formData['firstName'] . "<br />";
	$body_message.= "Last Name: " . $formData['lastName'] . "<br />";
	$body_message.= "Company Name: " . $formData['companyName'] . "<br />";
	$body_message.= "Company Address: " . $formData['companyAddress'] . "<br />";
	$body_message.= "City: " . $formData['city'] . "<br />";
	$body_message.= "State: " . $formData['state'] . "<br />";
	$body_message.= "Sender email: " . $formData['emailAddress'] . "<br />";
	$body_message.= "Sender Phone: " . $formData['phoneNumber'] . "<br />";
	$body_message.= "\n\n" . $formData['message'];
	$mail->Body = $body_message;
	$mail->send();
}

$response = [
    'success' => false,
    'errors' => [],
  //  'message' => 'Error sending message'
];

$formData = json_decode(file_get_contents("php://input"), true);

$errors = validate($formData);
if(!empty($errors)){
   $response['success']  = false;
   $response['errors']  = $errors;

}else {
    try{
        sendMail($formData);
        //Print Success Message
        $response['success'] = true;
        $response['message'] = 'Message was Sent!';
    }
	catch(Exception $e) {
		// Print phpMailer Error Message
		$response['success']  = false;
		$response['message'] = 'There has been an issue sending your message';

	}

}

echo json_encode($response);

exit;

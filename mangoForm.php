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
    // Initiate Array
    $validationMSG = array(); // array to hold validation errors

    // what to validate (basics, i.e. required fields)
    // key name => display name
    $fields = [
            'firstName' => [
                'label' => 'First Name',
                'rules' => 'required'
            ],
            'lastName' => [
                'label' => 'Last Name',
                'rules' => 'required'
            ],
            'companyName' => [
                'label' => 'Company Name',
                'rules' => 'required'
            ],
            'companyAddress' => [
                'label' => 'Company Address',
                'rules' => 'required'
            ],
            'city' => [
                'label' => 'City',
                'rules' => 'required'
            ],
            'state' => [
                'label' => 'State',
                'rules' => 'required'
            ],
            'zipcode' => [
                'label' => 'Zipcode',
                'rules' => 'required'
            ],
            'emailAddress' => [
                'label' => 'Email',
                'rules' => 'required|email'
            ],
            'phoneNumber' => [
                'label' => 'Phone Number',
                'rules' => 'required|phone'
            ],
        ];

    //simple loop
    foreach($fields as $fieldName => $args) {
        $rules = explode('|', $args['rules']);
        foreach($rules as $rule)
        {
            if($rule == 'required' && (!isset($formData[$fieldName]) || empty($formData[$fieldName])))
            {
                $validationMSG[$fieldName][] = sprintf('%s is a required field.', $args['label']);
            }

            if((isset($formData[$fieldName]) && $rule == 'email') && !empty($formData[$fieldName]) && !filter_var($formData[$fieldName], FILTER_VALIDATE_EMAIL))
            {
                $validationMSG[$fieldName][] = sprintf('%s must be a valid email.', $args['label']);
            }

            if((isset($formData[$fieldName]) && $rule == 'phone') && !empty($formData[$fieldName]) && !filter_var($formData[$fieldName], preg_match("/^[0-9]{3}-[0-9]{4}-[0-9]{4}$/", $formData[$fieldName])))
            {
                $validationMSG[$fieldName][] = sprintf('%s must be a phone number.', $args['label']);
            }
        }
    }
    //return messages
    return $validationMSG;

    if (empty($validationMSG)) {
        $captcha = checkCaptcha($formData['g-recaptcha-response']);
		if(!$captcha['isSuccess']){
		$validationMSG['captcha'] = 'ReCaptcha is required.';
		//return error messages
	    return $validationMSG;
    }
    }

//end of validate function
}

function checkCaptcha($g_recaptcha_response)
{
	$recaptcha_secret_key = 'SECRET_KEY_HERE';
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
	$mail->Password = 'SECRET_PASSWORD_HERE'; // SMTP password
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

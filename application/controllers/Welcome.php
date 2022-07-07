<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Welcome extends CI_Controller {

	public function __construct() { 
                parent::__construct(); 
                
                require APPPATH.'libraries/phpmailer/src/Exception.php';
                require APPPATH.'libraries/phpmailer/src/PHPMailer.php';
                require APPPATH.'libraries/phpmailer/src/SMTP.php';
                 
                    }
                    function index() 
                    {

                        // PHPMailer object
                     $response = false;
                     $mail = new PHPMailer();
                   
            
                    // SMTP configuration
                    $mail->isSMTP();
                    $mail->Host     = 'mail@roudhatulquran-manggungjaya.sch.id'; //sesuaikan sesuai nama domain hosting/server yang digunakan
                    $mail->SMTPAuth = true;
                    $mail->Username = 'admin@roudhatulquran-manggungjaya.sch.id'; // user email
                    $mail->Password = 'admin123Aa@'; // password email
                    $mail->SMTPSecure = 'ssl';
                    $mail->Port     = 465;

                    $mail->Timeout = 60; // timeout pengiriman (dalam detik)
                    $mail->SMTPKeepAlive = true; 
            
                    $mail->setFrom('admin@roudhatulquran-manggungjaya.sch.id', ''); // user email
                    $mail->addReplyTo('admin@roudhatulquran-manggungjaya.sch.id', ''); //user email
            
                    // Add a recipient
                    $mail->addAddress('to@hostdomain.com'); //email tujuan pengiriman email
            
                    // Email subject
                    $mail->Subject = 'SMTP Codeigniter'; //subject email
            
                    // Set email format to HTML
                    $mail->isHTML(true);
            
                    // Email body content
                    $mailContent = "<h1>SMTP Codeigniterr</h1>
                        <p>Laporan email SMTP Codeigniter.</p>"; // isi email
                    $mail->Body = $mailContent;
            
                    // Send email
                    if(!$mail->send()){
                        echo 'Message could not be sent.';
                        echo 'Mailer Error: ' . $mail->ErrorInfo;
                    }else{
                        echo 'Message has been sent';
                    }
                }

}
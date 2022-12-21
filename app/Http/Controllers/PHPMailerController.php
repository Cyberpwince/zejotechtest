<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PHPMailerController extends Controller
{
    public function composeEmail($email, $message, $subject, $username) {
       // require base_path("vendor/autoload.php");
        $mail = new PHPMailer(true);     // Passing `true` enables exceptions

        try {

            // Email server settings
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = 'mail.privateemail.com';             //  smtp host
            $mail->SMTPAuth = true;
            $mail->Username = 'mail@vixermailer.info';   //  sender username
            $mail->Password = 'Work123@';       // sender password
            $mail->SMTPSecure = 'ssl';                  // encryption - ssl/tls
            $mail->Port = 465;                          // port - 587/465

            $mail->setFrom('mail@vixermailer.info', 'Mail Vixer');
            $mail->addAddress($email);


            $mail->isHTML(true);                // Set email content format to HTML

            $mail->Subject = $subject;
            $mail->Body    = view('email.email', ['mail_message' => $message, 'username' => $username])->render();

            // $mail->AltBody = plain text version of email body;

            if( !$mail->send() ) {
                $result['status'] = "failed";
                $result['msg'] = $mail->ErrorInfo;
                return $result;
            }

            else {
                $result['status'] = "sucess";
                $result['msg'] = "Email has been sent.";
                return $result;
            }

        } catch (Exception $e) {
            $result['status'] = "failed";
            $result['msg'] = 'Message could not be sent.';
             return $result;
        }
    }
}
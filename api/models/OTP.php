<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'PHPMailer/vendor/autoload.php';

function sendOTP($dt) {
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'sample@gmail.com';                     //SMTP username
        $mail->Password   = 'Admin123!';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
        $mail->Port       = 587;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

        //Recipients
        $mail->setFrom('from@example.com', 'Mailer');
        //$mail->addAddress('joe@example.net', 'Joe User');     //Add a recipient
        $mail->addAddress($dt->acc_email);               //Name is optional
        $mail->addReplyTo('201810144@gordoncollege.edu.ph', 'Information');
        //$mail->addCC('cc@example.com');
        //$mail->addBCC('bcc@example.com');

        //Attachments
        //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = "Your WhatsUp One Time Pin";
        $mail->Body    = "
            <html>
                <head></head>
                    <body>
                        <div style='display: flex;
                                    flex-direction: column;
                                    justify-content: center;'>
                                    <div style='margin: 0 auto; width: 75%;'>
                                    <div style='width: fit-content;
                                        padding: 25px;
                                        border: 1px solid dimgray;
                                        border-radius: 20px;
                                        text-align: center;'>
                                            <img src='https://i.ibb.co/jk6RMrs/templogo.png'>
                                            <h2 style='margin-top: -20px;'>
                                                You're on the last step to starting your convenience with Go-cery,
                                                Please use the one time pin code below to complete your registration
                                                with us. Thank you and happy shopping!
                                            </h2>
                                            <p style='background-color: forestgreen;
                                                color: white;
                                                border-radius: 10px;
                                                padding: 15px;
                                                font-size: 20px'>
                                                    $dt->acc_otp
                                            </p>
                                     </div>
                                    </div>
                        </div>
                    </body>
            </html>
        ";
        // $mail->Body    = "Your otp code is $dt->acc_otp";
        $mail->AltBody = "Your otp code is $dt->acc_otp";

        $mail->send();
        
        http_response_code(200);
        return array("status"=>"success", "message"=>"Successfully sent email", "remarks"=>"success");
    } catch (Exception $e) {
        //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";+http_response_code(200);
        return array("status"=>"failed", "message"=>"Failed to send email", "remarks"=>"failed");
    }
}


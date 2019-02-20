<?php

// example on using PHPMailer with GMAIL

include("class.phpmailer.php");
include("class.smtp.php"); // note, this is optional - gets called from main class if not already loaded

class EmailHelper {

    public function send($toArray, $subject, $body,$attachment=false) {

        if (config_disable_email == true) {
            return;
            exit;
        }

        $mail = new PHPMailer();

        $mail->IsSMTP();
        $mail->SMTPAuth = true;                  // enable SMTP authentication
        $mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
        $mail->Host = "smtp.gmail.com";      // sets GMAIL as the SMTP server
        $mail->Port = 465;                   // set the SMTP port

//        $mail->Username = "intouchemailtester@gmail.com";  // GMAIL username
//        $mail->Password = "intouch25";           // GMAIL password
        $mail->Username = "intouchrd@gmail.com";  // GMAIL username
        $mail->Password = "intouch1"; 

       // $mail->From = "intouchemailtester@gmail.com";
        $mail->From = "intouchrd@gmail.com";
        $mail->FromName = "InTouch Email Tester";
        $mail->Subject = $subject;
        $mail->WordWrap = 70; // set word wrap

        $mail->MsgHTML($body);
        
//        //adding attachement of orderdetails on order approval    
        if($attachment){
            $mail->AddAttachment($attachment);
        }
        
//        $mail->AddReplyTo("intouchemailtester@gmail.com", "Intouch Admin");
         $mail->AddReplyTo("intouchrd@gmail.com", "Intouch Admin");
        foreach ($toArray as $to) {
            $mail->AddAddress($to);
        }

        $mail->IsHTML(true); // send as HTML

        if (!$mail->Send()) {

            return $mail->ErrorInfo;
        } else {

            return 0;
        }
    }

}

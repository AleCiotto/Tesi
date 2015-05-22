<?php
require '../lib/PHPMailer/PHPMailerAutoload.php';

function sendEmail($emailDestinatario, $subject, $body) {

  $mail = new PHPMailer;

  //$mail->SMTPDebug = 3;                               // Enable verbose debug output

  $mail->isSMTP();                                      // Set mailer to use SMTP
  $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
  $mail->SMTPAuth = true;                               // Enable SMTP authentication
  $mail->Username = 'webappartigiani.info@gmail.com';                 // SMTP username
  $mail->Password = '1LcL880y';                           // SMTP password
  $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
  $mail->Port = 587;                                     // TCP port to connect to

  $mail->From = 'webappartigiani.info@gmail.com';
  $mail->FromName = 'WebAppArtigiani';
  //$mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
  $mail->addAddress($emailDestinatario);                  // Name is optional
  //$mail->addReplyTo('info@example.com', 'Information');
  //$mail->addCC('cc@example.com');
  //$mail->addBCC('bcc@example.com');

  //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
  //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
  $mail->isHTML(true);                                  // Set email format to HTML

  //$mail->Subject = 'Here is the subject';
  $mail->Subject = $subject;
  //$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
  $mail->Body = $body;
  //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

  if(!$mail->send()) {
      //echo 'Message could not be sent.\n';
      return 'Mailer Error: '.$mail->ErrorInfo;
  } else {
      return true;
  }

}
?>
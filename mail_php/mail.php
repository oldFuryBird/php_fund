<?php
/**
  php 发送邮件测试php

*/
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once "vendor/autoload.php";

/**
    ini_set()动态修改邮件设置
*/
// mail( to subject,message,headers*,**)

$mail =  new PHPMailer(true);
try{
  $user = "ccwc3@163.com";
  $pass = "3244322ZX";
  $host = "smtp.163.com";
    $mail->SMTPDebug=2;
    $mail->isSMTP();
    $mail->Host = $host;
    $mail->SMTPAuth=true;
    $mail->Username = $user;
    $mail->Password = $pass;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port=465;

    $mail->setFrom("ccwc3@163.com",'ccwc3@163.com');
    $mail->addReplyTo("ccwc3@163.com","info");
    $mail->addAddress('253252952@qq.com','asdj');
    
    $mail->Subject="hello php mail";
    $mail->Body = "asjdklj php PHP 邮件";
    $mail->send();
    echo "message send";
}catch(Exception $e){
  echo "Message has not send\n";
  echo "Mailer Error ".$mail->ErrorInfo;
  // var_dump($e);
}
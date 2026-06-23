<?php
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


function sendMail($to, $subject, $body) {

    $env = parse_ini_file(__DIR__ . '/../.env');

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $env['SEND_MAIL_USERNAME'];
        $mail->Password   = $env['SEND_MAIL_PASSWORD'];
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom($env['SEND_MAIL_USERNAME'], 'Digital Tourism Platform');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;

    } catch (Exception $e) {
        return false;
    }
}

function sendAdminMail($subject, $body) {
    $env = parse_ini_file(__DIR__ . '/../.env');
    return sendMail($env['RECVE_MAIL_USERNAME'], $subject, $body);
}

function sendOtpMail($email, $otp) {

    $subject = "OTP Verification - Digital Tourism Platform";

    $body = "
        <h2>Email Verification</h2>
        <p>Your OTP code is:</p>
        <h1 style='color:#ff6600;'>$otp</h1>
        <p>This code will expire in 5 minutes.</p>
    ";

    return sendMail($email, $subject, $body);
}

function sendUserMail($email, $subject, $body) {
    return sendMail($email, $subject, $body);
}

function sendFPOtpMail($email, $otp) {

    $subject = "Password Reset OTP - Digital Tourism Platform";

    $body = "
        <h2>Password Reset Request</h2>
        <p>Your OTP code for password reset is:</p>
        <h1 style='color:#ff6600;'>$otp</h1>
        <p>This code will expire in 3 minutes.</p>
    ";

    return sendMail($email, $subject, $body);
}

function sendResetPWMail($email, $link) {

    $subject = "Password Reset Link - Digital Tourism Platform";

    $body = "
        <h3>Password Reset Link</h3>
        <p>Click this link to reset password:</p>
        <p style='color:#ff6600;'>$link</p>
        <p>This link will expire in 10 minutes.</p>
    ";

    return sendMail($email, $subject, $body);
}
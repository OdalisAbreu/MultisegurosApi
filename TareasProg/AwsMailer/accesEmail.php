<?php
    	/*$mail->isSMTP();
        $mail->Host = 'multiseguros.com.do';
        $mail->SMTPAuth = true;
        $mail->Username = 'operaciones@multiseguros.com.do';
        $mail->Password = '@x43RMcKh9@L';
        $mail->SMTPSecure = 'ssl';
        $mail->From = 'operaciones@multiseguros.com.do';
        $mail->FromName = 'MultiSeguros';
        $mail->Port = '465';
        $mail->SMTPDebug = true;*/
    
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Host = 'mail.segurosexpress.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'operaciones@segurosexpress.com';
        $mail->Password = 'oCgYS@7yIaOO';
        $mail->SMTPSecure = 'ssl';
        $mail->From = 'operaciones@segurosexpress.com';
        $mail->FromName = 'MultiSeguros';
        $mail->Port = '465';
        $mail->SMTPDebug = true;
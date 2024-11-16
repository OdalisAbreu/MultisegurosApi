<?php

function enviarEmail($email, $emailCC, $from, $name, $subject, $html){
    $curl = curl_init();
    
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.ckpnd.com:5001/v1/email',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>'{
        "to": [
            {
                "email": "'.$email.'"
            }
        ],
        "options": {
            "cc": [
                {
                    "email": "'.$emailCC.'"
                }
            ]
        },
        "from": {
            "email": "'.$from.'",
            "name": "'.$name.'"
        },
        "replyTo": {
            "email": "'.$from.'",
            "name": "'.$name.'"
        },
        "subject": "'.$subject.'",
        "body": "'.$html.'"
    }',
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer 3f6cad2f.0f9f49318468647529d45efa',
        'Content-Type: application/json'
      ),
    ));
    
    $response = curl_exec($curl);
    
    curl_close($curl);
    echo $response;
}
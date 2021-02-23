<?php
// Repository link: https://github.com/lazuardyk/transferwise-bot

/////* BOT Configuration */////
$expectedRate = 14200;
$tokenBot = '1072213363:AAEZU11SFubslrFzNnb9JHF1SymWpN_iXi0';
$chatId = '-380598521';
///////////////////////////////

$result = scrape($expectedRate);
if($result){
    $message = 'Transferwise rate now is = '.$result.' (1 USD to IDR)';
    sendMessageToGroup($tokenBot, $chatId, $message);
}

function scrape($expectedRate){
    $url = 'https://transferwise.com/gb/currency-converter/usd-to-idr-rate?amount=1';
    $pageSource = file_get_contents($url);
    preg_match('/config\.currentRate = (.*?);/', $pageSource, $matches);
    if(!empty($matches)){
        $rateNow = (int)$matches[1];
        echo 'Expected rate: '.$expectedRate.', Transferwise rate:', $rateNow;
        if($rateNow >= $expectedRate){
            return $rateNow;
        }
        return false;
    }
    echo "Error! rate is not detected.";
    return false;
}

function sendMessageToGroup($tokenBot, $chatId, $message){
    $method	= "sendMessage";
    $url    = "https://api.telegram.org/bot" . $tokenBot . "/". $method;
    $post = [
     'chat_id' => $chatId,
     'text' => $message
    ];
    $header = [
     "X-Requested-With: XMLHttpRequest",
     "User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.84 Safari/537.36" 
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post );   
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $data = curl_exec($ch);
    return $data;
}

?>
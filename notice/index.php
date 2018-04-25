<?php


$header .= "Cookie: golden_key=cb2hg813iq5o6sltcrgv0kpggeqy7qxh\r\n";

// use key 'http' even if you send the request to https://...
$options = array(
    'http' => array(
        'header'  => $header,
        'method'  => 'POST',
    )
);
$context  = stream_context_create($options);

$lastCount = 0;
$lastOrder = 0;

while (true) {
    //$content = file_get_contents("https://zergo.ru/update/GetAutoUpdaterState", false, $context);
    $content = file_get_contents("https://funpay.ru/", false, $context);

    ///print_r($content);exit;


    if (preg_match("/<span class=\"badge badge-chat\">(\d)<\/span>/iusU", $content, $r)) {
print_r($r);exit;
        $count = $r[1];

        print "update count - {$count}\n";

        if ($r[1] > $lastCount) {
            $lastCount = $r[1];
            print "\nsend\n";

            while (true) {
                mail("garen6666@gmail.com", "New Event ZERGO", "Message: ".$lastCount);

                sleep(5);


                $fileString = file_get_contents("C:/OpenServer/userdata/logs/mail_debug.log");
                $arr = explode("\r\n", $fileString);

                array_pop($arr);
                $string = (array_pop($arr));

                if (substr_count($string, "Completed.")) {
                    print "send comleted\n";
                    break;
                } else {
                    print "send false\n";
                    sleep(2);
                }
            }




        } else {
            $lastCount = $r[1];
        }

        /*preg_match("/\"ord\"\:(\d)\,/uis", $content, $r);

        if ($r[1] > $lastOrder) {
            $lastOrder = $r[1];

            print "\nsend\n";
            mail("garen6666@gmail.com", "New Order ZERGO", "Message: ".$lastOrder);
        } else {
            $lastOrder = $r[1];
        }*/
    }
    sleep(10);
}


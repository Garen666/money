<?php
//header ("Content-Type: text/html; charset=utf-8");
date_default_timezone_set('Europe/Moscow');
//$link = mysqli_connect('91.192.46.28', 'root', 'root', "", "1045");
$link = mysqli_connect('192.168.1.45', 'root', 'root');
mysqli_set_charset($link, 'utf8');
if (!$link) {
    die('Ошибка соединения: ' . mysql_error());
}
mysqli_select_db($link, "PW_Comission_Items");

$startDate = time();
$startDate = date('Y-m-d', strtotime('-1 month', $startDate));
//$startDate = date('Y-m-d', strtotime('-2 week', $startDate));

$resultEndTimeArray = array();
$result = mysqli_query($link, "SELECT * FROM `days`");

while ($row = mysqli_fetch_array($result)) {
    $date =  new DateTime();
    $date->setTimestamp($row["time"]);

    $resultEndTimeArray[$row["servername"]] = $date->diff(new DateTime())->days;
}
$result = mysqli_query($link, "SELECT * FROM `moneyOut` WHERE `date` > '{$startDate}'");

$resultOutArray = array();


while ($row = mysqli_fetch_array($result)) {
    $keyArray = explode(" ", $row['date']);
    $keyArray = $keyArray[0];
    $keyArray2 = $row["server"];

    @$resultOutArray[$keyArray][$keyArray2] += $row["money"];
}


$result = mysqli_query($link, "SELECT * FROM `money` WHERE `date` > '{$startDate}'");

$resultMonthArray = array();
$resultWeekArray = array();
$resultPrevDayArray = array();
$resultDayArray = array();
$resultLastArray = array();
$resultFreeArray = array();
$resultVersionArray = array();


$lastUpdateArray = array();

$lastDateOut = false;

while ($row = mysqli_fetch_array($result)) {
    $dateString = explode(" ", $row['date']);
    $dateString = $dateString[0];

    $date = new DateTime($dateString);


    $date2 = new DateTime(date("Y-m-d"));

    $diff = $date2->diff($date)->days;

    $server = $row["server"];

    $resultLastArray[$row["server"]] = $row["money"];
    $resultFreeArray[$row["server"]] = $row["freemoney"];
    $resultVersionArray[$row["server"]] = $row["version"];

    $lastUpdateArray[$server] = $row['date'];

    if (!isset($resultMonthArray[$server]["min"])) {
        $resultMonthArray[$server]["min"] = $row["money"];
    }

    $resultMonthArray[$server]["max"] = $row["money"];



    if ($diff < 7) {

        if (!isset($resultWeekArray[$server]["min"])) {
            $resultWeekArray[$server]["min"] = $row["money"];
        }

        $resultWeekArray[$server]["max"] = $row["money"];

        if ($diff == 1) {
            if (!isset($resultPrevDayArray[$server]["min"])) {
                $resultPrevDayArray[$server]["min"] = $row["money"];
            }

            $resultPrevDayArray[$server]["max"] = $row["money"];
        }


        if ($diff == 0) {
            if (!isset($resultDayArray[$server]["min"])) {
                $resultDayArray[$server]["min"] = $row["money"];
            }

            $resultDayArray[$server]["max"] = $row["money"];
        }

    }

}

foreach ($resultOutArray as $key => $data) {
    foreach ($data as $serverName => $money) {
        $dateString = $key;
        $date = new DateTime($dateString);


        $date2 = new DateTime(date("Y-m-d"));

        $diff = $date2->diff($date)->days;


        @$resultMonthArray[$serverName]["out"] += $money;

        if ($diff < 7) {

            @$resultWeekArray[$serverName]["out"] += $money;

            if ($diff == 1) {
                @$resultPrevDayArray[$serverName]["out"] += $money;
            }

            if ($diff == 0) {
                @$resultDayArray[$serverName]["out"] += $money;
            }
        }
    }
}

$resultMonthArray2 = array();
$resultWeekArray2 = array();
$resultPrevDayArray2 = array();
$resultDayArray2 = array();

$resultMonthArray2['Орион'] = false;
$resultMonthArray2['Электра'] = false;
$resultMonthArray2['Мира'] = false;
$resultMonthArray2['Дракон'] = false;
$resultMonthArray2['Гелиос'] = false;
$resultMonthArray2['Атлас'] = false;
$resultMonthArray2['Кассиопея'] = false;
$resultMonthArray2['Гидра'] = false;
$resultMonthArray2['Лисичка'] = false;
$resultMonthArray2['Цербер'] = false;

$resultMonthArray2['Орион_2'] = false;
$resultMonthArray2['Электра_2'] = false;
//$resultMonthArray2['Мира_2'] = false;
$resultMonthArray2['Дракон_2'] = false;
$resultMonthArray2['Гелиос_2'] = false;
$resultMonthArray2['Атлас_2'] = false;
$resultMonthArray2['Кассиопея_2'] = false;
$resultMonthArray2['Гидра_2'] = false;
$resultMonthArray2['Лисичка_2'] = false;
$resultMonthArray2['Цербер_2'] = false;

foreach ($resultMonthArray as $serverName => $data) {
    $sum = $data['max'] - $data['min'];
    $sum /= 1000;
    @$sum += $data["out"];

    $resultMonthArray2[$serverName] = $sum;
}

foreach ($resultWeekArray as $serverName => $data) {
    $sum = $data['max'] - $data['min'];
    $sum /= 1000;
    @$sum += $data["out"];

    $resultWeekArray2[$serverName] = $sum;
}

foreach ($resultDayArray as $serverName => $data) {
    $sum = $data['max'] - $data['min'];
    $sum /= 1000;
    @$sum += $data["out"];

    $resultDayArray2[$serverName] = $sum;
}


foreach ($resultPrevDayArray as $serverName => $data) {
    $sum = $data['max'] - $data['min'];
    $sum /= 1000;
    @$sum += $data["out"];

    $resultPrevDayArray2[$serverName] = $sum;
}


echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
<html>
<head>
<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">
<title>Bablo</title>
</head>
<body>";
print "<h1>Комиссионер</h1>";
print "<table border='1'><thead><tr><td>Сервер / Заработок</td><td>За день</td><td>За вчера</td><td>За неделю</td><td>За месяц</td><td>Сейчас</td><td>Свободно</td><td>Осталось дней</td><td>Версия</td><td>С последнего обновления прошло</td></tr></thead><tbody>";

$allMoney3 = 0;
$allPrice3 = 0;

$allMoney2 = 0;
$allPrice2 = 0;

$allMoney1 = 0;
$allPrice1 = 0;

$allMoneyPrev = 0;
$allPricePrev = 0;



$date2 = new DateTime(date("Y-m-d"));

foreach ($resultMonthArray2 as $serverName => $money3) {
    $money3 = round($money3);
    $allMoney3 += $money3;

    $money2 = round($resultWeekArray2[$serverName]);
    $allMoney2 += $money2;

    $money1 = round($resultDayArray2[$serverName]);
    $allMoney1 += $money1;

    $moneyPrev = round($resultPrevDayArray2[$serverName]);
    $allMoneyPrev += $moneyPrev;

    $lastMoney = round($resultLastArray[$serverName] / 1000);
    $freeMoney = round($resultFreeArray[$serverName] / 1000);

    if ($serverName == "Лисичка" || $serverName == "Лисичка_2") {
        $price3 = $money3 * 5;
        $price2 = $money2 * 5;
        $price1 = $money1 * 5;
        $pricePrev = $moneyPrev * 5;
    } else if ($serverName == "Цербер" || $serverName == "Цербер_2") {
        $price3 = $money3 * 4;
        $price2 = $money2 * 4;
        $price1 = $money1 * 4;
        $pricePrev = $moneyPrev * 4;
    } else {
        $price3 = $money3 * 3;
        $price2 = $money2 * 3;
        $price1 = $money1 * 3;
        $pricePrev = $moneyPrev * 3;
    }

    $allPrice3 += $price3;
    $allPrice2 += $price2;
    $allPrice1 += $price1;
    $allPricePrev += $pricePrev;

    $lastUpdate = $lastUpdateArray[$serverName];

    $to_time = strtotime($lastUpdate);
    $from_time = time();
    $lastUpdate = round(abs($to_time - $from_time) / 60);

    $color = '#60ff73';

    if ($lastUpdate > 60) {
        $color = 'red';
    }

    $lastUpdate .= " минут";

    $dayEndTime = $resultEndTimeArray[$serverName];
    $version = $resultVersionArray[$serverName];

    print "<tr><td><a href='/stat/?server={$serverName}' >{$serverName}</a> <a href='/statitem3/?server={$serverName}'>M</a></td><td>{$money1}kk ({$price1} грн.)</td><td>{$moneyPrev}kk ({$pricePrev} грн.)</td><td>{$money2}kk ({$price2} грн.)</td><td>{$money3}kk ({$price3} грн.)</td><td>{$lastMoney} kk</td><td>{$freeMoney} kk</td><td align='right'>{$dayEndTime}</td><td align='right'>{$version}</td><td style='background-color: {$color};'>{$lastUpdate}</td></tr>";
}

$allMoney = 0;

foreach ($resultLastArray as $money) {
    $allMoney += $money;
}

$allFreeMoney = 0;

foreach ($resultFreeArray as $money) {
    $allFreeMoney += $money;
}

$allMoney = round($allMoney / 1000);
$allFreeMoney = round($allFreeMoney / 1000);
print "<tr><td>Всего: ".count($resultMonthArray2)."</td><td>{$allMoney1}kk ({$allPrice1} грн.)</td><td>{$allMoneyPrev}kk ({$allPricePrev} грн.)</td><td>{$allMoney2}kk ({$allPrice2} грн.)</td><td>{$allMoney3}kk ({$allPrice3} грн.)</td><td>{$allMoney} kk</td><td>{$allFreeMoney} kk</td><td></td><td></td><td></td></tr>";

print "</tbody></table>";


echo "</body></html>";

?>

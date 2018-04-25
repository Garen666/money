<?php
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
<html>
<head>
<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">
<title>Bablo</title>
</head>
<body>";

//header ("Content-Type: text/html; charset=utf-8");
$link = mysql_connect('192.168.1.45', 'root', 'root');
mysql_set_charset('utf8');
if (!$link) {
    die('Ошибка соединения: ' . mysql_error());
}

if (count($_GET) == 0) {

    print "<a href='/stat/?server=Орион'>Орион</a><br>";
    print "<a href='/stat/?server=Орион_2'>Орион_2</a><br>";
    print "<a href='/stat/?server=Вега'>Вега</a><br>";
    print "<a href='/stat/?server=Сириус'>Сириус</a><br>";
    print "<a href='/stat/?server=Мира'>Мира</a><br>";
    print "<a href='/stat/?server=Дракон'>Дракон</a><br>";
    print "<a href='/stat/?server=Дракон_2'>Дракон_2</a><br>";
    print "<a href='/stat/?server=Гелиос'>Гелиос</a><br>";
    print "<a href='/stat/?server=Атлас'>Атлас</a><br>";
    print "<a href='/stat/?server=Кассиопея'>Кассиопея</a><br>";
    print "<a href='/stat/?server=Гидра'>Гидра</a><br>";
    print "<a href='/stat/?server=Луна'>Луна</a><br>";
    print "<a href='/stat/?server=Луна_2'>Луна_2</a><br>";
    print "<a href='/stat/?server=Лисичка'>Лисичка</a><br>";
    print "<a href='/stat/?server=Лисичка_2'>Лисичка_2</a><br>";
} else {
    $server = $_GET["server"];
    mysql_select_db("PW_Comission_Items");
    $startDate = time();
    $weekDate = date('Y-m-d', strtotime('-1 week', $startDate));
    $startDate = date('Y-m-d', strtotime('-1 month', $startDate));
    //$startDate = date('Y-m-d', strtotime('-4 month', $startDate));

    $allItemsName = array();
    $result = mysql_query("SELECT * FROM `items`");

    while ($row = mysql_fetch_array($result)) {
        $allItemsName[$row["itemId"]] = $row["name"];
    }

//print_r($allItemsName);exit;

    $link = mysql_connect('192.168.1.45', 'root', 'root');
    mysql_select_db($server);

    $dayArray = array();
    $allItemsMarginInfo = array();
    $allItemsMarginAllInfo = array();
    $result = mysql_query("SELECT * FROM `itemStatistics` WHERE `date` > '{$startDate}'");
    while ($row = mysql_fetch_array($result)) {
        if ($row["margin"] < 0) {
            continue;
        }

        $week = false;
        $allItemsMarginAllInfo[$row["itemId"]]['month']["count"] += 1;
        $allItemsMarginAllInfo[$row["itemId"]]['month']["margin"] += $row["margin"];

        $day = explode(' ', $row["date"])[0];


        if ($day >= $weekDate) {
            $week = true;
        }

        if ($week) {
            $allItemsMarginAllInfo[$row["itemId"]]['week']["count"] += 1;
            $allItemsMarginAllInfo[$row["itemId"]]['week']["margin"] += $row["margin"];
        }

        if ($row["status"] == "ok") {
            continue;
        } else if (strpos($row["status"], 'bad margin') !== 0) {
            continue;
        }

        $status = trim(str_replace(array('bad margin', '(', ')'), '', $row["status"]));
        $status = explode(' < ', $status);

        $allItemsMarginInfo[$row["itemId"]]['month']["count"] += 1;
        $allItemsMarginInfo[$row["itemId"]]['month']["sum1"] += $status[0];
        $allItemsMarginInfo[$row["itemId"]]['month']["sum2"] += $status[1];

        if ($week) {
            $allItemsMarginInfo[$row["itemId"]]['week']["count"] += 1;
            $allItemsMarginInfo[$row["itemId"]]['week']["sum1"] += $status[0];;
            $allItemsMarginInfo[$row["itemId"]]['week']["sum2"] += $status[1];;
        }

    }

    print "<h1>Статистика по серверу {$server}</h1>";
    print "<table border='1'><thead><tr><td> </td>";
    print "<td >За неделю</td><td >За месяц</td><td>Средняя маржа за неделю</td><td>Средняя маржа за месяц</td></tr></thead><tbody>";

    $allmargin = 0;
    foreach($allItemsName as $itemId => $itemName) {
        $weekCountAll = (int) $allItemsMarginAllInfo[$itemId]['week']['count'];

        if (!$weekCountAll) {
            continue;
        }

        $weekCount = (int) $allItemsMarginInfo[$itemId]['week']['count'];

        print "<tr><td><a href='/statitem2/?server={$server}&itemId={$itemId}'>";
        print $itemName;
        print "</a></td>";

        print "<td>" . (int) $weekCount . " / " .  $weekCountAll . " (". @(int)($allItemsMarginInfo[$itemId]['week']['sum1'] / $weekCount) ." / " . @(int)($allItemsMarginInfo[$itemId]['week']['sum2'] / $weekCount) . ")</td>";
        print "<td>" . (int) $allItemsMarginInfo[$itemId]['month']['count'] . " / " .  (int) $allItemsMarginAllInfo[$itemId]['month']['count'] . " (" . @(int)($allItemsMarginInfo[$itemId]['month']["sum1"] / $allItemsMarginInfo[$itemId]['month']['count']) ." / " . @(int)($allItemsMarginInfo[$itemId]['month']["sum2"] / $allItemsMarginInfo[$itemId]['month']['count']) . ")</td>";
        print '<td>' . @(int)($allItemsMarginAllInfo[$itemId]['week']['margin'] / $allItemsMarginAllInfo[$itemId]['week']['count']) . "</td>";
        print '<td>' . @(int)($allItemsMarginAllInfo[$itemId]['month']['margin'] / $allItemsMarginAllInfo[$itemId]['month']['count']) . "</td>";

        print "</tr>";
    }

    print "</tbody></table>";
}



function makePriceK($price) {
    return round($price / 1000, 2) . "k";
}

function makePriceKK($price) {
    return round($price / 1000000, 2) . "kk";
}
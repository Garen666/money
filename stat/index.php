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
    $allItemsBuyInfo = array();
    //$result = mysql_query("SELECT * FROM `buyData` WHERE `date` > '2017-09-01' AND `date` < '2017-10-01'");
    $result = mysql_query("SELECT * FROM `buyData` WHERE `date` > '{$startDate}'");
    while ($row = mysql_fetch_array($result)) {
        $day = explode(' ', $row["date"])[0];
        $dayArray[$day] = $day;
        $allItemsBuyInfo[$row["itemId"]][$day]["count"] += $row["count"];
        $allItemsBuyInfo[$row["itemId"]][$day]["price"] += $row["price"];
        $allItemsBuyInfo[$row["itemId"]][$day]["sum"] += $row["price"] * $row["count"];
        $allItemsBuyInfo[$row["itemId"]]["all"]["sum"] += $row["price"] * $row["count"];
        $allItemsBuyInfo[$row["itemId"]]["all"]["count"] += $row["count"];
    }


    $allItemsSaleInfo = array();
    //$result = mysql_query("SELECT * FROM `saleData` WHERE `date` > '2017-09-01' AND `date` < '2017-10-01'");
    $result = mysql_query("SELECT * FROM `saleData` WHERE `date` > '{$startDate}'");
    while ($row = mysql_fetch_array($result)) {

        $day = explode(' ', $row["date"])[0];
        $dayArray[$day] = $day;
        $allItemsSaleInfo[$row["itemId"]][$day]["count"] += $row["count"];
        $allItemsSaleInfo[$row["itemId"]][$day]["price"] += $row["price"];
        $allItemsSaleInfo[$row["itemId"]][$day]["sum"] += $row["price"] * $row["count"];
        $allItemsSaleInfo[$row["itemId"]]["all"]["sum"] += $row["price"] * $row["count"];
        $allItemsSaleInfo[$row["itemId"]]["all"]["count"] += $row["count"];
    }

    if (isset($allItemsSaleInfo[12979]) && !isset($allItemsBuyInfo[12979]) && isset($allItemsBuyInfo[39872]) && !isset($allItemsSaleInfo[39872])) {
        $allItemsSaleInfo[39872] = $allItemsSaleInfo[12979];
    }


    print "<h1>Статистика по серверу {$server}</h1>";
    print "<table border='1'><thead><tr><td rowspan='3'>Товар / Дата</td>";


    foreach($dayArray as $day) {
        print "<td colspan='3'>{$day}</td>";
    }

    print "<td rowspan='2'>Куплено за месяц</td><td rowspan='2'>Продаж за месяц</td><td rowspan='2'>Маржа за месяц</td>";

    print "</tr><tr>";
    foreach($dayArray as $day) {
        print "<td>Куплено</td><td>Продано</td><td>Маржа</td>";
    }

    print "</tr></thead><tbody>";
    $allmargin = 0;
    foreach($allItemsBuyInfo as $itemId => $arr) {
        if ($itemId == "all") {
            continue;
        }

        print "<tr><td><a href='/statitem2/?server={$server}&itemId={$itemId}'>";
        print $allItemsName[$itemId];
        print "</a></td>";

        foreach($dayArray as $day) {
            $countBuy = (int) $arr[$day]["count"];
            @$priceBuy = (int) round($arr[$day]["sum"] / (int) $arr[$day]["count"]);
            $countSale = (int) $allItemsSaleInfo[$itemId][$day]["count"];
            @$priceSale = (int) round($allItemsSaleInfo[$itemId][$day]["sum"] / $allItemsSaleInfo[$itemId][$day]["count"]);


            $margin = $allItemsSaleInfo[$itemId][$day]["sum"] - $arr[$day]["sum"];

            print "<td nowrap bgcolor='#d6f7ff'>{$countBuy}шт (". makePriceK($priceBuy).")</td>";
            print "<td nowrap bgcolor='#ffcecc'>{$countSale}шт (" . makePriceK($priceSale).")</td>";
            print "<td nowrap bgcolor='#a9ffae'>" . makePriceKK($margin)."</td>";
        }



        if ($allItemsSaleInfo[$itemId]["all"]["count"] >  $allItemsBuyInfo[$itemId]["all"]["count"]) {
            $count = $allItemsBuyInfo[$itemId]["all"]["count"];
        } else {
            $count = $allItemsSaleInfo[$itemId]["all"]["count"];
        }

        @$price1 = $allItemsSaleInfo[$itemId]["all"]["sum"] / $allItemsSaleInfo[$itemId]["all"]["count"];
        @$price2 = $allItemsBuyInfo[$itemId]["all"]["sum"] / $allItemsBuyInfo[$itemId]["all"]["count"];

        print "<td nowrap bgcolor='#7578ff'>" . $allItemsBuyInfo[$itemId]["all"]["count"]."шт (".makePriceK($price2).")</td>";
        print "<td nowrap bgcolor='#ff6773'>" . $allItemsSaleInfo[$itemId]["all"]["count"]."шт (".makePriceK($price1).")</td>";
        print "<td nowrap bgcolor='#4cff50'>";

        $allmargin += (($price1 - $price2) * $count);
        print makePriceKK(($price1 - $price2) * $count);
        print "</td>";
        print "</tr>";
    }

    print "<tr><td></td>";
    foreach($dayArray as $day) {
        print "<td colspan='3'></td>";
    }
    print "<td></td><td></td><td>Всего: ".makePriceKK($allmargin)."</td>";
    print "</tr>";

    print "</tbody></table>";
}



function makePriceK($price) {
    return round($price / 1000, 2) . "k";
}

function makePriceKK($price) {
    return round($price / 1000000, 2) . "kk";
}
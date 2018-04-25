<?php


$link = mysql_connect('192.168.1.45', 'root', 'root');
mysql_set_charset('utf8');
if (!$link) {
    die('Ошибка соединения: ' . mysql_error());
}



$itemId = $_GET["itemId"];
$server = $_GET["server"];
mysql_select_db($server);

$result = mysql_query("SELECT * FROM `itemStatistics` WHERE `itemId` = {$itemId}");
$dataArray = array();
$dataBuyArray = array();
$marginArray = array();
$priceSaleArray = array();
$priceBuyArray = array();

$dayArray = array();

while ($row = mysql_fetch_array($result)) {
    $time = $row["date"];
    $day = explode(' ', $row["date"])[0];
    $dayArray[] = $row["date"];
    //$time = explode('-', $row["date"], 2)[1];
    //$time = explode(':', $time);
    //$time = $time[0].":".$time[1];

    $dataArray[$time] = $row["salecount"];
    $dataBuyArray[$time] = $row["buycount"];
    $marginArray[$time] = $row["margin"];
    $priceSaleArray[$time] = $row["saleprice"];
    $priceBuyArray[$time] = $row["buyprice"];
}

print "<script>var globalData3 = [";
foreach ($dataArray as $day => $value) {
    $day2 = date('Y-m-d H:m:s', strtotime('-8 days', strtotime($day)));
    $result = mysql_query("SELECT * FROM `itemStatistics` WHERE `itemId` = {$itemId} AND `date` < '{$day}' AND `date` > '{$day2}'");

    $saleCount = 0;
    $buyCount = 0;

    $oldBuyCount = 0;
    while ($row = mysql_fetch_array($result)) {
        $rowBuyCount = $row['buycount'];
        $rowSaleCount = $row['salecount'];

        if ($rowBuyCount == 0) {
            $rowBuyCount = $oldBuyCount;
        } else {
            $oldBuyCount = $rowBuyCount;
        }


        if ($rowBuyCount > 0) {

            if ($rowSaleCount < $rowBuyCount * 0.05) {
                $rowBuyCount *= 5;
            } else if ($rowSaleCount < $rowBuyCount * 0.1) {
                $rowBuyCount *= 2;
            }

        }

        $buyCount += $rowBuyCount;
        $saleCount += $rowSaleCount;
    }

    /*$result = mysql_query("SELECT * FROM `saleData` WHERE `itemId` = {$itemId} AND `date` < '{$day}' AND `date` > '{$day2}'");

    while ($row = mysql_fetch_array($result)) {
        $saleCount += $row['count'];
    }*/

    if ($buyCount > 0) {
        @$coef = $saleCount / $buyCount;
    } else {
        @$coef = 0;
    }


    $day2 = date('Y-m-d H:m:s', strtotime('-24 hour', strtotime($day)));
    $result = mysql_query("SELECT * FROM `itemStatistics` WHERE `itemId` = {$itemId} AND `date` < '{$day}' AND `date` > '{$day2}'");

    $saleCount = 0;
    $buyCount = 0;

    while ($row = mysql_fetch_array($result)) {
        $buyCount += $row['buycount'];
        $saleCount += $row['salecount'];
    }

    /*$result = mysql_query("SELECT * FROM `saleData` WHERE `itemId` = {$itemId} AND `date` < '{$day}' AND `date` > '{$day2}'");

    while ($row = mysql_fetch_array($result)) {
        $saleCount += $row['count'];
    }*/

    if ($buyCount > 0) {
        @$coef2 = $saleCount / $buyCount;

        if ($coef2 > 5) {
            $coef2 = 5;
        }
    } else {
        @$coef2 = 0;
    }

    //print "['{$day}', {$coef}, {$coef2}],";
    print "['{$day}', {$coef}],";
}
print "];</script>";


print "<script>var globalData4 = [";
foreach ($dataArray as $day => $value) {
    $day2 = date('Y-m-d H:m:s', strtotime('-24 hour', strtotime($day)));
    $result = mysql_query("SELECT * FROM `itemStatistics` WHERE `itemId` = {$itemId} AND `date` < '{$day}' AND `date` > '{$day2}'");

    $saleCount = 0;
    $buyCount = 0;

    while ($row = mysql_fetch_array($result)) {
        $buyCount += $row['buycount'];
        $saleCount += $row['salecount'];
    }


    print "['{$day}', {$buyCount}, {$saleCount}],";
}
print "];</script>";


print "<script>var globalData = [";
foreach ($dataArray as $key => $value) {
    $value2 = $dataBuyArray[$key];
    print "['{$key}', {$value2}, {$value}],";
}
print "];</script>";


print "<script>var globalData2 = [";
$lastPrice = 0;
$lastPrice2 = 0;
foreach ($marginArray as $key => $value) {
    $value2 = $priceBuyArray[$key];
    $value3 = $priceSaleArray[$key];


    if ($value2 == 0) {
        $value2 = $lastPrice;
    } else {
        $lastPrice = $value2;
    }

    if ($value3 == 0) {
        $value3 = $lastPrice2;
    } else {
        $lastPrice2 = $value3;
    }


    print "['{$key}',{$value2}, {$value3}, {$value}],";
}
print "];</script>";

print '<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script><div id="chart_div"></div><div id="chart_div3"></div><div id="chart_div4"></div><div id="chart_div2"></div>';


print '<script type="text/javascript" src="index.js"></script>';

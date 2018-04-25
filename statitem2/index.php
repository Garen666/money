<?php

echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
<html>
<head>
<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">
<title>Bablo</title>
</head>
<body>";

$link = mysql_connect('192.168.1.45', 'root', 'root');
mysql_set_charset('utf8');
if (!$link) {
    die('Ошибка соединения: ' . mysql_error());
}


mysql_select_db("PW_Comission_Items");
$startDate = time();
$startDate = date('Y-m-d', strtotime('-1 month', $startDate));


$allItemsName = array();
$result = mysql_query("SELECT * FROM `items`");

while ($row = mysql_fetch_array($result)) {
    $allItemsName[$row["itemId"]] = $row["name"];
}


$itemId = $_GET["itemId"];
$server = $_GET["server"];
mysql_select_db($server);

print "<h1 align='center'>{$server} / " . $allItemsName[$itemId] . "</h1>";

$day2 = date('Y-m-d H:m:s', strtotime('-38 days'));
$day3 = date('Y-m-d H:m:s', strtotime('-30 days'));
$result = mysql_query("SELECT * FROM `itemStatistics` WHERE `itemId` = {$itemId} AND `date` > '{$day2}'");
$dataArray = array();
$dataBuyArray = array();
$marginArray = array();
$priceSaleArray = array();
$priceBuyArray = array();
$allDataArray = array();
$dayArray = array();

while ($row = mysql_fetch_array($result)) {
    $time = $row["date"];
    $day = explode(' ', $row["date"])[0];

    $allDataArray[$day][$time]["salecount"] = $row["salecount"];
    $allDataArray[$day][$time]["buycount"] = $row["buycount"];
    $allDataArray[$day][$time]["margin"] = $row["margin"];
    $allDataArray[$day][$time]["saleprice"] = $row["saleprice"];
    $allDataArray[$day][$time]["buyprice"] = $row["buyprice"];

    if ($day2 > $time) {
        continue;
    }
    $dayArray[] = $row["date"];
    $dataArray[$time] = $row["salecount"];
    $dataBuyArray[$time] = $row["buycount"];
    $marginArray[$time] = $row["margin"];
    $priceSaleArray[$time] = $row["saleprice"];
    $priceBuyArray[$time] = $row["buyprice"];
}

$arr4 = array();

$arr5 = array();

print "<script>var globalData3 = [";
foreach ($dataArray as $day => $value) {

    $dayArray2 = array();
    for ($i = 9; $i >= 0; $i--) {
        $dayArray2[] = date('Y-m-d', strtotime("-{$i} days", strtotime($day)));
    }
    $day2 = date('Y-m-d H:m:s', strtotime('-8 days', strtotime($day)));

    $timedate = strtotime($day);
    $timedate2 = strtotime($day2);

    //print $day2;
    //print "--" . $day . "--";
    //print_r($dayArray2);exit;
    //$result = mysql_query("SELECT * FROM `itemStatistics` WHERE `itemId` = {$itemId} AND `date` < '{$day}' AND `date` > '{$day2}'");

    //print $timedate."\n".$timedate2."\n";
    $saleCount = 0;
    $buyCount = 0;

    $oldBuyCount = 0;
    foreach ($dayArray2 as $day2) {
        //print "\nnow=$day2";
        $myArray = $allDataArray[$day2];

        if (!$myArray) {
            continue;
        }

        foreach ($myArray as $time2 => $row) {

            //print "\nnowtime=$time2";
            $time3 = strtotime($time2);
            if ($time3 < $timedate && $time3 > $timedate2) {
                //print "\nq";
            } else {
                //print "\nw";
                continue;
            }
            /*if (explode(":", $time2)[0] > explode(":", $day)) {
                continue;
            }*/

            $arr5[$time2]["salePrice"] = $row['saleprice'];
            $arr5[$time2]["buyPrice"] = $row['buyprice'];

            $rowBuyCount = $row['buycount'];
            $rowSaleCount = $row['salecount'];

            /*if ($rowBuyCount == 0) {
                $rowBuyCount = $oldBuyCount / 2;
            } else {
                $oldBuyCount = $rowBuyCount / 2;
            }*/


            if ($rowBuyCount > 0) {

                if ($rowSaleCount < $rowBuyCount * 0.05) {
                    $rowBuyCount *= 1.5;
                } else if ($rowSaleCount < $rowBuyCount * 0.1) {
                    $rowBuyCount *= 1.2;
                }

            }

            $buyCount += $rowBuyCount;
            $saleCount += $rowSaleCount;
        }



    }

    $arr4[$day]["buyCount"] = $buyCount;
    $arr4[$day]["saleCount"] = $saleCount;

    /*$result = mysql_query("SELECT * FROM `saleData` WHERE `itemId` = {$itemId} AND `date` < '{$day}' AND `date` > '{$day2}'");

    while ($row = mysql_fetch_array($result)) {
        $saleCount += $row['count'];
    }*/
    if ($buyCount > 0) {
        @$coef = $saleCount / $buyCount;
    } else {
        @$coef = 0;
    }
    print "['{$day}', {$coef}],";

}
print "];</script>";

print "<script>var globalData = [";
foreach ($dataArray as $key => $value) {
    $value2 = $dataBuyArray[$key];
    print "['{$key}', {$value2}, {$value}],";
}
print "];</script>";

print "<script>var globalData4 = [";
foreach ($arr4 as $key => $value) {
    $value2 = $value["buyCount"];
    $value3 = $value["saleCount"];
    print "['{$key}', {$value2}, {$value3}],";
}
print "];</script>";



print "<script>var globalData5 = [";
foreach ($arr5 as $day => $arr) {
    $value2 = $arr["buyPrice"];
    $value3 = $arr["salePrice"];
    print "['{$day}', {$value2}, {$value3}],";
}
print "];</script>";



print '<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script><div id="chart_div"></div><div id="chart_div3"></div><div id="chart_div4"></div><div id="chart_div5"></div>';


print '<script type="text/javascript" src="index.js"></script>';

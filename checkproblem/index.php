<?php
//header ("Content-Type: text/html; charset=utf-8");

$IdsArray = array(56813, 56830, 56851);
$serverName = "Вега";

$link = mysql_connect('192.168.1.45', 'root', 'root');
mysql_set_charset('utf8');
if (!$link) {
    die('Ошибка соединения: ' . mysql_error());
}
mysql_select_db("PW_Comission_Items");

$result = mysql_query("SELECT * FROM `money` WHERE `id` IN (" . implode($IdsArray, ",") . ")");

$resultMoneyDateArray = array();
$resultMoneyMoneyArray = array();


while ($row = mysql_fetch_array($result)) {
    if (!$resultMoneyDateArray["start"]) {
        $resultMoneyDateArray["start"] = $row['date'];
    }

    $resultMoneyDateArray["finish"] = $row['date'];
    $resultMoneyMoneyArray[$row["date"]] = $row["money"];
}


$resultDataArray = array();
mysql_select_db($serverName);
$result = mysql_query("SELECT * FROM `itemStatistics` WHERE `date` > '".$resultMoneyDateArray["start"]."' AND `date` <= '".$resultMoneyDateArray["finish"]."'");
while ($row = mysql_fetch_array($result)) {
    $resultDataArray[$row["itemId"]]["margin"][] = $row["margin"];
    $resultDataArray[$row["itemId"]]["salecount"][] = $row["salecount"];
    $resultDataArray[$row["itemId"]]["saleprice"][] = $row["saleprice"];
    $resultDataArray[$row["itemId"]]["buycount"][] = $row["buycount"];
    $resultDataArray[$row["itemId"]]["buyprice"][] = $row["buyprice"];
    $resultDataArray[$row["itemId"]]["status"][] = $row["status"];
}


echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
<html>
<head>
<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">
<title>Bablo</title>
</head>
<body>";
print "<table border='1'><thead><tr><td>Id</td><td>Margin</td><td>SaleCount</td><td>SalePrice</td><td>BuyCount</td><td>BuyPrice</td><td>Status</td></tr></thead><tbody>";

foreach ($resultDataArray as $itemId => $arr) {
    $marginStr = "";
    foreach ($arr["margin"] as $value) {
        if ($marginStr != "") {
            $marginStr .= "/";
        }

        $marginStr .= $value;
    }

    $saleCountStr = "";
    foreach ($arr["salecount"] as $value) {
        if ($saleCountStr != "") {
            $saleCountStr .= "/";
        }

        $saleCountStr .= $value;
    }

    $salePriceStr = "";
    foreach ($arr["saleprice"] as $value) {
        if ($salePriceStr != "") {
            $salePriceStr .= "/";
        }

        $salePriceStr .= $value;
    }

    $buyCountStr = "";
    foreach ($arr["buycount"] as $value) {
        if ($buyCountStr != "") {
            $buyCountStr .= "/";
        }

        $buyCountStr .= $value;
    }

    $buyPriceStr = "";
    foreach ($arr["buyprice"] as $value) {
        if ($buyPriceStr != "") {
            $buyPriceStr .= "/";
        }

        $buyPriceStr .= $value;
    }

    $statusStr = "";
    foreach ($arr["status"] as $value) {
        if ($statusStr != "") {
            $statusStr .= "/";
        }

        $statusStr .= $value;
    }

    print "<tr><td>{$itemId}</td><td>$marginStr</td><td>$saleCountStr</td><td>$salePriceStr</td><td>$buyCountStr</td><td>$buyPriceStr</td><td>$statusStr</td></tr>";
}

print "</tbody></table>";


echo "</body></html>";

?>

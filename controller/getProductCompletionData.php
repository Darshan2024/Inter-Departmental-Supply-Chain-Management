<?php
include_once "../config/database.php";
include_once "functions.php";
session_start();

$products = json_decode(select_query($con, "*", "product_master", "enabled='1'", "", "", ""));
$finalResult = [];
$productNames = [];
$estimateds = [];
$takens = [];

foreach ($products as $product) {
    $productNames[] = $product->name;
    $estimateds[] = intval($product->eta);

    $timeTaken = 0;

    // $progress = select_query($con, "*", "chain_progress_master", "enabled='1' AND productId=" . $product->id, "deptId ASC", "", "");
    $progress = json_decode(select_query($con, "*", "chain_progress_master", "enabled='1' AND productId=" . $product->id, "deptId ASC", "", ""));

    $start = $end = '';
    foreach ($progress as $p) {
        if ($p->deptId == 1)
            $start = new DateTime($p->createdOn);

        $end = new DateTime($p->createdOn);
    }

    if ($start != '' && $end != '')
        $timeTaken = $start->diff($end)->days;

    $takens[] = $timeTaken;
}

$finalResult['products'] = $productNames;
$finalResult['estimated'] = $estimateds;
$finalResult['taken'] = $takens;

echo json_encode($finalResult);

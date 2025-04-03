<?php
include_once "../config/database.php";
include_once "functions.php";
session_start();

$getYears = json_decode(select_query($con, "DISTINCT YEAR(`createdOn`) as `years`", "product_master", "", "", "", ""));

$data = $finalResult = $years = $months = [];
$days = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

foreach ($getYears as $year) {
    $y = $year->years;
    $getMonths = json_decode(select_query($con, "DISTINCT MONTH(`createdOn`) as `months`", "product_master", "createdOn LIKE '" . $y . "-%'", "", "", ""));

    foreach ($getMonths as $month) {
        $m = $month->months < 10 ? '0' . $month->months : $month->months;
        $month = $month->months;

        $getMonthlySale = json_decode(select_query($con, "*", "product_master", "createdOn LIKE '%-" . $m . "-%'", "", "", ""));

        $total_sale = 0;
        foreach ($getMonthlySale as $sale) {
            $total_sale++;
        }

        $finalResult[$y][] = $total_sale;
    }
}
echo json_encode($finalResult);

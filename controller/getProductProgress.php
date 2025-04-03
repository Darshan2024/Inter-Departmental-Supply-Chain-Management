<?php
include_once "../config/database.php";
include_once "functions.php";
session_start();

$id = $productCode = "";

if (isset($_GET['id']))
    $id = mysqli_real_escape_string($con, htmlspecialchars(trim($_GET['id'])));

if (isset($_GET['productCode']))
    $productCode = mysqli_real_escape_string($con, htmlspecialchars(trim($_GET['productCode'])));

if ($id != '') {
    echo select_query($con, "c.*, p.eta", "chain_progress_master c JOIN product_master p ON c.productId=p.id", "c.enabled='1' AND c.productId=" . $id, "c.deptId ASC", "", "");
} else if ($productCode != '') {
    echo select_query($con, "c.*, p.eta", "chain_progress_master c JOIN product_master p ON c.productId=p.id", "c.enabled AND p.enabled='1' AND productCode='" . $productCode . "'", "deptId ASC", "", "");
} else {
    echo "nexe";
}

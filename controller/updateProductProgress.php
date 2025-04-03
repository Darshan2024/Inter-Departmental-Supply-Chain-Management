<?php
include_once "../config/database.php";
include_once "functions.php";
session_start();

$progress = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['progress'])));
$deptId = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['deptId'])));
$id = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['id'])));
$uid = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['uid'])));

if ($id != '' && $deptId != '' && $progress != '') {
    $progressTillNow = json_decode(select_query($con, "*", "chain_progress_master", "enabled='1' AND productId=" . $id . " AND deptId=" . $deptId, "", "", ""));

    if (count($progressTillNow) > 0) {
        echo update_query($con, "chain_progress_master", "progressInPercentage=" . $progress . ", updatedBy=" . $uid, "id=" . $progressTillNow[0]->id);
    } else {
        $result = insert_query($con, array('deptId', 'productId', 'progressInPercentage', 'createdBy', 'updatedBy'), array($deptId, $id, $progress, $uid, $uid), "chain_progress_master");

        if ($result)
            echo true;
        else false;
    }
} else echo json_encode(array("status" => false));

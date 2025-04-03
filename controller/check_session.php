<?php
session_start();

$_SESSION['logged_in'] == 1;

if (empty($_SESSION) || (empty($_SESSION['logged_in']) && !isset($_SESSION['logged_in'])))
    header("Location: login.php");


if ($_SESSION['category'] != 'superadmin')
    switch ($page) {
        case "Clients":
            if ($_SESSION['category'] != 'deptAdmin')
                header("Location: index.php");
            else if ($_SESSION['category'] == 'deptAdmin' && $_SESSION['department'] != 1)
                header("Location: index.php");

        case "Products":
            if ($_SESSION['category'] != 'deptAdmin')
                header("Location: index.php");
            else if ($_SESSION['category'] == 'deptAdmin' && $_SESSION['department'] != 1)
                header("Location: index.php");
    }

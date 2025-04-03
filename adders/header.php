<?php
header('Access-Control-Allow-Origin: *');
define('SITE_ROOT', realpath(dirname(__FILE__)));

$prepend = $page == 'Add Products' ? '.' : '';

include_once $prepend . "./controller/check_session.php";
include_once $prepend . "./config/database.php";
include_once $prepend . "./controller/functions.php";

$activeMaster = array('Products');

$user = json_decode(select_query($con, "*", "user_master", "id=" . $_SESSION['uid'], "", "", ""));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucwords($page); ?> | Supply Chain</title>

    <link rel="stylesheet" href="<?php echo $prepend; ?>./assets/css/bootstrap.css">
    <link rel="stylesheet" href="<?php echo $prepend; ?>./assets/css/sweetalert2.min.css" type="text/css">
    <link rel="stylesheet" href="<?php echo $prepend; ?>./assets/css/style.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous">

    <script src="<?php echo $prepend; ?>./assets/js/font-awesome.js" crossorigin="anonymous"></script>
    <script src="<?php echo $prepend; ?>./assets/js/sweetalert2.min.js"></script>

    <!-- Datatable CSS CDN -->
    <link rel="stylesheet" type="text/css" href="<?php echo $prepend; ?>./assets/plugin/datatable/datatables.min.css" />
    <link rel="stylesheet" href="<?php echo $prepend; ?>./assets/plugin/datatable/jquery.dataTables.min.css">
    <link rel="stylesheet" href="<?php echo $prepend; ?>./assets/plugin/datatable/buttons.dataTables.min.css">

    <?php if ($page == 'Charts' && $_SESSION['category'] == 'superadmin') { ?>
        <!-- Charts CDN -->
        <script src="https://code.highcharts.com/highcharts.js"></script>
    <?php } ?>

    <?php
    if ($page == "Sales") { ?>
        <style>
            .modal-dialog {
                max-width: 100%;
                margin: 1rem;
                top: 0;
                bottom: 0;
                left: 0;
                right: 0;
                display: flex;
            }

            .modal-content {
                height: auto;
                border-radius: 0;
            }

            li {
                cursor: pointer;
            }

            li:hover {
                background-color: rgb(197, 197, 197);
            }
        </style>
    <?php }
    ?>
</head>

<body id="body-pd">
    <header class="header" id="header">
        <div class="header_toggle"> <i class='bx bx-menu' id="header-toggle"></i> </div>

        <div class="border-right ml-auto mr-2">
            <h6 class="mr-2"><?php echo ucwords($user[0]->first_name . ' ' . $user[0]->last_name);
                                ?></h6>
        </div>
        <div class="dropdown">
            <a class="nav-link nav-user text-white" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                <span class="rounded-circle bg-dark p-3"><?php echo ucwords($user[0]->first_name[0] . ' ' . $user[0]->last_name[0]);
                                                            ?></span>
            </a>
            <ul class="dropdown-menu p-2" aria-labelledby="dropdownMenuLink">
                <li class="border-bottom"><a class="dropdown-item" href="#">Profile</a></li>
                <li><a class="dropdown-item" href="<?php echo $prepend; ?>./logout.php">Logout</a></li>
            </ul>
        </div>
    </header>
    <div class="l-navbar" id="nav-bar">
        <nav class="nav">
            <div>
                <a href="index.php" class="nav_logo">
                    <i class='fas fa-truck-loading nav_logo-icon'></i>
                    <span class="nav_logo-name">Supply Chain</span>
                </a>

                <div class="nav_list">
                    <a href="<?php echo $prepend; ?>./dashboard.php" class="nav_link <?php echo $page == 'Dashboard' ? 'active' : ''; ?>" data-toggle="tooltip" data-placement="top" title="Dashboard">
                        <i class='bx bx-grid-alt nav_icon'></i>
                        <span class="nav_name">Dashboard</span>
                    </a>

                    <?php if (in_array($_SESSION['department'], ["1"]) || $_SESSION['category'] == 'superadmin') { ?>
                        <a href="<?php echo $prepend; ?>./products.php" class="nav_link <?php echo $page == 'Products' ? 'active' : ''; ?>" data-toggle="tooltip" data-placement="top" title="Products">
                            <i class="fab fa-product-hunt"></i>
                            <span class="nav_name">Products</span>
                        </a>
                    <?php } ?>

                    <?php if (in_array($_SESSION['department'], ["1"]) || $_SESSION['category'] == 'superadmin') { ?>
                        <a href="<?php echo $prepend; ?>./clients.php" class="nav_link <?php echo $page == 'Clients' ? 'active' : ''; ?>" data-toggle="tooltip" data-placement="top" title="Clients">
                            <i class="fas fa-copyright"></i>
                            <span class="nav_name">Clients</span>
                        </a>
                    <?php } ?>

                    <?php if ($_SESSION['category'] == 'superadmin') { ?>
                        <a href="<?php echo $prepend; ?>./users.php" class="nav_link">
                            <i class='bx bx-user nav_icon'></i>
                            <span class="nav_name">Users</span>
                        </a>

                        <a href="<?php echo  $prepend; ?>./charts.php" class="nav_link">
                        <i class="fas fa-chart-bar nav_icon"></i>
                            <span class="nav_name">Charts</span>
                        </a>
                    <?php } ?>


                    <!--<a href="#" class="nav_link">
                        <i class='bx bx-bookmark nav_icon'></i>
                        <span class="nav_name">Bookmark</span>
                    </a>

                    <a href="#" class="nav_link">
                        <i class='bx bx-folder nav_icon'></i>
                        <span class="nav_name">Files</span>
                    </a>

                    <a href="#" class="nav_link">
                        <i class='bx bx-bar-chart-alt-2 nav_icon'></i>
                        <span class="nav_name">Stats</span>
                    </a> -->
                </div>
            </div> <a href="#" class="nav_link"> <i class='bx bx-log-out nav_icon'></i> <span class="nav_name">SignOut</span> </a>
        </nav>
    </div>
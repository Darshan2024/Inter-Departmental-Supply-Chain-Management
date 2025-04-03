<?php
$page = "Charts";
include_once "./adders/header.php";

$getProducts = json_decode(select_query($con, "*", "product_master", "enabled='1'", "", "", ""));
$departments = departments;

$product = '';
$activeProducts = [];
$productsWIP = $productsCompleted = $productsPending = 0;

foreach ($getProducts as $product) {
    $productProgress = array();
    if ($_SESSION['category'] != 'client') {
        $chainProgress = json_decode(select_query($con, "*", "chain_progress_master", "enabled='1' AND productId=" . $product->id, "", "", ""));

        if (count($chainProgress) > 1) {
            foreach ($chainProgress as $progress) {
                $productProgress[$progress->deptId - 1] = $progress->progressInPercentage;
            }
        }

        array_push($activeProducts, $product);

        if (count($productProgress) == 4 && $productProgress[3] == "100")
            $productsCompleted++;
        else if (count($productProgress) < 4 && count($productProgress) > 1)
            $productsWIP++;
        else
            $productsPending++;
    } else {
        $a = json_decode(select_query($con, "*", "client_product_map_master", "enabled='1' AND uid=" . $_SESSION['uid'] . " AND product LIKE '%" . $product->id . "%'", "", "", ""));
        if (count($a) == 1) {
            $chainProgress = json_decode(select_query($con, "*", "chain_progress_master", "enabled='1' AND productId=" . $product->id, "", "", ""));

            if (count($chainProgress) > 1) {
                foreach ($chainProgress as $progress) {
                    $productProgress[$progress->deptId - 1] = $progress->progressInPercentage;
                }
            }

            array_push($activeProducts, $product);

            if (count($productProgress) == 4 && $productProgress[3] == "100")
                $productsCompleted++;
            else if (count($productProgress) < 4 && count($productProgress) > 1)
                $productsWIP++;
            else
                $productsPending++;
        }
    }
}
?>
<div class="container-fluid pt-3">
    <div class="row">
        <div class="col-md-6">
            <div id="pieChart"></div>
        </div>
        <div class="col-md-6">
            <div id="saleChart"></div>
        </div>
        <div class="col">
            <div id="timeChart"></div>
        </div>
    </div>
</div>
<?php
include_once "./adders/footer.php";
?>

<script>
    // Pie Chart
    Highcharts.chart('pieChart', {
        chart: {
            type: 'pie'
        },
        title: {
            text: 'Task Status'
        },
        series: [{
            name: 'Tasks',
            data: [{
                    name: 'Active',
                    y: <?php echo count($activeProducts); ?>
                },
                {
                    name: 'WIP',
                    y: <?php echo $productsWIP; ?>
                },
                {
                    name: 'Completed',
                    y: <?php echo $productsCompleted; ?>
                },
                {
                    name: 'Pending',
                    y: <?php echo $productsPending; ?>
                }
            ]
        }]
    });

    // Monthly Sale Data
    $.ajax({
        type: "POST",
        url: './controller/getMonthlySaleData.php',
        success: function(result) {
            data = JSON.parse(result);

            if (result != '') {
                var d = [];

                $.each(data, function(k, v) {
                    d.push({
                        name: k,
                        data: v
                    });
                })
                Highcharts.chart('saleChart', {
                    chart: {
                        type: 'line'
                    },
                    title: {
                        text: 'Number of Products Sold per Month'
                    },
                    xAxis: {
                        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                    },
                    yAxis: {
                        title: {
                            text: 'Number of Products Sold'
                        }
                    },
                    series: d
                });
            }
        }
    })

    // Product Time Chart
    $.ajax({
        type: "POST",
        url: './controller/getProductCompletionData.php',
        success: function(result) {
            data = JSON.parse(result);

            if (result != '') {
                debugger
                Highcharts.chart('timeChart', {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: 'Product Time Comparison'
                    },
                    xAxis: {
                        categories: data.products,
                        crosshair: true
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Time (Days)'
                        }
                    },
                    plotOptions: {
                        column: {
                            pointPadding: 0,
                            borderWidth: 0,
                            pointPlacement: -0.2
                        }
                    },
                    series: [{
                        name: 'Estimated Time',
                        data: data.estimated
                    }, {
                        name: 'Actual Time Taken',
                        data: data.taken
                    }]
                });
            }
        }
    })
</script>
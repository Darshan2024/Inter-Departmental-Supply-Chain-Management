<?php
$page = "Dashboard";
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
    <div class="row mb-3">
        <h4 class="text-uppercase"><?php echo $page; ?></h4>
    </div>

    <div class="row mb-3">
        <div class="col">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title"><?php echo count($activeProducts); ?></h5>
                    <p class="card-text">Total Active Products</p>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title"><?php echo $productsWIP; ?></h5>
                    <p class="card-text">Total Work in Progress</p>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title"><?php echo $productsCompleted; ?></h5>
                    <p class="card-text">Total Completed</p>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title"><?php echo $productsPending; ?></h5>
                    <p class="card-text">Total Pending</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-6 text-center my-auto">
            <div class="card">
                <div class="card-body">
                    <label>Select product to check/update progress</label>
                    <select class="custom-select custom-select-sm" id="productDropdown" name="product">
                        <option selected disabled value="0">Select Product</option>
                        <?php
                        foreach ($activeProducts as $p) { ?>
                            <option value="<?php echo $p->id; ?>" <?php echo $product == $p->id ? "selected" : ""; ?>><?php echo $p->name; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
        <?php if ($_SESSION['category'] != "client" && $_SESSION['category'] != "superadmin") { ?>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body justify-content-center">
                        <div class="currentProgress d-none">
                            <div class="alert alert-success mx-auto w-50 text-center d-none">Progress updated!</div>
                            <div class="w-100 my-5 text-center">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="25">
                                    <label class="form-check-label" for="inlineCheckbox1">Phase 1</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="25">
                                    <label class="form-check-label" for="inlineCheckbox2">Phase 2</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="inlineCheckbox3" value="25">
                                    <label class="form-check-label" for="inlineCheckbox1">Phase 3</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="inlineCheckbox4" value="25">
                                    <label class="form-check-label" for="inlineCheckbox2">Phase 4</label>
                                </div>
                                <div class="price alert alert-primary text-center w-25 mx-auto">0%</div>
                                <input class="btn btn-sm btn-outline-success my-2" id="updateProgress" type="button" value="Update progress" class="btn mx-auto">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row justify-content-center">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Search by Reference Id</label>
                                    <input type="text" class="form-control form-control-sm text-center" id="reference_id">
                                </div>
                            </div>
                            <input class="btn btn-sm btn-outline-success my-4" id="getById" type="button" value="Get Progress" class="btn mx-auto">
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <div class="row mt-5 align-items-center position-relative">
        <div class="progress-container w-100" style="margin-top: 35px;">
            <div class="progress-bar-truck">
                <div class="js-completed-bar completed-bar" data-complete="0">
                    <hr class="completed-bar__dashed">
                    <i class="fas fa-truck-moving completed-bar__truck"></i>
                    <i class="fas fa-truck-loading completed-bar__truck d-none"></i>
                </div>
            </div>
        </div>

        <div class="col pl-0">
            <label class="font-weight-bold">Marketing</label>
            <div class="container pl-0" data-toggle="tooltip" data-placement="top" title="Marketing">
                <div class="progress float-left" data-percentage="0" id="dept1">
                    <span class="progress-left">
                        <span class="progress-bar"></span>
                    </span>
                    <span class="progress-right">
                        <span class="progress-bar"></span>
                    </span>
                    <div class="progress-value">
                        <div>0%</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col pl-0">
            <label class="font-weight-bold">Storage</label>
            <div class="container pl-0" data-toggle="tooltip" data-placement="top" title="Storage">
                <div class="progress float-left" data-percentage="0" id="dept2">
                    <span class="progress-left">
                        <span class="progress-bar"></span>
                    </span>
                    <span class="progress-right">
                        <span class="progress-bar"></span>
                    </span>
                    <div class="progress-value">
                        <div>0%</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col pl-0">
            <label class="font-weight-bold">Design</label>
            <div class="container pl-0" data-toggle="tooltip" data-placement="top" title="Design">
                <div class="progress float-left" data-percentage="0" id="dept3">
                    <span class="progress-left">
                        <span class="progress-bar"></span>
                    </span>
                    <span class="progress-right">
                        <span class="progress-bar"></span>
                    </span>
                    <div class="progress-value">
                        <div>0%</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col pl-0">
            <label class="font-weight-bold">Manufacturing</label>
            <div class="container pl-0">
                <div class="progress float-left" data-percentage="0" id="dept4">
                    <span class="progress-left">
                        <span class="progress-bar"></span>
                    </span>
                    <span class="progress-right">
                        <span class="progress-bar"></span>
                    </span>
                    <div class="progress-value">
                        <div>0%</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col pl-0">
            <label class="font-weight-bold">Quality Assurance</label>
            <div class="container pl-0" data-toggle="tooltip" data-placement="top" title="Quality Assurance">
                <div class="progress float-left" data-percentage="0" id="dept4">
                    <span class="progress-left">
                        <span class="progress-bar"></span>
                    </span>
                    <span class="progress-right">
                        <span class="progress-bar"></span>
                    </span>
                    <div class="progress-value">
                        <div>0%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center mt-5 time-estimation d-none">
        <div class="col-md-4">
            <h6 class="text-center">Expected Time In Days: <span id="etaTime"></span></h6>
            <div class="alert alert-primary">
                <h4>Estimated Time Left In Days: <span id="etaRemaining"></span></h4>
            </div>
        </div>
    </div>
</div>
<?php
include_once "./adders/footer.php";
?>

<script>
    var currentUserDept = <?php echo $_SESSION['department'] != '' ? $_SESSION['department'] : "0"; ?>;
    $(document).ready(function() {
        $("#productDropdown").on("change", function() {
            var selectedProduct = $("#productDropdown").find(":selected").val();
            var deptProgress = new Array(4);
            $(".currentProgress").removeClass("d-none");
            $(".time-estimation").removeClass("d-none");

            for (let i = 0; i <= deptProgress.length; i++)
                updateProgress(0, i);

            getProductAndUpdate(selectedProduct);
        });

        $("input[type='checkbox']").click(function() {
            let cb = $("input[type='checkbox']:checked");

            let prog = 0;
            cb.each(() => {
                prog += Number($(this).val());
            });

            $('div.price').text(prog + "%");
        });

        $("#updateProgress").click(function() {
            selectedProduct = $("#productDropdown").find(":selected").val();
            let cb = $("input[type='checkbox']:checked");

            let v = 0;
            cb.each(() => {});

            for (var i = 0; i < cb.length; i++) {
                let z = cb[i].value;
                v += Number(z);
            }

            $.ajax({
                type: "POST",
                url: "./controller/updateProductProgress.php?dept=<?php echo $_SESSION['department']; ?>&id=" + selectedProduct,
                data: {
                    deptId: currentUserDept,
                    id: selectedProduct,
                    progress: v,
                    uid: <?php echo $_SESSION['uid']; ?>
                },
                success: function(data) {
                    if (data == 1) {
                        $(".alert-success").toggleClass("d-none");
                        updateProgress(v, currentUserDept - 1);

                        getProductAndUpdate(selectedProduct);
                    }

                    setTimeout(() => {
                        $(".alert-success").toggleClass("d-none");
                    }, 3000);
                }
            })
        });

        $("#getById").on("click", function() {
            debugger
            var selectedProduct = $("#reference_id").val();
            var deptProgress = new Array(4);
            $(".currentProgress").removeClass("d-none");

            for (let i = 0; i <= deptProgress.length; i++)
                updateProgress(0, i);

            $.ajax({
                type: "GET",
                url: "./controller/getProductProgress.php?productCode=" + selectedProduct,
                success: function(data) {
                    var d = null;

                    if (data != '' && data != undefined)
                        d = JSON.parse(data);

                    if (d != null) {
                        d.forEach(record => {
                            deptProgress[record.deptId - 1] = record.progressInPercentage;
                        });

                        let val = 0;
                        for (let i = 0; i <= deptProgress.length; i++) {
                            v = 0;

                            if (deptProgress[i] != undefined) {
                                v = deptProgress[i];
                                val += Number(v);

                            }

                            updateProgress(v, i);
                        }

                        if (currentUserDept > 1) {
                            if ((deptProgress[currentUserDept - 1] == undefined || deptProgress[currentUserDept - 1] == 0) && deptProgress[currentUserDept - 2] < 100)
                                $(".currentProgress").addClass("d-none");

                            else if (deptProgress[currentUserDept - 2] == undefined || deptProgress[currentUserDept - 2] < 100)
                                $(".currentProgress").addClass("d-none");
                        } else if (deptProgress[currentUserDept - 1] == 100)
                            $(".currentProgress").addClass("d-none");

                        const progress = document.querySelector(".js-completed-bar");

                        if (progress) {
                            width = val < 390 ? ((val / 5) + 5) : (val / 5);
                            progress.style.width = width + "%";
                            progress.style.opacity = 1;

                            if (width == 100)
                                setTimeout(() => {
                                    $('.fa-truck-moving, .fa-truck-loading').toggleClass('d-none');
                                }, 2000);
                        }
                    }
                }
            });
        });
    });

    function updateProgress(percentage, i) {
        let progress = percentage / 100;

        // $("#dept" + i).addClass('progress-percentage-' + progress);
        $("#dept" + (i + 1)).attr('data-percentage', percentage);
        $("#dept" + (i + 1) + " .progress-value div").text(percentage + "%");
    }

    function getProductAndUpdate(selectedProduct) {
        var deptProgress = new Array(4);

        $.ajax({
            type: "GET",
            url: "./controller/getProductProgress.php?id=" + selectedProduct,
            success: function(data) {
                var d = null;
                let val = 0;
                $("input[type='checkbox']").prop('checked', false);
                $('div.price').text("0%");

                if (data != '' && data != undefined)
                    d = JSON.parse(data);

                if (d != null && d.length > 0) {
                    var eta = d[0].eta
                    var timeForEachDept = Math.round((eta * 24) / 5);

                    $("#etaTime").text(eta);

                    d.forEach(record => {
                        deptProgress[record.deptId - 1] = record.progressInPercentage;
                    });

                    for (let i = 0; i <= deptProgress.length; i++) {
                        v = 0;

                        if (deptProgress[i] != undefined) {
                            v = deptProgress[i];
                            val += Number(v);

                        }

                        updateProgress(v, i);
                    }

                    if (currentUserDept > 1) {
                        let a = deptProgress[currentUserDept - 1] / 25;

                        for (let index = 0; index < a; index++) {
                            $("#inlineCheckbox" + (index + 1)).prop('checked', true);
                        }

                        $('div.price').text(deptProgress[currentUserDept - 1] + "%");

                        if ((deptProgress[currentUserDept - 1] == undefined || deptProgress[currentUserDept - 1] == 0) && deptProgress[currentUserDept - 2] < 100)
                            $(".currentProgress").addClass("d-none");

                        else if (deptProgress[currentUserDept - 2] == undefined || deptProgress[currentUserDept - 2] < 100)
                            $(".currentProgress").addClass("d-none");
                    } else if (deptProgress[currentUserDept - 1] == 100)
                        $(".currentProgress").addClass("d-none");


                }

                const progress = document.querySelector(".js-completed-bar");

                if (progress) {
                    width = val < 490 ? ((val / 5) + 5) : (val / 5);
                    progress.style.width = width + "%";
                    progress.style.opacity = 1;

                    timeRemaining = (width / 100) * eta;
                    $("#etaRemaining").text(timeRemaining);

                    if (width == 100)
                        setTimeout(() => {
                            $('.fa-truck-moving, .fa-truck-loading').toggleClass('d-none');
                        }, 2000);
                }
            }
        });
    }
</script>
<?php
$page = "Products";
include_once "./adders/header.php";

$getProducts = json_decode(select_query($con, "*", "product_master", "enabled='1'", "", "", ""));

$pnameErr = $etaErr = '';

$toggleModal = false;

if (isset($_POST['prodSub'])) {
    $pname = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['product_name'])));
    $eta = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['product_eta'])));

    if (!preg_match('/^[a-zA-Z]+[a-zA-Z0-9 ]+$/', $pname))
        $pnameErr = "Only alphabets, numbers and spaces are allowed";

    if ($pname == '')
        $pnameErr = "Product name is required";

    if ($eta == '' || $eta < 1)
        $etaErr = "Product ETA is required and must be more than 0";

    if ($pnameErr == '' && $etaErr == '') {
        if (isset($_GET['pref']) && !isset($_GET['act'])) {
            $requestUpdateId = update_query($con, "product_master", "name='" . $pname . "', eta='" . $eta . "'", "id=" . decryption($_GET['pref']));

            if ($requestUpdateId != '') {
                echo '<script>swal({title: "Product has been updated successfully",type: "success",button: "Ok"}).then(function() {window.location.href = "products.php";});</script>';
            } else {
                echo '<script>swal({title: "Something went wrong",type: "error",button: "Ok"});</script>';
            }
        } else {
            $referenceCode = generate_code($con);
            $addProd = json_decode(insert_query($con, array('name', 'productCode', 'eta', 'createdBy'), array($pname, $referenceCode, $eta, $_SESSION['uid']), "product_master"));

            if ($addProd != '') {
                echo '<script>swal({title: "Product added successfully",type: "success",button: "Ok"}).then(function() {window.location.href = "products.php";});</script>';
            } else {
                echo '<script>swal({title: "Something went wrong",type: "warning",button: "Ok"});</script>';
            }
        }
    }
}

if (isset($_GET['pref']) && !isset($_GET['act'])) {
    $toggleModal = true;
    $productToEdit = json_decode(select_query($con, "*", "product_master", "id=" . decryption($_GET['pref']), "", "", ""));

    if (count($productToEdit) > 0) {
        $pname = $productToEdit[0]->name;
        $eta = $productToEdit[0]->eta;
    }
}

if (isset($_GET['pref']) && isset($_GET['act']) && $_GET['act'] === "at") {
    $isDeleted = json_decode(update_query($con, "product_master", "enabled='0', updatedBy=" . $_SESSION['uid'], "id=" . decryption($_GET['pref'])));

    if ($isDeleted)
        echo '<script>swal({title: "Product deleted successfully",type: "success",button: "Ok"}).then(function() {window.location.href = "products.php";});</script>';
    else
        echo '<script>swal({title: "Something went wrong",type: "error",button: "Ok"});</script>';
}

function generate_code($con)
{
    $data = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 0, 'A', 'B', 'C', 'D', 'E', 'F');
    $res = '';
    $isUnique = false;

    while (!$isUnique) {
        for ($i = 0; $i < 7; $i++) {
            $rand = rand(0, (count($data) - 1));
            $res .= $data[$rand];
        }

        $code = json_decode(select_query($con, "productCode", "product_master", "productCode='" . $res . "'", "", "", ""));

        if (count($code) == 0)
            $isUnique = true;
    }

    return $res;
}

?>
<div class="container-fluid pt-3">
    <div class="row my-3 ml-1">
        <button class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#addProductModalCenter">Add New Product</button>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="addProductModalCenter" tabindex="-1" role="dialog" aria-labelledby="addProductModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header primary-head">
                    <h5 class="modal-title" id="addProductModalLongTitle">Add New Product</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Product Name</label>
                                    <input type="text" name="product_name" class="form-control form-control-sm" <?php echo isset($pname) && $pname != '' ? 'value="' . $pname . '"' : ''; ?>>
                                    <?php echo $pnameErr != '' ? '<span class="text-danger">' . $pnameErr . '</span>' : ''; ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Estimated Requried Time In Days</label>
                                    <input type="number" name="product_eta" class="form-control form-control-sm" <?php echo isset($eta) && $eta != '' ? 'value="' . $eta . '"' : ''; ?> min="1">
                                    <?php echo $etaErr != '' ? '<span class="text-danger">' . $etaErr . '</span>' : ''; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="prodSub" class="btn btn-outline-primary btn-sm">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <table id="productsTable" class="display nowrap w-100 table-responsive-sm" style="width:100%;">
            <thead class="thead">
                <th>#</th>
                <th>Name</th>
                <th>Product Code</th>
                <th>Product ETA In Days</th>
                <th>Added On</th>
                <th>Action</th>
            </thead>

            <tbody>
                <?php
                if (!empty($getProducts)) {
                    $cnt = 1;
                    foreach ($getProducts as $product) {
                ?>
                        <tr>
                            <td><?php echo $cnt++; ?></td>
                            <td><?php echo $product->name; ?></td>
                            <td><?php echo $product->productCode; ?></td>
                            <td><?php echo $product->eta; ?></td>
                            <td><?php echo date('d M Y', strtotime($product->createdOn)); ?></td>
                            <td>
                                <a class="mx-1" href="products.php?pref=<?php echo encryption($product->id); ?>"><i class="fas fa-pen"></i></a>
                                <a class="mx-1" href="products.php?act=at&pref=<?php echo encryption($product->id); ?>"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                <?php }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<?php
include_once "./adders/footer.php";
?>

<script>
    $('#productsTable').DataTable({
        dom: 'Bfrtip',
        buttons: ['excel', 'pdf', 'print']
    });

    <?php if ($pnameErr != '' || $toggleModal) { ?>
        $('#addProductModalCenter').modal('toggle');
    <?php } ?>
</script>
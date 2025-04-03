<?php
$page = "Clients";
include_once "./adders/header.php";

if ($_SESSION['category'] != 'superadmin')
    $getClients = json_decode(select_query($con, "*", "user_master", "enabled='1' AND category='client' AND createdBy=" . $_SESSION['uid'], "", "", ""));
else
    $getClients = json_decode(select_query($con, "*", "user_master", "enabled='1' AND category='client'", "", "", ""));
$getProducts = json_decode(select_query($con, "*", "product_master", "enabled='1' AND createdBy=" . $_SESSION['uid'], "", "", ""));

$fname = $fnameErr = $lname = $lnameErr = $phone = $phoneErr = $email = $emailErr = $password = $passwordErr = $product = $productErr = '';

$toggleModal = false;

if (isset($_POST['prodSub'])) {
    $fname = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['first_name'])));
    $lname = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['last_name'])));
    $phone = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['phone'])));
    $email = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['email'])));
    $password = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['password'])));
    $product = $_POST['product'];

    if ($fname == '')
        $fnameErr = "First name is required";

    if ($lname == '')
        $lnameErr = "Last name is required";

    if ($phone == '')
        $phoneErr = "Phone number is required";

    if ($email == '')
        $emailErr = "Email address is required";

    if ($password == '')
        $passwordErr = "Password is required";

    if (count($product) == 0)
        $productErr = "Product is required";

    if ($fnameErr == '' && $lnameErr == '' && $phoneErr == '' && $emailErr == '' && $passwordErr == '' && $productErr == '') {
        $selectedProducts = implode(",", $product);
        if (isset($_GET['pref']) && isset($_GET['p']) && !isset($_GET['act'])) {
            $toggleModal = false;
            $requestUpdateId = update_query($con, "user_master", "first_name='" . $fname . "', last_name='" . $lname . "', email_id='" . $email . "', phone_number='" . $phone . "', password='" . $password . "'", "id=" . decryption($_GET['pref']));

            if ($requestUpdateId != '') {
                $mapUpdateId = update_query($con, "client_product_map_master", "product='" . $selectedProducts . "'", "id=" . decryption($_GET['p']));

                if ($mapUpdateId != '')
                    echo '<script>swal({title: "Client has been updated successfully",type: "success",button: "Ok"}).then(function() {window.location.href = "clients.php";});</script>';
                else
                    echo '<script>swal({title: "Something went wrong while updating mapping",type: "error",button: "Ok"});</script>';
            } else {
                echo '<script>swal({title: "Something went wrong",type: "error",button: "Ok"});</script>';
            }
        } else {
            $addClient = json_decode(insert_query($con, array('category', 'first_name', 'last_name', 'phone_number', 'email_id', 'password', 'createdBy'), array('client', $fname, $lname, $phone, $email, $password, $_SESSION['uid']), "user_master"));

            if ($addClient != '') {
                $mapClientProduct = json_decode(insert_query($con, array('uid', 'product'), array($addClient, $selectedProducts), "client_product_map_master"));
                if ($mapClientProduct != '')
                    echo '<script>swal({title: "Client added successfully",type: "success",button: "Ok"}).then(function() {window.location.href = "clients.php";});</script>';
                else
                    echo '<script>swal({title: "Something went wrong while mapping product",type: "warning",button: "Ok"});</script>';
            } else {
                echo '<script>swal({title: "Something went wrong",type: "warning",button: "Ok"});</script>';
            }
        }
    }
}

if (isset($_GET['pref']) && isset($_GET['p']) && !isset($_GET['act'])) {
    $toggleModal = true;
    $clientToEdit = json_decode(select_query($con, "*", "user_master", "id=" . decryption($_GET['pref']), "", "", ""));
    $clientProductMapToEdit = json_decode(select_query($con, "*", "client_product_map_master", "id IN (" . decryption($_GET['p']) . ")", "", "", ""));

    if (count($clientToEdit) > 0 && count($clientProductMapToEdit) > 0) {
        $fname = $clientToEdit[0]->first_name;
        $lname = $clientToEdit[0]->last_name;
        $phone = $clientToEdit[0]->phone_number;
        $email = $clientToEdit[0]->email_id;
        $password = $clientToEdit[0]->password;
        $product = $clientProductMapToEdit[0]->product;
    }
}

if (isset($_GET['pref']) && isset($_GET['p']) && isset($_GET['act']) && $_GET['act'] === "at") {
    $isDeleted = json_decode(update_query($con, "user_master", "enabled='0', updatedBy=" . $_SESSION['uid'], "id=" . decryption($_GET['pref'])));

    if ($isDeleted) {
        $isMappingDeleted = json_decode(update_query($con, "client_product_map_master", "enabled='0', updatedBy=" . $_SESSION['uid'], "id=" . decryption($_GET['p'])));

        if ($isMappingDeleted)
            echo '<script>swal({title: "Client deleted successfully",type: "success",button: "Ok"}).then(function() {window.location.href = "clients.php";});</script>';
        else
            echo '<script>swal({title: "Something went wrong while deleting mapping",type: "error",button: "Ok"});</script>';
    } else
        echo '<script>swal({title: "Something went wrong",type: "error",button: "Ok"});</script>';
}
?>
<div class="container-fluid pt-3">
    <div class="row my-3 ml-1">
        <button class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#addclientModalCenter">Add New Client</button>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="addclientModalCenter" tabindex="-1" role="dialog" aria-labelledby="addclientModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header primary-head">
                    <h5 class="modal-title" id="addclientModalLongTitle">Add New Client</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">First Name</label>
                                    <input type="text" name="first_name" class="form-control form-control-sm" <?php echo isset($fname) && $fname != '' ? 'value="' . $fname . '"' : ''; ?> <?php echo $fnameErr != '' ? '<span class="text-danger">' . $fnameErr . '</span>' : ''; ?>>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Last Name</label>
                                    <input type="text" name="last_name" class="form-control form-control-sm" <?php echo isset($lname) && $lname != '' ? 'value="' . $lname . '"' : ''; ?> <?php echo $lnameErr != '' ? '<span class="text-danger">' . $lnameErr . '</span>' : ''; ?>>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Phone Number</label>
                                    <input type="text" name="phone" class="form-control form-control-sm" <?php echo isset($phone) && $phone != '' ? 'value="' . $phone . '"' : ''; ?> <?php echo $phoneErr != '' ? '<span class="text-danger">' . $phoneErr . '</span>' : ''; ?>>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Email/Username</label>
                                    <input type="text" name="email" class="form-control form-control-sm" <?php echo isset($email) && $email != '' ? 'value="' . $email . '"' : ''; ?>>
                                    <?php echo $emailErr != '' ? '<span class="text-danger">' . $emailErr . '</span>' : ''; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Password</label>
                                    <input type="text" name="password" class="form-control form-control-sm" <?php echo isset($password) && $password != '' ? 'value="' . $password . '"' : ''; ?>>
                                    <?php echo $passwordErr != '' ? '<span class="text-danger">' . $passwordErr . '</span>' : ''; ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Product</label>
                                    <select class="custom-select custom-select-sm" id="semesterDropdown" name="product[]" multiple>
                                        <option <?php isset($product) && $product == '' ? '' : 'selected' ?> disabled>Select Product</option>
                                        <?php
                                        foreach ($getProducts as $p) { ?>
                                            <option value="<?php echo $p->id; ?>" <?php echo str_contains($product, $p->id) ? "selected" : ""; ?>><?php echo $p->name; ?></option>
                                        <?php } ?>
                                    </select>
                                    <?php echo $productErr != '' ? '<span class="text-danger">' . $productErr . '</span>' : ''; ?>
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
        <table id="clientsTable" class="display nowrap w-100 table-responsive-sm " style="width:100%;">
            <thead class="thead">
                <th>#</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Product</th>
                <th>Action</th>
            </thead>

            <tbody>
                <?php
                if (!empty($getClients)) {
                    $cnt = 1;
                    foreach ($getClients as $client) {
                        $clientProductMapping = json_decode(select_query($con, "*", "client_product_map_master", "uid=" . $client->id, "", "", ""));
                        $getProduct = json_decode(select_query($con, "*", "product_master", "id IN (" . $clientProductMapping[0]->product . ")", "", "", ""));
                        $productNames = [];

                        foreach ($getProduct as $p) {
                            array_push($productNames, $p->name);
                        }
                ?>
                        <tr>
                            <td><?php echo $cnt++; ?></td>
                            <td><?php echo $client->first_name . " " . $client->last_name;; ?></td>
                            <td><?php echo $client->phone_number; ?></td>
                            <td><?php echo $client->email_id; ?></td>
                            <td><?php echo implode(",", $productNames); ?></td>
                            <td>
                                <a class="mx-1" href="clients.php?pref=<?php echo encryption($client->id); ?>&p=<?php echo encryption($clientProductMapping[0]->id); ?>"><i class="fas fa-pen"></i></a>
                                <a class="mx-1" href="clients.php?act=at&pref=<?php echo encryption($client->id); ?>&p=<?php echo encryption($clientProductMapping[0]->id); ?>"><i class="fas fa-trash"></i></a>
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
    $('table').DataTable({
        dom: 'Bfrtip',
        buttons: ['excel', 'pdf', 'print']
    });

    <?php if (($fnameErr != '' && $lnameErr != '' && $phoneErr != '' && $emailErr != '' && $passwordErr != '' && $productErr != '') || $toggleModal) { ?>
        $('#addclientModalCenter').modal('toggle');
    <?php } ?>
</script>
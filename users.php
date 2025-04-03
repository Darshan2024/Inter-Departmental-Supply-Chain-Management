<?php
$page = "Users";
include_once "./adders/header.php";

$getUsers = json_decode(select_query($con, "*", "user_master", " category = 'deptAdmin' and createdBy=" . $_SESSION['uid'], "", "", ""));
$departments = departments;

$fname = $fnameErr = $lname = $lnameErr = $phone = $phoneErr = $email = $emailErr = $password = $passwordErr = $department = $departmentErr = '';

if (isset($_POST['userSub'])) {
    $fname = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['first_name'])));
    $lname = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['last_name'])));
    $phone = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['phone'])));
    $email = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['email'])));
    $password = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['password'])));
    $department = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['department'])));

    if ($fname == '')
        $fnameErr = "First name is required";
    if ($lname == '')
        $lnameErr = "Last name is required";
    if ($phone == '')
        $phoneErr = "Phone number is required";
    if ($email == '')
        $emailErr = "Email/Username is required";
    if ($password == '')
        $passwordErr = "Password is required";
    if ($department == '')
        $departmentErr = "Department is required";

    if ($fnameErr == '' && $lnameErr == '' && $phoneErr == '' && $emailErr == '' && $passwordErr == '' && $departmentErr == '') {
        $addUser = json_decode(insert_query($con, array('category', 'first_name', 'last_name', 'phone_number', 'email_id', 'password', 'createdBy', 'updatedBy'), array('deptAdmin', $fname, $lname, $phone, $email, $password, $_SESSION['uid'], $_SESSION['uid']), "user_master"));

        if ($addUser != '') {
            $mapUserDept = json_decode(insert_query($con, array('uid', 'department'), array($addUser, $department), "user_department_map_master"));
            if ($mapUserDept != '')
                echo '<script>swal({title: "User added successfully",type: "success",button: "Ok"}).then(function() {window.location.href = "users.php";});</script>';
            else
                echo '<script>swal({title: "Something went wrong while mapping department",type: "warning",button: "Ok"});</script>';
        } else {
            echo '<script>swal({title: "Something went wrong",type: "warning",button: "Ok"});</script>';
        }
    }
}
?>
<div class="container-fluid pt-3">
    <div class="row my-3 ml-1">
        <button class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#addUserModalCenter">Add New User</button>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="addUserModalCenter" tabindex="-1" role="dialog" aria-labelledby="addUserModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header primary-head">
                    <h5 class="modal-title" id="addProductModalLongTitle">Add New User</h5>
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
                                    <label for="">Department</label>
                                    <select class="custom-select custom-select-sm" name="department">
                                        <option selected disabled>Select Department</option>
                                        <?php
                                        $i = 1;
                                        foreach ($departments as $d) { ?>
                                            <option value="<?php echo $i++; ?>" <?php echo $department == ($i - 1) ? "selected" : ""; ?>><?php echo $d; ?></option>
                                        <?php } ?>
                                    </select>
                                    <?php echo $departmentErr != '' ? '<span class="text-danger">' . $departmentErr . '</span>' : ''; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="userSub" class="btn btn-outline-primary btn-sm">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <table id="usersTable" class="display nowrap w-100 table-responsive-sm" style="width:100%;">
            <thead class="thead">
                <th>#</th>
                <th>Name</th>
                <th>Department</th>
                <th>Username</th>
                <th>Added On</th>
                <th>Action</th>
            </thead>

            <tbody>
                <?php
                if (!empty($getUsers)) {
                    $cnt = 1;
                    foreach ($getUsers as $user) {
                        $getDepartment = json_decode(select_query($con, "*", "user_department_map_master", "uid=" . $user->id, "", "", ""));
                ?>
                        <tr>
                            <td><?php echo $cnt++; ?></td>
                            <td><?php echo $user->first_name . " " . $user->last_name; ?></td>
                            <td><?php echo $departments[$getDepartment[0]->department-1]; ?></td>
                            <td><?php echo $user->email_id; ?></td>
                            <td><?php echo date('d M Y', strtotime($user->createdOn)); ?></td>
                            <td></td>
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
    $('#usersTable').DataTable({
        dom: 'Bfrtip',
        buttons: ['excel', 'pdf', 'print']
    });

    <?php if ($fnameErr != '' && $lnameErr != '' && $phoneErr != '' && $emailErr != '' && $passwordErr != '' && $departmentErr != '') { ?>
        $('#addUserModalCenter').modal('toggle');
    <?php } ?>
</script>
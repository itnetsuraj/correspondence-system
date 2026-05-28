<?php
include_once '../config/session.php';
include_once '../config/security_headers.php';
include_once __DIR__.'/config/auth_check.php';

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit;
}

include '../config/db.php';
include '../header.php';

/* Admin only */

if($_SESSION['admin_role']!="admin"){
    header("Location: ../dashboard.php");
    exit;
}

/* CSRF check */

if(isset($_POST['save'])){

    if(
        !isset($_POST['csrf_token']) ||
        !hash_equals(
            $_SESSION['csrf_token'],
            $_POST['csrf_token']
        )
    ){

        die("Invalid CSRF Token");
    }

    $username=trim($_POST['username']);

    $password=password_hash(
        $_POST['password'],
        PASSWORD_DEFAULT
    );

    $role=$_POST['admin_role'];

    $establishment=$_POST['establishment'];



    /* Check duplicate username */

    $check=$conn->prepare("
        SELECT id
        FROM users
        WHERE username=?
    ");

    $check->bind_param(
        "s",
        $username
    );

    $check->execute();

    $result=$check->get_result();


    if($result->num_rows>0){

        echo "
        <script>
        alert('Username already exists');
        </script>
        ";

    }
    else{

        /* Secure insert */

        $stmt=$conn->prepare("
        INSERT INTO users
        (
        username,
        password,
        admin_role,
        establishment
        )

        VALUES
        (
        ?,
        ?,
        ?,
        ?
        )
        ");

        $stmt->bind_param(
            "ssss",
            $username,
            $password,
            $role,
            $establishment
        );

        if($stmt->execute()){

            echo "
            <script>
            alert('User Added Successfully');
            location='add_user.php';
            </script>
            ";

        }
        else{

            die(
                "Database Error : ".
                $conn->error
            );

        }

    }

}
?>

<title>Office Inward Outward Management</title>

<div class="center-page">

<div class="box">

<h1 class="form-title">

Add New User

</h1>

<form method="post" class="modern-form">

<input
type="hidden"
name="csrf_token"
value="<?= $_SESSION['csrf_token'] ?>"
>


<div class="input-group">

<label>

Username

</label>

<input
name="username"
required>

</div>


<div class="input-group">

<label>

Password

</label>

<input
type="password"
name="password"
required>

</div>


<div class="input-group">

<label>

Role

</label>

<select
name="admin_role"
required>

<option value="user">

User

</option>

<option value="admin">

Admin

</option>

</select>

</div>


<div class="input-group">

<label>

Establishment

</label>

<select
name="establishment"
required
style="
padding:10px;
width:100%;
border-radius:8px;
">

<option value="">

Select Establishment

</option>

<?php

$establishments=$conn->query("
SELECT *
FROM establishments
ORDER BY establishment_name ASC
");

while(
$row=
$establishments->fetch_assoc()
){

?>

<option
value="<?= htmlspecialchars($row['establishment_name']) ?>">

<?= htmlspecialchars($row['establishment_name']) ?>

</option>

<?php } ?>

</select>

</div>


<button
class="save-btn"
name="save">

Create User

</button>

</form>

</div>

</div>

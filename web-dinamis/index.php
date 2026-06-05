<?php
session_start();
include "koneksi.php";

$error = "";

if(isset($_POST['login'])){

    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users
            WHERE username='$username'
            AND password='$password'";

    $result = $conn->query($sql);

    if($result->num_rows > 0){

        $_SESSION['username'] = $username;

        header("Location: dashboard.php");
        exit();

    } else {

        $error = "Username atau Password Salah!";

    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login UAS</title>
</head>
<body>

<h2>Login UAS Cloud Computing</h2>

<?php if($error != ""){ ?>
<p style="color:red;">
    <?php echo $error; ?>
</p>
<?php } ?>

<form method="POST">

    <label>Username</label>
    <br>
    <input type="text" name="username" required>

    <br><br>

    <label>Password</label>
    <br>
    <input type="password" name="password" required>

    <br><br>

    <button type="submit" name="login">
        Login
    </button>

</form>

</body>
</html>
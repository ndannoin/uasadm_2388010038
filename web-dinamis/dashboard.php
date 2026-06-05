<?php
session_start();

if(!isset($_SESSION['username'])){
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>

<h1>Dashboard UAS Cloud Computing</h1>

<p>
Selamat Datang,
<b><?php echo $_SESSION['username']; ?></b>
</p>

<a href="logout.php">
Logout
</a>

</body>
</html>
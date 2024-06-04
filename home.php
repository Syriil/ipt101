<?php
session_start();
require_once("db.php");



if (!isset($_SESSION['username'])) {
    header('Location: loginform.php');
    exit;
}


$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container vh-100 d-flex justify-content-center align-items-center">
        <div class="col-md-6 shadow-lg p-5 bg-white rounded text-center">
            <h1 class="mb-4">Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
            <p class="lead mb-4">This is your home page.</p>
            <!-- Logout Button -->
            <a href="logout.php" class="btn btn-info btn-lg">Singout</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

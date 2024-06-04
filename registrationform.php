<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container" style="background-color: yellow;">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <br>
                <?php if(isset($_GET['error'])) { ?>
                    <div class="alert alert-danger text-center">
                        <?php echo $_GET['error']; ?>
                    </div>
                <?php } ?>
                <form class="shadow-lg p-3 mb-5 rounded" action="register.php" method="POST" id="registerForm">
                    <h2 class="text-center">Register</h2>
                    <div class="mb-3">
                        <label for="First_name" class="form-label">Firstname:</label>
                        <input type="text" name="First_name" class="form-control" placeholder="Firstname" id="First_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="Middle_name" class="form-label">Middlename:</label>
                        <input type="text" name="Middle_name" class="form-control" placeholder="Middlename" id="Middle_name">
                    </div>
                    <div class="mb-3">
                        <label for="Lastname" class="form-label">Lastname:</label>
                        <input type="text" name="Lastname" class="form-control" placeholder="Lastname" id="Lastname" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username:</label>
                        <input type="text" name="username" class="form-control" placeholder="Username" id="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" name="Email" class="form-control" placeholder="Email" id="Email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password:</label>
                        <input type="password" name="password" class="form-control" placeholder="Password" id="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password:</label>
                        <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" id="confirm_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="Status" class="form-label">Status:</label>
                        <select name="Status" class="form-select" id="Status" required>
                            <option value="">Select Status</option>
                            <option value="Single">Single</option>
                            <option value="In a Relationship">In a Relationship</option>
                            <option value="Married">Married</option>
                            <option value="Divorced">Divorced</option>
                        </select>
                    </div>
                    <button type="submit" name="register_btn" id="register_btn" class="btn btn-info">Register</button>
                    
                    <div class="row">
                        <div class="col-md-6 offset-md-3">
                            <div class="text-center">
                                <a href="loginform.php">Click to login</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if the browser supports local storage
            if (typeof(Storage) !== "undefined") {
                // Retrieve values from local storage and set them as input values
                document.getElementById("First_name").value = localStorage.getItem("First_name") || "";
                document.getElementById("Middle_name").value = localStorage.getItem("Middle_name") || "";
                document.getElementById("Lastname").value = localStorage.getItem("Lastname") || "";
                document.getElementById("username").value = localStorage.getItem("username") || "";
                document.getElementById("password").value = localStorage.getItem("password") || "";
                document.getElementById("Email").value = localStorage.getItem("Email") || "";
                document.getElementById("confirm_password").value = localStorage.getItem("confirm_password") || "";
                document.getElementById("Status").value = localStorage.getItem("Status") || "";

                // Store input values in local storage when the form is submitted
                document.getElementById("register_btn").addEventListener("click", function() {
                    localStorage.setItem("First_name", document.getElementById("First_name").value);
                    localStorage.setItem("Middle_name", document.getElementById("Middle_name").value);
                    localStorage.setItem("Lastname", document.getElementById("Lastname").value);
                    localStorage.setItem("username", document.getElementById("username").value);
                    localStorage.setItem("password", document.getElementById("password").value);
                    localStorage.setItem("Email", document.getElementById("Email").value);
                    localStorage.setItem("confirm_password", document.getElementById("confirm_password").value);
                    localStorage.setItem("Status", document.getElementById("Status").value);

                    // Mark the form as submitted
                    document.querySelector("form").submitted = true;
                });

                // Clear local storage when navigating away from the page without submitting the form
                window.addEventListener('beforeunload', function(event) {
                    if (!document.querySelector("form").submitted) {
                        localStorage.removeItem("First_name");
                        localStorage.removeItem("Middle_name");
                        localStorage.removeItem("Lastname");
                        localStorage.removeItem("username");
                        localStorage.removeItem("password");
                        localStorage.removeItem("Email");
                        localStorage.removeItem("confirm_password");
                        localStorage.removeItem("Status");
                    }
                });
            } else {
                // Local storage is not supported
                alert("Sorry, your browser does not support web storage. Your inputs will not be saved.");
            }

            // Remove error message from URL parameters if present
            const url = new URL(window.location.href);
            if (url.searchParams.has('error')) {
                url.searchParams.delete('error');
                window.history.replaceState({}, document.title, url.toString());
            }
        });
    </script>
</body>
</html>

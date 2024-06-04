<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN</title>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.2/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="container mt-5" style="background-color: yellow;">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form id="loginForm" action="index.php" method="POST" class="bg-yellow p-4">
                    <h2 class="mb-4">LOGIN</h2>
                    <?php if (isset($_GET['error'])) { ?>
                        <p class="error"><?php echo $_GET['error']; ?></p> <?php } ?>

                    <div class="mb-3">
                        <label for="username" class="form-label">Username:</label>
                        <input type="text" id="username" name="username" class="form-control" placeholder="Username">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password:</label>
                        <div class="input-group">
                            <input type="password" id="password" name="password" class="form-control" placeholder="Password">
                        </div>
                    </div>

                    <button type="submit" id="submit" class="btn btn-info">Submit</button><br><br>

                    <div class="text-center mb-3">
                        <a href="registrationform.php">Click to register</a>
                    </div>
                    
                    <div class="text-center">
                        <p>or login with</p>
                        
                        <a href="facebook-login.php" class="btn btn-primary">
                            <i class="bi bi-facebook"></i> Facebook
                        </a>
                        <a href="google-login.php" class="btn btn-danger">
                            <i class="bi bi-google"></i> Google
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');

            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.querySelector('i').classList.toggle('bi-eye');
                this.querySelector('i').classList.toggle('bi-eye-slash');
            });

            // Check if the browser supports local storage
            if (typeof(Storage) !== "undefined") {
                // Retrieve values from local storage and set them as input values
                document.getElementById("username").value = localStorage.getItem("username") || "";
                document.getElementById("password").value = localStorage.getItem("password") || "";

                // Store input values in local storage when the form is submitted
                document.getElementById("submit").addEventListener("click", function() {
                    localStorage.setItem("username", document.getElementById("username").value);
                    
                    // Mark the form as submitted
                    document.querySelector("form").submitted = true;
                });

                // Clear local storage when navigating away from the page without submitting the form
                window.addEventListener('beforeunload', function(event) {
                    if (!document.querySelector("form").submitted) {
                        localStorage.removeItem("username");
                        localStorage.removeItem("password");
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

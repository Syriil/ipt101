<?php
session_start(); 


include "db_conn.php";

$full_name = $email = $address = $gender = $contact_info = $date_of_birth = $education  = $social_media =  $phone_number = "";

$username = $_SESSION['username'];


$sql_user = "SELECT user_id, username, password, Lastname, First_name, Middle_name, Email FROM user WHERE username = '$username'";
$result_user = mysqli_query($conn, $sql_user);

if ($result_user && mysqli_num_rows($result_user) > 0) {
    $row_user = mysqli_fetch_assoc($result_user);
    $user_id = $row_user['user_id'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $full_name = $_POST['full_name'];
        $email = $_POST['email'];
        $phone_number = $_POST['phone_number'];
        $address = $_POST['address'];
        $date_of_birth = $_POST['date_of_birth'];
        $gender = $_POST['gender'];
        $contact_info = $_POST['contact_info'];
        $education = $_POST['education'];
        
        $social_media = $_POST['social_media'];
        

        // Regular expressions for validation
        $phone_regex = '/^\d{11}$/';
        $email_regex = '/^\S+@\S+\.\S+$/';
        $social_media_regex = '/^(https?:\/\/)?(www\.)?(facebook\.com|twitter\.com|instagram\.com|linkedin\.com|youtube\.com|snapchat\.com)\/.+$/i';

        $errors = array();

        function validateEmail($email)
        {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        }

        // Validate email
        if (empty($email) || !validateEmail($email)) {
            $errors[] = "Invalid email";
        }

        // Validate phone number
        if (!empty($phone_number) && !preg_match($phone_regex, $phone_number)) {
            $errors[] = "Invalid phone number";
        }

        // Validate social media link
        if (!empty($social_media) && !preg_match($social_media_regex, $social_media)) {
            $errors[] = "Invalid social media link";
        }

        // Check if the new email already exists in the database
        $sql_check_email = "SELECT user_id FROM user WHERE Email = ? AND user_id != ?";
        $stmt_check_email = $conn->prepare($sql_check_email);
        $stmt_check_email->bind_param('si', $email, $user_id);
        $stmt_check_email->execute();
        $result_check_email = $stmt_check_email->get_result();

        if ($result_check_email->num_rows > 0) {
            $errors[] = "The email address is already in use by another account.";
        }

        // Check if the date of birth is a valid past date and at least 13 years ago
        $today = date("Y-m-d");
        $min_age_date = date("Y-m-d", strtotime("-13 years"));

        if (empty($date_of_birth) || $date_of_birth >= $today) {
            $errors[] = "Date of birth must be a valid past date.";
        } elseif ($date_of_birth > $min_age_date) {
            $errors[] = "You must be at least 13 years old.";
        }

        // Handle profile picture upload
        $profile_picture = '';

        if (isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] == UPLOAD_ERR_OK) {
            $allowed_types = array('image/jpeg', 'image/jpg', 'image/png');
            $file_type = $_FILES["profile_picture"]["type"];

            if (in_array($file_type, $allowed_types)) {
                $profile_picture = $_FILES["profile_picture"]["tmp_name"];
                $profile_picture_content = file_get_contents($profile_picture);

                if ($profile_picture_content === false) {
                    $errors[] = "Failed to read profile picture file.";
                } else {
                    $profile_picture_content = mysqli_real_escape_string($conn, $profile_picture_content);
                }
            } else {
                $errors[] = "Invalid file format. Only JPG, JPEG, and PNG files are allowed.";
            }
        }

        // If there are any errors, redirect back to the dashboard with the error messages
        if (!empty($errors)) {
            $error_message = implode(", ", $errors);
            header("Location: dashboard.php?error=$error_message");
            exit();
        }

        // Construct the SQL query to update the user profile
        $sql_profile = "UPDATE user_profile SET 
                    full_name = '$full_name', 
                    phone_number = '$phone_number', 
                    address = '$address', 
                    date_of_birth = '$date_of_birth', 
                    gender = '$gender',";

        if (!empty($profile_picture_content)) {
            $sql_profile .= " profile_picture = '$profile_picture_content',";
        }

        $sql_profile .= " contact_info = '$contact_info', 
                    education = '$education', 
                    social_media = '$social_media' 
                    WHERE user_id = $user_id";

        // Construct the SQL query to update the user's email
        $sql_user = "UPDATE user SET Email = '$email' WHERE user_id = $user_id";

        // Execute the update queries
        if (mysqli_query($conn, $sql_profile) && mysqli_query($conn, $sql_user)) {
            header("Location: dashboard.php?success=Your profile has been updated successfully");
        } else {
            $error_message = mysqli_error($conn);
            $error_message = urlencode("Your profile could not be updated: $error_message");
            header("Location: dashboard.php?error=$error_message");
            exit();
        }
    } else {
        echo "No form submitted or no file uploaded.";
    }
}
?>

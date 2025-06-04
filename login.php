<?php
// login.php

// --- IMPORTANT: session_start() MUST be at the very top before any output ---
session_start();

// --- IMPORTANT: FOR DEBUGGING ONLY ---
// Remove these lines in a production environment as they can expose sensitive information.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// --- END DEBUGGING SETTINGS ---


// Include your database configuration file
// Ensure configdb.php is in the same directory or adjust the path.
include_once 'configdb.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Use 'username' as the variable name to match your database column more clearly
    $username_input = $_POST['registration_number']; // Get value from form field named 'registration_number'
    $password_input = $_POST['password'];

    // CORRECTED SQL QUERY:
    // - Changed 'registration_number' to 'username' to match your database column.
    // - Changed 'name' to 'student_name' to match your database column.
    $sql = "SELECT student_id, student_name, password FROM students WHERE username = ?";

    // Prepare the SQL statement
    $stmt = $conn->prepare($sql);

    // Check if the prepare statement failed (e.g., due to incorrect column names or SQL syntax)
    if ($stmt === false) {
        // Log the detailed error to the server's error log (not visible to user)
        error_log("SQL Prepare Failed in login.php: " . $conn->error);
        // Display a generic error to the user
        $error = "An internal server error occurred. Please try again later.";
    } else {
        // Bind parameters: 's' for string (username)
        $stmt->bind_param("s", $username_input);

        // Execute the statement
        $stmt->execute();

        // Store the result to check the number of rows
        $stmt->store_result();

        // Bind result variables to the columns selected
        $stmt->bind_result($student_id, $student_name, $hashed_password); // Bind to student_name

        if ($stmt->num_rows == 1) {
            // Fetch the result (moves data into bound variables)
            $stmt->fetch();

            // Verify the password
            if (password_verify($password_input, $hashed_password)) {
                // Password is correct, set session variables
                $_SESSION['student_id'] = $student_id;
                $_SESSION['student_name'] = $student_name; // Use $student_name here

                // Redirect to the dashboard
                header("Location: dashboard.php");
                exit(); // Always exit after a header redirect
            } else {
                // Incorrect password
                $error = "Invalid username or password.";
            }
        } else {
            // No user found with that username
            $error = "Invalid username or password.";
        }

        // Close the statement
        $stmt->close();
    }
}

// Close the database connection when script finishes (or here if logic is done)
// This should be done only if the connection was successfully opened.
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
    <style>
        /* Your CSS styles here */
        /* ... (all your existing CSS) ... */

        body {
            font-family: 'Comic Sans MS', 'Chalkboard SE', cursive;
            background-color: #e6ffe6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            overflow: hidden;
        }

        .container {
            background-color: #FFFFFF;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 380px;
            text-align: center;
            border: 2px solid #a0e0a0;
        }

        h2 {
            color: #4CAF50;
            margin-bottom: 25px;
            font-size: 2em;
            font-weight: bold;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.05);
        }

        label {
            display: block;
            text-align: left;
            margin-bottom: 8px;
            color: #666;
            font-weight: normal;
            font-size: 1.1em;
        }

        input#registration_number,
        input#password {
            width: calc(100% - 20px);
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #c8e6c9;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 1em;
            color: #333;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        input#registration_number:focus,
        input#password:focus {
            border-color: #81C784;
            outline: none;
            box-shadow: 0 0 5px rgba(129, 199, 132, 0.6);
        }

        button {
            background-color: #81C784;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: bold;
            width: 100%;
            transition: background-color 0.3s ease, transform 0.2s ease;
            letter-spacing: 0.5px;
        }

        button:hover {
            background-color: #66BB6A;
            transform: translateY(-2px);
        }

        button:active {
            transform: translateY(0);
        }

        .error {
            color: #d32f2f;
            background-color: #ffe0e0;
            border: 1px solid #ffcdd2;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 0.9em;
        }

        .success {
            color: #4CAF50;
            background-color: #e8f5e9;
            border: 1px solid #c8e6c9;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 0.9em;
        }

        p {
            margin-top: 20px;
            color: #777;
            font-size: 0.95em;
        }

        p a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        p a:hover {
            color: #388E3C;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Student Login</h2>
        <?php
        // Added htmlspecialchars for security when displaying session messages
        if (isset($_SESSION['message'])) {
            echo "<p class='success'>" . htmlspecialchars($_SESSION['message']) . "</p>";
            unset($_SESSION['message']);
        }
        // Added htmlspecialchars for security when displaying error messages
        if (isset($error)) {
            echo "<p class='error'>" . htmlspecialchars($error) . "</p>";
        }
        ?>
        <form action="login.php" method="POST">
            <label for="registration_number">Username (or Registration Number):</label>
            <input type="text" id="registration_number" name="registration_number" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>
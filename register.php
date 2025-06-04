<?php
// register.php

// --- IMPORTANT: FOR DEBUGGING ONLY ---
// Remove these lines in a production environment as they can expose sensitive information.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// --- END DEBUGGING SETTINGS ---

session_start();
include_once'configdb.php'; // Ensure this file correctly sets up your database connection ($conn)

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username_input = $_POST['registration_number']; // Use 'username_input' to match DB 'username'
    $student_name_input = $_POST['name'];             // Use 'student_name_input' to match DB 'student_name'
    $password_input = $_POST['password'];

    // --- IMPORTANT: Your 'students' table currently DOES NOT have an 'email' column. ---
    // If you want to store email, you must first add an 'email' column to your 'students' table:
    // ALTER TABLE students ADD COLUMN email VARCHAR(100) UNIQUE AFTER student_name;
    // For now, I'm commenting out the email part.
    // $email = $_POST['email'];


    // Hash the password securely for database storage
    $hashed_password = password_hash($password_input, PASSWORD_DEFAULT);

    // Prepare SQL statement to prevent SQL injection
    // CORRECTED COLUMNS: 'username', 'student_name', 'password'
    $sql = "INSERT INTO students (username, student_name, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Check if statement preparation was successful
    if ($stmt === false) {
        // Log the detailed error to the server's error log
        error_log("Database error (prepare failed in register.php): " . $conn->error);
        $error = "An internal server error occurred during registration. Please try again later.";
    } else {
        // Bind parameters: 'sss' for 3 string parameters (username, student_name, hashed_password)
        $stmt->bind_param("sss", $username_input, $student_name_input, $hashed_password);

        // Execute the statement
        if ($stmt->execute()) {
            $_SESSION['message'] = "Registration successful! Please login.";
            header("Location: login.php");
            exit(); // Always exit after a header redirect
        } else {
            // Check for specific duplicate entry error (e.g., unique username)
            if ($conn->errno == 1062) { // MySQL error code for duplicate entry
                $error = "Registration number (username) already exists. Please try another one.";
            } else {
                // Log the detailed error to the server's error log
                error_log("Error during registration execution: " . $stmt->error);
                $error = "Error during registration: " . $stmt->error; // For debugging, remove $stmt->error in production
            }
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <style>
        /* Your CSS styles here */
        /* ... (all your existing CSS for the form) ... */

        /* Body with a soft, very light green background */
        body {
            font-family: 'Open Sans', Arial, sans-serif;
            background: linear-gradient(135deg, #e6ffe6 0%, #c8e6c9 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            overflow: hidden;
            position: relative;
        }

        /* Subtle decorative elements for an aesthetic touch */
        body::before, body::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            filter: blur(40px);
            z-index: -1;
        }

        body::before {
            width: 150px;
            height: 150px;
            top: 10%;
            left: 10%;
            animation: floatEffect 8s ease-in-out infinite alternate;
        }

        body::after {
            width: 200px;
            height: 200px;
            bottom: 15%;
            right: 15%;
            animation: floatEffect 10s ease-in-out infinite alternate-reverse;
        }

        @keyframes floatEffect {
            0% { transform: translate(0, 0); }
            100% { transform: translate(20px, 20px); }
        }

        .container {
            background-color: #FFFFFF;
            padding: 35px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
            width: 100%;
            max-width: 350px;
            text-align: center;
            animation: fadeInSlightlyUp 0.7s ease-out;
            box-sizing: border-box;
        }

        @keyframes fadeInSlightlyUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            color: #4CAF50;
            margin-bottom: 25px;
            font-size: 2.2em;
            font-weight: 700;
            letter-spacing: -0.5px;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.08);
        }

        label {
            display: block;
            text-align: left;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
            font-size: 0.95em;
            letter-spacing: 0.2px;
        }

        input[type="text"],
        input[type="email"], /* Keep this for future if you add email to DB */
        input[type="password"] {
            width: calc(100% - 20px);
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #d4edda;
            border-radius: 10px;
            box-sizing: border-box;
            font-size: 1em;
            color: #333;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #66BB6A;
            outline: none;
            box-shadow: 0 0 8px rgba(102, 187, 106, 0.4);
        }

        button {
            background-color: #66BB6A;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: 700;
            width: 100%;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.2s ease;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 10px rgba(102, 187, 106, 0.3);
        }

        button:hover {
            background-color: #4CAF50;
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(102, 187, 106, 0.4);
        }

        button:active {
            transform: translateY(0);
            box-shadow: 0 2px 5px rgba(102, 187, 106, 0.2);
        }

        .error {
            color: #d32f2f;
            background-color: #ffebee;
            border: 1px solid #ef9a9a;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 0.88em;
            font-weight: 600;
        }

        .success {
            color: #4CAF50;
            background-color: #e8f5e9;
            border: 1px solid #a5d6a7;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 0.88em;
            font-weight: 600;
        }

        p {
            margin-top: 20px;
            color: #777;
            font-size: 0.9em;
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

        /* --- MEDIA QUERIES FOR MOBILE RESPONSIVENESS --- */
        @media (max-width: 600px) {
            body {
                align-items: flex-start;
                padding-top: 20px;
            }

            .container {
                max-width: 90%;
                margin: 0 auto;
                padding: 20px;
                border-radius: 15px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            }

            h2 {
                font-size: 1.6em;
                margin-bottom: 15px;
            }

            label {
                font-size: 0.85em;
                margin-bottom: 5px;
            }

            input[type="text"],
            input[type="email"],
            input[type="password"] {
                padding: 10px;
                margin-bottom: 15px;
                font-size: 0.9em;
            }

            button {
                padding: 10px 18px;
                font-size: 0.95em;
            }

            .error, .success {
                padding: 8px;
                font-size: 0.8em;
                margin-bottom: 10px;
            }

            p {
                margin-top: 15px;
                font-size: 0.8em;
            }

            body::before, body::after {
                display: none;
            }
        }

        @media (max-width: 400px) {
            .container {
                padding: 15px;
                max-width: 95%;
            }

            h2 {
                font-size: 1.4em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Student Registration</h2>
        <?php
        // Added htmlspecialchars for security when displaying error messages
        if (isset($error)) { echo "<p class='error'>" . htmlspecialchars($error) . "</p>"; }
        ?>
        <form action="register.php" method="POST">
            <label for="registration_number">Username (e.g., Registration Number):</label>
            <input type="text" id="registration_number" name="registration_number" required>

            <label for="name">Your Full Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>
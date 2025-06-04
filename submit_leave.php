<?php
// submit_leave.php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

// Ensure the path to your database configuration is correct.
// Based on your earlier problem, it was 'configdb.php', not 'config/db.php'.
include 'configdb.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_SESSION['student_id'];

    // 1. Get start_date and end_date from the form
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';

    // 2. Get meal_type as an array and convert it to a comma-separated string
    // This is crucial because your form sends multiple checkboxes with the same name 'meal_type[]'
    $meal_types_array = $_POST['meal_type'] ?? [];
    if (empty($meal_types_array)) {
        $_SESSION['error_message'] = "Please select at least one meal to skip.";
        header("Location: dashboard.php");
        exit();
    }
    // Implode the array into a string (e.g., "Breakfast,Lunch") for database storage
    $meal_type_string = implode(",", $meal_types_array);

    // 3. Get the reason (optional)
    $reason = $_POST['reason'] ?? '';

    $errors = [];

    // --- Input Validation ---
    // Validate dates
    if (empty($start_date) || empty($end_date)) {
        $errors[] = "Start date and end date are required.";
    } elseif (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $start_date) || !preg_match("/^\d{4}-\d{2}-\d{2}$/", $end_date)) {
        $errors[] = "Invalid date format. Dates must be YYYY-MM-DD.";
    } else {
        $start_dt = new DateTime($start_date);
        $end_dt = new DateTime($end_date);
        $current_dt = new DateTime(date('Y-m-d')); // Today's date

        if ($start_dt > $end_dt) {
            $errors[] = "End date cannot be before the start date.";
        }
        if ($start_dt < $current_dt) {
            $errors[] = "You cannot request a skip for a past date (From Date).";
        }
        // Additional check: Ensure end_date is not too far in the future if desired
        // e.g., if ($end_dt > new DateTime('+6 months')) { $errors[] = "Cannot skip more than 6 months in advance."; }
    }

    // If there are validation errors, store and redirect
    if (!empty($errors)) {
        $_SESSION['error_message'] = "Error submitting skip request: <br>" . implode("<br>", $errors);
        header("Location: dashboard.php");
        exit();
    }

    // --- Database Insertion ---
    // We are now inserting a single row for a date range, not looping through individual dates.
    // The columns are now 'start_date', 'end_date', 'meal_type', 'reason'.
    $sql = "INSERT INTO leave_requests (student_id, start_date, end_date, meal_type, reason) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        error_log("Prepare failed for leave request insertion: " . $conn->error);
        $_SESSION['error_message'] = "Database error: Could not prepare statement. Please try again.";
        header("Location: dashboard.php");
        exit();
    }

    // Bind parameters: 'issss' means integer, string, string, string, string
    // student_id (int), start_date (string), end_date (string), meal_type_string (string), reason (string)
    $stmt->bind_param("issss", $student_id, $start_date, $end_date, $meal_type_string, $reason);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Meal skip request for " . htmlspecialchars($start_date) . " to " . htmlspecialchars($end_date) . " submitted successfully!";
    } else {
        // Check for duplicate entry error if applicable (though we aren't checking for ranges yet)
        if ($conn->errno == 1062) { // MySQL error code for duplicate entry
            $_SESSION['error_message'] = "A similar skip request for these dates/meals might already exist.";
        } else {
            error_log("Execute failed for leave request insertion: " . $stmt->error);
            $_SESSION['error_message'] = "Error submitting your request: " . $stmt->error;
        }
    }
    $stmt->close();
    $conn->close();

    header("Location: dashboard.php");
    exit();

} else {
    // If someone tries to access this page directly without a POST request
    header("Location: dashboard.php");
    exit();
}
?>
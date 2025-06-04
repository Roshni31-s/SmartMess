<?php

// dashboard.php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}
// Using require_once for configdb.php is a good practice to prevent multiple inclusions.
require_once 'configdb.php'; // Ensure this path is correct

$student_id = $_SESSION['student_id'] ?? null;
$student_name = $_SESSION['student_name'] ?? 'Student';

// Check if $student_id is valid before proceeding with DB queries
if ($student_id === null) {
    header("Location: login.php");
    exit();
}

// Fetch food saving tips from the database
$tips = [];
$sql_tips = "SELECT tip_text FROM food_saving_tips ORDER BY created_at DESC LIMIT 5";
$result_tips = $conn->query($sql_tips);
if ($result_tips) {
    if ($result_tips->num_rows > 0) {
        while($row = $result_tips->fetch_assoc()) {
            $tips[] = $row['tip_text'];
        }
    }
} else {
    error_log("Error fetching food saving tips: " . $conn->error);
}

// Fetch upcoming leave/skip requests for the logged-in student
$upcoming_leaves = [];
// This query now correctly uses 'start_date' and 'end_date'
// Using CURDATE() to ensure only future or current leaves are shown
$sql_leaves = "SELECT start_date, end_date, meal_type, reason FROM leave_requests WHERE student_id = ? AND end_date >= CURDATE() ORDER BY start_date ASC";
$stmt_leaves = $conn->prepare($sql_leaves);

if ($stmt_leaves === false) {
    error_log("Prepare failed for leave requests: " . $conn->error);
    $_SESSION['error_message'] = "Could not load upcoming leave requests. Please try again later.";
} else {
    $stmt_leaves->bind_param("i", $student_id); // 'i' for integer student_id

    if ($stmt_leaves->execute()) {
        $result_leaves = $stmt_leaves->get_result();
        if ($result_leaves->num_rows > 0) {
            while($row = $result_leaves->fetch_assoc()) {
                $upcoming_leaves[] = $row;
            }
        }
    } else {
        error_log("Execute failed for leave requests: " . $stmt_leaves->error);
        $_SESSION['error_message'] = "Error fetching your leave requests.";
    }
    $stmt_leaves->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mess Dashboard</title>
    <style>
        /* General Reset & Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8faf8;
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header Styles */
        .header {
            background-color: #2e7d32; /* Dark Green */
            color: white;
            padding: 25px 40px; /* Consistent padding */
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(46, 125, 50, 0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap; /* Allows wrapping on smaller screens */
        }

        .header h1 {
            font-size: 2.2em;
            margin: 0;
            flex-grow: 1; /* Allows h1 to take available space */
            text-align: left; /* Align text to the left */
        }

        .logout-btn {
            background-color: white;
            color: #2e7d32; /* Dark Green */
            padding: 8px 20px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
            transition: all 0.3s ease;
            border: 2px solid white;
            white-space: nowrap; /* Prevent text wrapping */
            margin-left: 20px; /* Space from h1 */
        }

        .logout-btn:hover {
            background-color: transparent;
            color: white;
        }

        /* Main Container */
        .container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            max-width: 1200px;
            margin: 0 auto 30px auto;
            padding: 0 20px;
            flex-grow: 1; /* Allows container to take up available space */
        }

        /* Box Styles */
        .box {
            background-color: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            border: 1px solid #e0e0e0;
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .box:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(46, 125, 50, 0.1);
        }

        .box h2 {
            color: #2e7d32; /* Dark Green */
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e8f5e9; /* Light Green Border */
            font-size: 1.4em;
            display: flex;
            align-items: center;
        }

        .box h2::before {
            content: "🍃"; /* Leaf emoji */
            margin-right: 10px;
        }

        /* Food Saving Tips */
        .tip-item {
            padding: 10px 0;
            border-bottom: 1px solid #e8f5e9;
            color: #4a4a4a;
        }

        .tip-item:last-child {
            border-bottom: none;
        }

        /* Meal Selection Grid */
        .meal-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
            margin: 15px 0;
        }

        .meal-option {
            background-color: #f1f8e9; /* Very light green */
            border: 2px solid #c8e6c9; /* Light green */
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100px; /* Ensure consistent height */
            position: relative; /* For checkmark positioning */
        }

        .meal-option:hover {
            background-color: #e8f5e9; /* Slightly darker light green */
        }

        .meal-option.selected {
            background-color: #2e7d32; /* Dark Green */
            color: white;
            border-color: #2e7d32; /* Dark Green */
        }

        /* Checkmark for selected options */
        .meal-option.selected::after {
            content: '✓'; /* Checkmark character */
            position: absolute;
            bottom: 5px;
            right: 5px;
            font-size: 1.2em;
            color: white;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }

        .meal-icon {
            font-size: 1.8em;
            margin-bottom: 8px;
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2e7d32; /* Dark Green */
        }

        input[type="date"],
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #c8e6c9; /* Light Green */
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            background-color: #f8faf8;
        }

        input[type="date"]:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #2e7d32; /* Dark Green */
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.2);
        }

        button {
            background-color: #2e7d32; /* Dark Green */
            color: white;
            border: none;
            padding: 14px;
            border-radius: 8px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 10px;
        }

        button:hover {
            background-color: #1b5e20; /* Even darker green */
            transform: translateY(-2px);
        }

        /* Messages (Success/Error) */
        .message {
            padding: 15px;
            margin-top: 20px;
            border-radius: 8px;
            animation: slideIn 0.5s ease;
            font-weight: 600;
        }

        .success {
            background-color: #e8f5e9; /* Lightest green */
            border: 1px solid #c8e6c9;
            color: #1b5e20; /* Darker green */
        }

        .error {
            background-color: #ffebee; /* Light red */
            border: 1px solid #ef9a9a;
            color: #c62828; /* Dark red */
        }

        /* Leaves Table */
        .leaves-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 0.95em;
            flex-grow: 1; /* Allows table to fill space in its box */
        }

        .leaves-table th {
            background-color: #e8f5e9; /* Lightest green */
            color: #2e7d32; /* Dark Green */
            text-align: left;
            padding: 12px;
            border-bottom: 2px solid #c8e6c9;
        }

        .leaves-table td {
            padding: 12px;
            border-bottom: 1px solid #e8f5e9;
        }

        .leaves-table tr:hover {
            background-color: #f1f8e9; /* Very light green on hover */
        }

        /* Animations */
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                padding: 20px;
                text-align: center; /* Center align header content on small screens */
            }
            .header h1 {
                font-size: 1.8em;
                margin-bottom: 15px;
                text-align: center; /* Center align title */
            }
            .logout-btn {
                margin-left: 0; /* Remove left margin */
                margin-top: 10px; /* Add top margin */
            }
            .container {
                grid-template-columns: 1fr;
            }
            .meal-grid {
                grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome, <?php echo htmlspecialchars($student_name); ?>!</h1>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="container">
        <div class="box">
            <h2>Food Saving Tips</h2>
            <?php if (!empty($tips)): ?>
                <?php foreach ($tips as $tip): ?>
                    <div class="tip-item">• <?php echo htmlspecialchars($tip); ?></div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; color: #666;">No tips available yet.</p>
            <?php endif; ?>
        </div>

        <div class="box">
            <h2>Notify Meal Skip</h2>
            <form action="submit_leave.php" method="POST" id="leaveForm">
                <div class="form-group">
                    <label for="start_date">From Date</label>
                    <input type="date" name="start_date" id="start_date" required min="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="form-group">
                    <label for="end_date">To Date</label>
                    <input type="date" name="end_date" id="end_date" required min="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="form-group">
                    <label>Select Meals to Skip</label>
                    <div class="meal-grid">
                        <div class="meal-option" data-meal="Breakfast">
                            <div class="meal-icon">🌅</div>
                            <div>Breakfast</div>
                            <div style="font-size: 0.9em; opacity: 0.8;">7-9 AM</div>
                            <input type="checkbox" name="meal_type[]" value="Breakfast" style="display: none;">
                        </div>
                        <div class="meal-option" data-meal="Lunch">
                            <div class="meal-icon">☀️</div>
                            <div>Lunch</div>
                            <div style="font-size: 0.9em; opacity: 0.8;">12-2 PM</div>
                            <input type="checkbox" name="meal_type[]" value="Lunch" style="display: none;">
                        </div>
                        <div class="meal-option" data-meal="Dinner">
                            <div class="meal-icon">🌙</div>
                            <div>Dinner</div>
                            <div style="font-size: 0.9em; opacity: 0.8;">7-9 PM</div>
                            <input type="checkbox" name="meal_type[]" value="Dinner" style="display: none;">
                        </div>
                        <div class="meal-option" data-meal="All Day" id="all_day_option">
                            <div class="meal-icon">🗓️</div>
                            <div>All Day</div>
                            <div style="font-size: 0.9em; opacity: 0.8;">(B, L, D)</div>
                            <input type="checkbox" name="meal_type[]" value="All Day" style="display: none;">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="reason">Reason (Optional)</label>
                    <select name="reason" id="reason">
                        <option value="">Select reason</option>
                        <option value="Going Home">Going Home</option>
                        <option value="Trip/Outing">Trip/Outing</option>
                        <option value="College Event">College Event</option>
                        <option value="Not Well">Not Feeling Well</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <button type="submit">Submit Skip Request</button>
            </form>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="message success"><?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="message error"><?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?></div>
            <?php endif; ?>
        </div>

        <div class="box">
            <h2>Your Upcoming Skips</h2>
            <?php if (!empty($upcoming_leaves)): ?>
                <table class="leaves-table">
                    <thead>
                        <tr>
                            <th>From Date</th>
                            <th>To Date</th>
                            <th>Meal(s)</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($upcoming_leaves as $leave): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($leave['start_date']); ?></td>
                                <td><?php echo htmlspecialchars($leave['end_date']); ?></td>
                                <td><?php echo htmlspecialchars($leave['meal_type']); ?></td>
                                <td><?php echo htmlspecialchars($leave['reason'] ?? 'N/A'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; padding: 20px; color: #666;">No upcoming meal skips scheduled.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Get all meal option elements
        const mealOptions = document.querySelectorAll('.meal-option');
        const allDayOption = document.getElementById('all_day_option');
        const individualMealCheckboxes = document.querySelectorAll('.meal-option:not(#all_day_option) input[type="checkbox"]');
        const individualMealOptions = document.querySelectorAll('.meal-option:not(#all_day_option)');

        mealOptions.forEach(option => {
            option.addEventListener('click', function() {
                const checkbox = this.querySelector('input[type="checkbox"]');
                
                // Toggle the selected class and checkbox state
                this.classList.toggle('selected');
                checkbox.checked = !checkbox.checked;

                // If "All Day" was clicked
                if (this.id === 'all_day_option') {
                    const isAllDaySelected = this.classList.contains('selected');
                    individualMealCheckboxes.forEach(individualCheckbox => {
                        const individualOption = individualCheckbox.closest('.meal-option');
                        if (isAllDaySelected) {
                            individualOption.classList.add('selected');
                            individualCheckbox.checked = true;
                        } else {
                            individualOption.classList.remove('selected');
                            individualCheckbox.checked = false;
                        }
                    });
                } else {
                    // If an individual meal was clicked, check if all are selected
                    const allIndividualMealsSelected = Array.from(individualMealCheckboxes).every(cb => cb.checked);
                    if (allIndividualMealsSelected) {
                        allDayOption.classList.add('selected');
                        allDayOption.querySelector('input[type="checkbox"]').checked = true;
                    } else {
                        allDayOption.classList.remove('selected');
                        allDayOption.querySelector('input[type="checkbox"]').checked = false;
                    }
                }
            });
        });

        // Set minimum date for 'From Date' to today
        document.getElementById('start_date').min = new Date().toISOString().split('T')[0];

        // Ensure 'To Date' is not before 'From Date' and also not before today
        document.getElementById('start_date').addEventListener('change', function() {
            const startDate = this.value;
            const endDateInput = document.getElementById('end_date');
            
            endDateInput.min = startDate; // Set min for end_date to current start_date

            // If end_date is earlier than new start_date, reset it to start_date
            if (endDateInput.value < startDate) {
                endDateInput.value = startDate;
            }
        });

        // Also ensure end_date cannot be set before today's date initially
        document.getElementById('end_date').min = new Date().toISOString().split('T')[0];


        // Form validation before submission
        document.getElementById('leaveForm').addEventListener('submit', function(e) {
            const mealSelected = document.querySelectorAll('input[name="meal_type[]"]:checked').length > 0;
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;

            if (!mealSelected) {
                e.preventDefault();
                alert('Please select at least one meal to skip.');
                return;
            }
            if (new Date(startDate) > new Date(endDate)) {
                e.preventDefault();
                alert('End Date cannot be before From Date.');
                return;
            }
            
            // Additional validation for "All Day" selection ambiguity if desired:
            // This prevents a scenario where "All Day" is selected, but also only
            // one or two individual meals are explicitly selected alongside it,
            // which can create confusion.
            const isAllDayChecked = allDayOption.classList.contains('selected');
            const numIndividualMealsSelected = Array.from(individualMealCheckboxes).filter(cb => cb.checked).length;
            
            if (isAllDayChecked && numIndividualMealsSelected > 0 && numIndividualMealsSelected < 3) {
                 e.preventDefault();
                 alert('If "All Day" is selected, it implies all meals. Please either select only "All Day" or select specific meals (Breakfast, Lunch, Dinner).');
                 return;
            }
        });
    </script>
</body>
</html>
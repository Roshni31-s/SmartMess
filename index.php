<?php
// Session configuration must be FIRST, with no whitespace before
// Always include the configdb.php and start the session
require_once('configdb.php'); // Use require_once to prevent multiple inclusions

// Configure session parameters for better security
session_set_cookie_params([
    'lifetime' => 86400, // 1 day
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    // 'secure' => true,    // Enable in production with HTTPS - uncomment when deploying to HTTPS
    'httponly' => true, // Prevents JavaScript access to session cookie
    'samesite' => 'Lax' // Changed to Lax. Strict can cause issues with some third-party integrations (e.g., if a login form is submitted from another domain). 'Lax' provides good security while being more compatible.
]);

// Start session securely
session_start();
session_regenerate_id(true); // Regenerate session ID on each page load for better security (prevents session fixation)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MessMaintenance - Smart Campus</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/style.css"> <style>
        /* Global CSS Reset & Box Sizing */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Root CSS Variables */
        :root {
            --primary: #27ae60;          /* Vibrant green */
            --primary-light: #2ecc71;    /* Lighter green */
            --primary-dark: #219653;     /* Darker green */
            --secondary: #f39c12;        /* Accent orange */
            --accent: #e67e22;           /* Warm accent */
            --light: #f5f5f5;            /* Light background */
            --dark: #34495e;             /* Dark text */
            --success: #2ecc71;          /* Success green */
            --warn: #f1c40f;             /* Warning yellow */

            /* New Gradient Variables for Body Background */
            --gradient-primary: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
            --gradient-hero: linear-gradient(135deg, rgba(39,174,96,0.95), rgba(46,204,113,0.95));
            --gradient-card: linear-gradient(45deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
        }

        /* Body Styles */
        body {
            font-family: 'Poppins', sans-serif;
            background: var(--gradient-primary); /* Apply the main body gradient */
            background-attachment: fixed; /* Ensures the background stays fixed during scroll */
            color: white; /* Default text color for elements directly on body background */
            padding: 20px; /* Base padding for overall content */
            line-height: 1.6;
        }

        /* Container Styles */
        .container {
            max-width: 1200px; /* Increased max-width for a more spacious feel */
            margin: 0 auto; /* Center the container */
            padding: 20px;
        }

        /* Section Titles */
        .section-title {
            font-size: 2.5rem; /* Slightly larger title */
            margin-bottom: 50px; /* More space below title */
            color: white; /* Default color for titles on the main gradient background */
            position: relative;
            display: inline-block;
            font-weight: 700; /* Bolder */
            text-align: center;
            width: 100%; /* Ensure it centers correctly */
        }

        .section-title::after {
            content: '';
            display: block;
            width: 80px; /* Longer underline */
            height: 4px; /* Thicker underline */
            background: var(--primary-light);
            margin: 15px auto 0; /* More space and centered */
            border-radius: 2px;
        }

        /* Hero Section */
        .hero-section {
            background: var(--gradient-hero), url('https://source.unsplash.com/random/1920x1080/?campus,food,green') center center / cover no-repeat; /* Dynamic background image */
            color: white;
            padding: 150px 0; /* More vertical padding */
            text-align: center;
            border-bottom: 5px solid var(--primary-dark);
            margin-bottom: 60px;
            border-radius: 15px; /* Rounded corners for the hero section */
            overflow: hidden; /* Ensures background doesn't bleed out of rounded corners */
            position: relative;
            z-index: 1; /* Ensure hero content is above any potential background elements */
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.2); /* Subtle overlay for text readability */
            z-index: -1;
        }

        .hero-section h1 {
            font-size: 4rem; /* Larger heading */
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 5px rgba(0,0,0,0.3);
        }

        .hero-section p.lead {
            font-size: 1.5rem; /* Larger lead paragraph */
            margin-bottom: 40px;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.2);
        }

        /* Quote Section */
        .quote-section {
            background: linear-gradient(45deg, #a8e6cf, #dcedc8); /* Softer green/yellow gradient */
            border-radius: 15px;
            padding: 30px; /* Increased padding */
            margin-bottom: 40px; /* More margin */
            text-align: center;
            border-left: 8px solid var(--primary); /* Thicker, vibrant border */
            box-shadow: 0 8px 20px rgba(0,0,0,0.1); /* Subtle shadow */
        }

        .quote {
            font-size: 1.6em; /* Larger font size */
            font-style: italic;
            color: var(--dark); /* Darker text for readability on light background */
            margin-bottom: 15px;
            line-height: 1.8;
            transition: opacity 0.3s ease;
        }

        .quote-author {
            font-weight: bold;
            color: var(--primary-dark); /* Darker green for author */
            font-size: 1.1em;
            transition: opacity 0.3s ease;
        }

        /* Waste Stat Boxes */
        .waste-stat-box {
            background: white;
            border-radius: 12px;
            padding: 30px; /* More padding */
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08); /* Improved shadow */
            border-bottom: 5px solid var(--primary);
            margin-bottom: 30px; /* Consistent margin */
            transition: all 0.3s ease;
            color: var(--dark);
            height: 100%; /* Ensure boxes are same height in a row */
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .waste-stat-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .waste-stat-number {
            font-size: 3.5rem; /* Larger numbers */
            font-weight: 700;
            color: var(--primary-dark); /* Emphasize with primary color */
            margin-bottom: 10px;
            display: block; /* Ensures margin works */
        }

        .waste-stat-box p {
            font-size: 1.1rem;
            color: var(--dark);
        }

        /* Feature Cards (Glassmorphism Style) */
        .feature-card {
            flex: 1;
            max-width: 380px; /* Slightly wider cards */
            min-height: 350px; /* Minimum height for consistent look */
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            color: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            cursor: pointer;
            margin: 0 auto 30px;
            display: flex; /* Flexbox for internal alignment */
            flex-direction: column;
            justify-content: space-between; /* Distribute space */
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.15), transparent); /* Stronger shimmer */
            transition: left 0.5s ease-in-out; /* Smoother transition */
        }

        .feature-card:hover::before {
            left: 100%;
        }

        .feature-card:hover {
            transform: translateY(-15px); /* More pronounced lift */
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4); /* Larger shadow */
            border-color: rgba(255, 255, 255, 0.6); /* Brighter border on hover */
        }

        .feature-card .icon-lg {
            width: 90px; /* Larger icons */
            height: 90px;
            border-radius: 50%;
            margin: 0 auto 25px; /* More space below icon */
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px; /* Larger icon font */
            transition: transform 0.3s ease;
            color: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2); /* Shadow for icons */
        }

        /* Specific gradients for each .feature-card icon */
        .feature-card:nth-child(1) .icon-lg {
            background: linear-gradient(45deg, #ff6b6b, #ee4040); /* Redder */
        }

        .feature-card:nth-child(2) .icon-lg {
            background: linear-gradient(45deg, #48cae4, #0077b6); /* Deeper blue */
        }

        .feature-card:nth-child(3) .icon-lg {
            background: linear-gradient(45deg, #06d6a0, #1b998b); /* More vibrant green */
        }

        .feature-card:hover .icon-lg {
            transform: scale(1.15) rotate(7deg); /* More pronounced animation */
        }

        .feature-card h3 {
            font-size: 28px; /* Larger heading */
            margin-bottom: 15px;
            font-weight: 700;
            color: white;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }

        .feature-card p {
            font-size: 17px; /* Slightly larger paragraph */
            line-height: 1.7;
            opacity: 0.95;
            margin-bottom: 25px; /* More space below paragraph */
            color: white;
        }

        /* Animations for feature cards */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-15px); } /* More floating effect */
        }

        .feature-card:nth-child(1) { animation: float 7s ease-in-out infinite; } /* Slightly longer duration */
        .feature-card:nth-child(2) { animation: float 7s ease-in-out infinite 2.5s; } /* Adjusted delay */
        .feature-card:nth-child(3) { animation: float 7s ease-in-out infinite 5s; } /* Adjusted delay */

        /* Save Food Callout */
        .save-food-callout {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border-radius: 15px; /* More rounded */
            padding: 40px; /* More padding */
            margin: 50px 0; /* More margin */
            text-align: center;
            box-shadow: 0 15px 40px rgba(46,204,113,0.3); /* Stronger shadow */
            border: 3px solid white; /* Thicker border */
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .save-food-callout:hover {
            transform: scale(1.02); /* Slightly grow on hover */
            box-shadow: 0 20px 50px rgba(46,204,113,0.4);
        }

        .save-food-callout h3 {
            font-size: 2.2rem; /* Larger heading */
            margin-bottom: 15px;
            font-weight: 700;
        }

        .save-food-callout p.mb-0 {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        /* How It Works Section - Dashboard Cards */
        .dashboard-card {
            background: white;
            border-radius: 15px; /* More rounded */
            padding: 30px;
            margin-bottom: 30px; /* Consistent margin */
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border-left: 8px solid var(--primary); /* Thicker border */
            color: var(--dark);
            height: 100%; /* Ensure cards are same height */
            display: flex;
            flex-direction: column;
        }

        .dashboard-card h3 {
            font-size: 2rem; /* Larger heading */
            color: var(--primary-dark); /* Use primary color for headings */
            margin-bottom: 15px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px; /* Space between number and text */
        }

        .dashboard-card h3 .icon-lg {
            width: 45px;
            height: 45px;
            font-size: 22px;
            background: var(--primary); /* Consistent icon background */
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0; /* Prevent shrinking */
        }

        .dashboard-card p {
            color: var(--dark);
            font-size: 1.1rem;
            margin-bottom: 20px;
            flex-grow: 1; /* Allow paragraph to take up available space */
        }

        .dashboard-card img {
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .dashboard-card img:hover {
            transform: scale(1.03);
        }

        /* Global button styles */
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            border-radius: 30px;
            padding: 14px 35px; /* Slightly more padding */
            font-weight: 600;
            letter-spacing: 0.7px; /* More spacing */
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(46,204,113,0.4); /* Stronger shadow */
            font-size: 1.1rem; /* Larger font */
        }

        .btn-primary:hover {
            background-color:white;
            border-color: var(--primary-dark);
            transform: translateY(-3px); /* More lift */
            box-shadow: 0 8px 25px rgba(46,204,113,0.5);
        }

        .btn-outline-primary {
            color: white; /* Changed for visibility on hero gradient */
            border-color: white; /* Changed for visibility */
            border-radius: 30px;
            padding: 14px 35px;
            font-weight: 600;
            letter-spacing: 0.7px;
            transition: all 0.3s ease;
            font-size: 1.1rem;
        }

        .btn-outline-primary:hover {
            background-color: white;
            color: var(--primary); /* Text becomes primary color on hover */
        }

        /* Badge Styles */
        .badge-primary {
            background-color: var(--primary) !important; /* Use !important to override Bootstrap if needed */
            margin-right: 8px;
            padding: 8px 15px; /* Larger padding */
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.85rem;
            color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        /* Background Light Adjustment */
        .bg-light {
            background-color: rgba(255, 255, 255, 0.1) !important; /* Glassmorphism effect */
            backdrop-filter: blur(8px); /* Increased blur */
            border-radius: 20px;
            padding: 50px; /* More padding */
            margin-bottom: 60px; /* More margin */
            color: white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15); /* More prominent shadow */
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .bg-light .section-title {
            color: white; /* Titles within bg-light should also be white for contrast */
        }

        .bg-light .waste-stat-box,
        .bg-light .dashboard-card {
            background: rgba(255, 255, 255, 0.95); /* Nearly opaque white for content cards */
            color: var(--dark);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            border: none; /* Remove border from these cards inside bg-light */
            border-bottom: 5px solid var(--primary); /* Keep the bottom border for waste stats */
            border-left: 5px solid var(--primary); /* Keep the left border for dashboard cards */
        }
        /* Specific adjustments for cards inside bg-light to ensure dark text */
        .bg-light .waste-stat-box .waste-stat-number,
        .bg-light .dashboard-card h3,
        .bg-light .dashboard-card p,
        .bg-light .list-group-item {
            color: var(--dark);
        }

        /* List Group Items */
        .list-group-item {
            border: none;
            padding: 1rem 1.25rem 1rem 50px; /* More padding and left offset for checkmark */
            position: relative;
            background: transparent;
            color: white;
            font-size: 1.1rem;
        }

        .list-group-item::before {
            content: '✓';
            position: absolute;
            left: 20px; /* Adjusted left position */
            color: var(--primary-light); /* More vibrant checkmark */
            font-weight: bold;
            font-size: 1.5rem; /* Larger checkmark */
        }

        /* Testimonial Images */
        .testimonial-img {
            width: 80px; /* Larger image */
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 20px; /* More space */
            border: 4px solid var(--primary-light); /* Thicker, colored border */
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }

        /* Footer */
        footer {
            background-color: var(--primary-dark);
            color: white;
            padding: 50px 0 30px; /* More padding */
            margin-top: 80px; /* More margin */
            text-align: center;
        }

        footer p {
            margin-bottom: 0;
            font-size: 0.95rem;
        }

        /* Conservation Tips Section */
        .tips-section {
            background: linear-gradient(45deg, #a8e6cf, #dcedc8);
            border-radius: 15px;
            padding: 30px;
            margin-top: 40px;
            text-align: center;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .tips-section h3 {
            color: var(--dark); /* Dark text for title */
            margin-bottom: 25px;
            font-size: 1.8em;
            font-weight: 700;
        }

        .tip-item {
            background: rgba(255,255,255,0.9); /* Slightly more opaque */
            border-radius: 10px;
            padding: 20px; /* More padding */
            margin: 15px 0; /* More spacing */
            border-left: 6px solid var(--primary); /* Thicker border */
            text-align: left;
            color: var(--dark); /* Dark text for tips */
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            font-size: 1.1rem;
        }
        .tip-item strong {
            color: var(--primary-dark); /* Use a primary color for strong text */
        }

        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .hero-section {
                padding: 100px 0;
            }
            .hero-section h1 {
                font-size: 3rem;
            }
            .hero-section p.lead {
                font-size: 1.2rem;
            }
            .section-title {
                font-size: 2rem;
                margin-bottom: 30px;
            }
            .feature-card {
                max-width: 90%; /* Allow cards to take more width on smaller screens */
            }
            .waste-stat-box {
                margin-bottom: 20px;
            }
            .dashboard-card {
                margin-bottom: 20px;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 15px;
            }
            .hero-section {
                padding: 80px 0;
                margin-bottom: 40px;
            }
            .hero-section h1 {
                font-size: 2.5rem;
            }
            .hero-section p.lead {
                font-size: 1rem;
            }
            .section-title {
                font-size: 1.8rem;
                margin-bottom: 25px;
            }
            .section-title::after {
                width: 50px;
                height: 3px;
                margin: 10px auto 0;
            }
            .quote-section, .tips-section {
                padding: 20px;
                margin-bottom: 30px;
            }
            .quote {
                font-size: 1.2em;
            }
            .waste-stat-number {
                font-size: 3rem;
            }
            .feature-card {
                min-height: auto; /* Allow height to adjust */
                padding: 25px;
            }
            .feature-card .icon-lg {
                width: 70px;
                height: 70px;
                font-size: 30px;
                margin-bottom: 20px;
            }
            .feature-card h3 {
                font-size: 22px;
            }
            .feature-card p {
                font-size: 15px;
            }
            .save-food-callout {
                padding: 30px;
                margin: 30px 0;
            }
            .save-food-callout h3 {
                font-size: 1.8rem;
            }
            .save-food-callout p.mb-0 {
                font-size: 1rem;
            }
            .dashboard-card {
                padding: 25px;
            }
            .dashboard-card h3 {
                font-size: 1.5rem;
            }
            .dashboard-card h3 .icon-lg {
                width: 40px;
                height: 40px;
                font-size: 20px;
            }
            .list-group-item {
                font-size: 1rem;
                padding: 0.75rem 1.25rem 0.75rem 40px;
            }
            .list-group-item::before {
                left: 15px;
                font-size: 1.2rem;
            }
            .btn-primary, .btn-outline-primary {
                padding: 10px 25px;
                font-size: 1rem;
            }
            .testimonial-img {
                width: 60px;
                height: 60px;
                margin-right: 15px;
            }
        }

        @media (max-width: 576px) {
            .container {
                padding: 10px;
            }
            body {
                padding: 10px;
            }
            .hero-section {
                padding: 60px 0;
            }
            .hero-section h1 {
                font-size: 2rem;
            }
            .hero-section p.lead {
                font-size: 0.9rem;
            }
            .section-title {
                font-size: 1.5rem;
                margin-bottom: 20px;
            }
            .btn-primary, .btn-outline-primary {
                display: block; /* Stack buttons */
                width: 100%;
                margin-right: 0 !important;
                margin-bottom: 15px;
            }
            .feature-card {
                max-width: 100%;
            }
            .dashboard-card img {
                width: 100% !important; /* Make images fill card width on small screens */
                height: auto;
            }
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<section class="hero-section">
    <div class="container">
        <h1 class="display-3 mb-4">Smart Meal Management for a Sustainable Campus</h1>
        <p class="lead mb-5">Revolutionizing campus dining to reduce waste and cater to every student's needs.</p>
        <?php if (isset($_SESSION['student_id'])): ?>
            <a href="dashboard.php" class="btn btn-primary btn-lg px-5 py-3 mr-3">
                Go to Dashboard
            </a>
        <?php else: ?>
            <a href="register.php" class="btn btn-primary btn-lg px-5 py-3 mr-3">
                Student Sign Up
            </a>
            <a href="login.php" class="btn btn-outline-primary btn-lg px-5 py-3">
                Login
            </a>
        <?php endif; ?>
    </div>
</section>

<div class="container">
    <div class="quote-section">
        <div class="quote" id="quote-text">"Food is precious. Waste it not, want it not."</div>
        <div class="quote-author" id="quote-author">- Smart Mess Community</div>
    </div>

    <section class="py-4 my-4">
        <div class="row text-center">
            <div class="col-12 mb-4">
                <h2 class="section-title text-center">Campus Food Waste Impact</h2>
            </div>
            <div class="col-md-4 d-flex"> <div class="waste-stat-box">
                    <span class="waste-stat-number">40%</span>
                    <p>Average reduction in campus food waste</p>
                </div>
            </div>
            <div class="col-md-4 d-flex">
                <div class="waste-stat-box">
                    <span class="waste-stat-number">₹3.5L</span>
                    <p>Monthly savings on food costs</p>
                </div>
            </div>
            <div class="col-md-4 d-flex">
                <div class="waste-stat-box">
                    <span class="waste-stat-number">2,500kg</span>
                    <p>Food saved monthly by our system</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 my-5">
        <h2 class="section-title text-center">Key Features</h2>
        <div class="row justify-content-center">
            <div class="col-md-4 d-flex">
                <div class="feature-card">
                    <div class="card-body text-center p-4">
                        <div class="icon-lg"><i class="fas fa-bell"></i></div> <h3>Automated Reminders</h3>
                        <p>Get notifications before each meal to confirm your attendance, ensuring accurate counts.</p>
                        <div class="mt-3">
                            <span class="badge badge-pill badge-primary">SMS</span>
                            <span class="badge badge-pill badge-primary">Email</span>
                            <span class="badge badge-pill badge-primary">App</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 d-flex">
                <div class="feature-card">
                    <div class="card-body text-center p-4">
                        <div class="icon-lg"><i class="fas fa-brain"></i></div> <h3>AI Predictions</h3>
                        <p>Smart forecasting of meal attendance using advanced AI to optimize food preparation and minimize waste.</p>
                        <div class="mt-3">
                            <span class="badge badge-pill badge-primary">Machine Learning</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 d-flex">
                <div class="feature-card">
                    <div class="card-body text-center p-4">
                        <div class="icon-lg"><i class="fas fa-chart-line"></i></div> <h3>Live Dashboard</h3>
                        <p>Real-time tracking of meal attendance and consumption patterns for efficient mess management.</p>
                        <div class="mt-3">
                            <span class="badge badge-pill badge-primary">Real-time Data</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-3">
        <div class="save-food-callout">
            <h3>Every Meal Update Helps Save Food</h3>
            <p class="mb-0">By simply marking your attendance, you directly contribute to reducing food waste and fostering a more sustainable campus!</p>
        </div>
    </section>

    <section class="py-5 my-5 bg-light">
        <div class="container">
            <h2 class="section-title text-center">How It Works</h2>
            <div class="row align-items-stretch"> <div class="col-md-6 d-flex">
                    <div class="dashboard-card">
                        <h3><span class="icon-lg">1</span> Plan Your Meals</h3>
                        <p>Use our intuitive calendar interface to easily mark which meals you'll attend or skip. Our system is flexible, understanding that student needs can vary.</p>
                        <img src="https://media.istockphoto.com/id/1472185863/photo/desk-calendar-on-table-with-blurred-bokeh-background-appointment-and-business-meeting-concept.jpg?b=1&s=170667a&w=0&k=20&c=pphUVYXa-0A3uANaP1xBrhsP5VbbjYs7ocFLsjPG-Mk=" alt="Meal Calendar" class="img-fluid mt-auto"> </div>
                </div>
                <div class="col-md-6 d-flex">
                    <div class="dashboard-card">
                        <h3><span class="icon-lg">2</span> Get Reminders</h3>
                        <p>Receive timely, automated notifications before each meal to confirm your plans. This ensures you never forget to update your status, making the system highly accurate.</p>
                        <img src="https://t4.ftcdn.net/jpg/00/26/99/69/360_F_26996933_aHDs1FQ9TXvHSEC8U5bZwWEiimNmDzNd.jpg" alt="Notification" class="img-fluid mt-auto"> </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 my-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 class="section-title text-center text-md-left">Accommodating Special Needs</h2>
                <p>We understand that students may need to be away for extended periods due to various reasons:</p>
                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item">Illness or medical treatment</li>
                    <li class="list-group-item">Family emergencies</li>
                    <li class="list-group-item">Academic trips or internships</li>
                    <li class="list-group-item">Personal circumstances or leave</li>
                </ul>
                <p>Our system operates without arbitrary skip limits. Instead, we utilize **intelligent forecasting** to accommodate all situations while effectively minimizing food waste and ensuring no student is penalized for genuine absences.</p>
            </div>
            <div class="col-md-6 text-center text-md-right mt-4 mt-md-0">
                <img src="https://media.istockphoto.com/photos/down-syndrome-boy-with-headset-doing-thumbs-up-picture-id535401785?k=6&m=535401785&s=612x612&w=0&h=ns4LO7u6fC6wpvFqHmAq2mevKg76nyc1zAt9E8k1Qf4=" alt="Special Needs Accommodation" class="img-fluid rounded shadow-lg">
            </div>
        </div>
    </section>

    <section class="py-5 my-5 bg-light">
        <div class="container">
            <h2 class="section-title text-center">What Our Community Says</h2>
            <div class="row">
                <div class="col-md-4 d-flex">
                    <div class="dashboard-card">
                        <div class="media">
                            <img src="https://as2.ftcdn.net/v2/jpg/02/76/74/69/1000_F_276746961_AQhKGzozQOWJnXzCem4414wSCXvZaj9a.jpg" class="testimonial-img align-self-start" alt="Priya, 2nd Year">
                            <div class="media-body">
                                <p class="mb-2">"When I was sick for 3 weeks, I didn't have to worry about meal penalties. The system just adjusted seamlessly."</p>
                                <p class="font-weight-bold mb-0">— Priya, 2nd Year Student</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 d-flex">
                    <div class="dashboard-card">
                        <div class="media">
                            <img src="https://cdn.prod.website-files.com/5fbb9b89508062592a9731b1/6448c1ce35d6ffe59e4d6f46_GettyImages-1399565382.jpg" class="testimonial-img align-self-start" alt="Mr. Sharma, Mess Manager">
                            <div class="media-body">
                                <p class="mb-2">"The AI predictions are incredibly accurate, helping us reduce food waste by 40% while consistently having enough for everyone."</p>
                                <p class="font-weight-bold mb-0">— Mr. Sharma, Mess Manager</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 d-flex">
                    <div class="dashboard-card">
                        <div class="media">
                            <img src="https://img.freepik.com/premium-photo/indian-college-boy-happy-smiling-college_437792-732.jpg" class="testimonial-img align-self-start" alt="Rahul, 3rd Year">
                            <div class="media-body">
                                <p class="mb-2">"The automated reminders are so convenient – I can update my meal status directly from the notification!"</p>
                                <p class="font-weight-bold mb-0">— Rahul, 3rd Year Student</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="tips-section">
        <h3>💡 Food Conservation Tips</h3>
        <div class="tip-item">
            <strong>Plan Ahead:</strong> Cancel meals at least 24 hours in advance whenever possible to assist the mess staff in planning and reducing preparation waste.
        </div>
        <div class="tip-item">
            <strong>Communicate Effectively:</strong> If you're going out or have group plans, coordinate with your friends to inform the mess collectively about your attendance.
        </div>
        <div class="tip-item">
            <strong>Be Mindful:</strong> Only cancel when you are certain you will not be dining at the mess. Your accurate updates make a significant difference!
        </div>
    </div>
</div>

<section class="py-5">
    <div class="container text-center">
        <h2 class="section-title">Ready to Join?</h2>
        <p class="lead mb-4">Become a part of our mission to create a more sustainable and efficient campus dining system.</p>
        <div class="stat-counter mb-4 text-primary" style="font-size: 3rem; font-weight: 700;">40% Food Waste Reduction</div> <?php if (isset($_SESSION['student_id'])): ?>
            <a href="dashboard.php" class="btn btn-primary btn-lg px-5 py-3">
                Go to Your Dashboard
            </a>
        <?php else: ?>
            <a href="register.php" class="btn btn-primary btn-lg px-5 py-3 mr-3">
                Register Now
            </a>
            <a href="login.php" class="btn btn-outline-primary btn-lg px-5 py-3">
                Login
            </a>
        <?php endif; ?>
    </div>
</section>

<footer>
    <div class="container text-center">
        <p> MessMaintenance - Smart Campus</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzmrkfjJgprL/eS0qGVuXuYwWqjoPyfFxU/d-V8E/KqsiJChy/C8K5" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" integrity="sha384-Fy6S38WX9/wA+3W2Z70kM0u+uNcgyGjFNPZQuNdgxS7L/c7s5gYV0p63a8B5S6Y8b" crossorigin="anonymous"></script>

<script>
    // Food conservation quotes
    const quotes = [
        { text: "Food is precious. Waste it not, want it not.", author: "Smart Mess Community" },
        { text: "Every grain saved is a step towards sustainability.", author: "Environmental Wisdom" },
        { text: "The best way to reduce food waste is to plan ahead.", author: "Sustainable Living" },
        { text: "A meal saved is a meal earned for someone in need.", author: "Food Conservation Society" },
        { text: "Small actions, big impact - every cancelled meal counts.", author: "Eco Warriors" },
        { text: "Respect food, respect resources, respect the planet.", author: "Green Living" },
        { text: "Planning meals is planning for a better tomorrow.", author: "Future Generation" },
        { text: "Food waste is a waste of resources, time, and effort.", author: "Mindful Eating" }
    ];
    let currentQuoteIndex = 0;

    // Rotate quotes every 5 seconds
    function rotateQuotes() {
        const quoteText = document.getElementById('quote-text');
        const quoteAuthor = document.getElementById('quote-author');

        if (!quoteText || !quoteAuthor) {
            console.warn("Quote elements not found. Quote rotation will not function.");
            return;
        }

        // Add a class for fade-out
        quoteText.classList.add('fade-out');
        quoteAuthor.classList.add('fade-out');

        setTimeout(() => {
            currentQuoteIndex = (currentQuoteIndex + 1) % quotes.length;
            quoteText.textContent = `"${quotes[currentQuoteIndex].text}"`;
            quoteAuthor.textContent = `- ${quotes[currentQuoteIndex].author}`;

            // Remove fade-out and add fade-in
            quoteText.classList.remove('fade-out');
            quoteAuthor.classList.remove('fade-out');
            // Re-adding a class or forcing reflow can sometimes help if the transition doesn't re-trigger
            void quoteText.offsetWidth; // Trigger reflow
            quoteText.classList.add('fade-in');
            quoteAuthor.classList.add('fade-in');

        }, 300); // This should match the CSS transition duration

        // After the animation, remove the fade-in class so it's ready for the next cycle
        setTimeout(() => {
            if (quoteText) quoteText.classList.remove('fade-in');
            if (quoteAuthor) quoteAuthor.classList.remove('fade-in');
        }, 600); // Longer than the transition to ensure it's removed
    }

    // Initial call to set the first quote and start rotation
    document.addEventListener('DOMContentLoaded', () => {
        // Add CSS for fade transitions dynamically if needed, or ensure it's in the <style> block
        const style = document.createElement('style');
        style.innerHTML = `
            .quote.fade-out, .quote-author.fade-out {
                opacity: 0;
                transition: opacity 0.3s ease-out;
            }
            .quote.fade-in, .quote-author.fade-in {
                opacity: 1;
                transition: opacity 0.3s ease-in;
            }
        `;
        document.head.appendChild(style);

        // Set initial quote immediately
        const quoteText = document.getElementById('quote-text');
        const quoteAuthor = document.getElementById('quote-author');
        if (quoteText && quoteAuthor) {
            quoteText.textContent = `"${quotes[currentQuoteIndex].text}"`;
            quoteAuthor.textContent = `- ${quotes[currentQuoteIndex].author}`;
            quoteText.style.opacity = '1'; // Ensure initial quote is visible
            quoteAuthor.style.opacity = '1';
        }

        // Start quote rotation
        setInterval(rotateQuotes, 5000);
    });
</script>

</body>
</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Tahoma, Geneva, Verdana, sans-serif;
          
        }

        .container-fluid {
            padding: 0;
       
        }

        .header {
            background-color: rgb(255, 255, 255);
            border-bottom: 1px solid black;
            display: flex;
            align-items: center;
            padding: 10px 0;
            width: 100%;
        }

        .header .logo {
            flex: 1;
        }

        .header .logo img {
            height: 80px;
            margin: 4px;
            transition: transform 0.7s ease-in-out;
        }

        .header .logo img:hover {
            transform: scale(1.1);
        }

        .login-btn {
            background-color: #dc3545;
            color: white;
            padding: 5px 15px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .login-btn:hover {
            background-color: rgb(242, 5, 29);
            color: black;
        }

        .register-container {
            max-width: 900px;
            margin: 20px auto;
            background-color: #fff;
            padding: 30px;
            border:1px solid black;
            box-shadow: 1px 2px 10px rgba(0, 0, 0, 0.1);
            
        }

        .input-group-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .input-group {
            flex: 0 0 calc(50% - 20px);
            margin-bottom: 15px;
        }

        .input-group input,
        .input-group select {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            box-sizing: border-box;
            border-radius: 4px;
            transition: border-color 0.3s;
        }

        button {
            background-color: #333;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out;
        }

        button:hover {
            background-color: #555;
        }

        .animated-button {
            animation: changecolor 1.5s infinite;
            margin-right: 20px;
        }

        @keyframes changecolor {
            0% {
                transform: scale(1);
                background-color: gray;
            }

            50% {
                transform: scale(1.2);
                background-color: red;
            }

            100% {
                transform: scale(1);
                background-color: green;
            }
        }

        footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 10px 0;
            position: relative;
            bottom: 0;
            width: 100%;
        }

        footer a {
            color: #ffc107;
            text-decoration: none;
            font-weight: bold;
        }

        footer a:hover {
            text-decoration: underline;
        }

        a {
            color: blue;
            text-decoration: none;
            font-weight: bold;
        }

        h1 {
            font-size: 36px;
            color: black;
            text-align: left;
            margin-top: 20px;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .input-group {
                flex: 0 0 100%;
            }

            .register-container {
                padding: 20px;
            }
        }


        @media (max-width: 768px) 
        {
  

    


    .header .logo img {
        max-width: 100%;
        margin: 0 auto;
        padding:0px;
    }

   
}

    </style>
</head>

<body>

    <div class="container-fluid">
        <!-- Header -->
        <header class="header">
            <div class="logo">
                <a href="https://moderncollegepune.edu.in/">
                    <img src="photos/modernlogo.png" alt="Modern College Logo">
                </a>
            </div>
            <div>
                <a href="loginpage.html" style="margin:34px;" class="login-btn">Login</a>
            </div>
        </header>

        <!-- Content goes here -->
        <section>
            <div class="register-container">
                <h2>Register</h2>

                <form method="post" enctype="multipart/form-data" action="tempregister.php">
                    <div class="input-group-container">
                        <div class="input-group">
                            <label for="enrollment">Enrollment Number:</label>
                            <input type="text" id="enrollment" name="enrollment" placeholder="Enrollment number provided by college" pattern="[0-9]{5,}" title="Enrollment number must be more than 5 digits." required>
                        </div>
                        <div class="input-group">
                            <label for="fullname">Full Name:</label>
                            <input type="text" id="fullname" name="fullname" placeholder="As per Leaving Certificate" required>
                        </div>
               <div class="input-group">
    <label for="password">Password:</label>
    <input type="password" id="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_])[0-9A-Za-z\W_]{8,}" title="Password must contain at least 8 characters, including at least one uppercase letter, one lowercase letter, one digit, and one special character." name="password" placeholder="Use Digit, Symbols, Alphabets" required>
</div>
<div class="input-group">
    <label for="confirm_password">Confirm Password:</label>
    <input type="password" id="confirm_password" name="confirm_password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_])[0-9A-Za-z\W_]{8,}" title="Password must contain at least 8 characters, including at least one uppercase letter, one lowercase letter, one digit, and one special character." placeholder="Match Correct password" required>
</div>


                        <div class="input-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" placeholder="student123@gmail.com" required>
                        </div>
                        <div class="input-group">
                            <label for="phone">Phone Number:</label>
                            <input type="tel" id="phone" name="phone" maxlength="10" placeholder="+91 1234567890" pattern="[0-9]{10}" title="Phone number must be 10 digits." required>
                        </div>
                        <div class="input-group">
                            <label for="gender">Gender:</label>
                            <select name="gender" required>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label for="parent_name">Parent's Name:</label>
                            <input type="text" id="parent_name" placeholder="Parent Name" name="parent_name" required>
                        </div>
                        <div class="input-group">
                            <label for="address">Address:</label>
                            <input type="text" id="address" name="address" placeholder="Enter permanent address" required>
                        </div>
                        <div class="input-group">
                            <label for="course">Course:</label>
                            <select name="course" required>
                                <?php
                                    // Establish database connection
                                    include 'dbconnect.php';
                                    $sql = "SELECT course_name FROM public.course";
                                    $result = pg_query($conn, $sql);

                                    while ($row = pg_fetch_assoc($result)) {
                                        echo "<option value='" . $row['course_name'] . "'>" . $row['course_name'] . "</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="input-group">
                            <label for="acdemicyear">Select Academic Year:</label>
                            <select name="acdemicyear">
                                <option value="2022-2023">2022-2023</option>
                                <option value="2023-2024">2023-2024</option>
                                <option value="2024-2025">2024-2025</option>
                                <option value="2025-2026">2025-2026</option>
                                <option value="2026-2027">2026-2027</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label for="semester">Semester:</label>
                            <select name="semester" required>
                                <option value="Semester 1">Semester 1</option>
                                <option value="Semester 2">Semester 2</option>
                                <option value="Semester 3">Semester 3</option>
                                <option value="Semester 4">Semester 4</option>
                                <option value="Semester 5">Semester 5</option>
                                <option value="Semester 6">Semester 6</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label for="dob">Date of Birth:</label>
                            <input type="date" id="dob" name="dob" required>
                        </div>
                        <div class="input-group">
                            <label for="photo">Add Passport Photo:</label>
                            <input type="file" id="photo" name="photo" required>
                        </div>
                        <div class="input-group">
                            <label for="fee">Upload Fee Receipt:</label>
                            <input type="file" id="fee" name="file" required>
                        </div>
                    </div>
                    <button type="submit" class="animated-button">Register</button>
                    <button type="reset">Reset</button>
                </form>
                <p>Already have login details? <a href="loginpage.html">Login Here..</a>.</p>
            </div>
        </section>

        <!-- Footer -->
        <footer>
            <p>For more information, visit <a href="#" target="_blank">Our Team</a></p>
        </footer>
    </div>

</body>

</html>

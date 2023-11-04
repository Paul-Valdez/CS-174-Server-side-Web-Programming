<?php
    // Start output buffering
    ob_start();

    // HTML code
    echo <<<_END
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <title>Assignment #5 - Authentication and Cookies</title>
                <meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                
                <style>
                    // CSS styling
                    .custom-font {
                        font-family: 'Courier';
                    }

                    table {
                        border-collapse: collapse;
                    }
                    
                    td, th {                        
                        text-align: left;
                        border: 1px solid black;
                        width: 100px;  // Width of the cell
                        height: 100px; // Height of the cell for perfect squares
                        line-height: 30px; // To vertically align content in the middle
                    }                    

                    .stretch-table {
                        display: flex;
                        flex-direction: column;
                        width: 100%;
                        border: 1px solid black;
                    }
                    
                    .stretch-table tr {
                        display: flex;
                        border-bottom: 1px solid black;
                    }
                    
                    .stretch-table td, .stretch-table th {
                        flex: 0 0 100px;
                        text-align: left;
                        border: 1px solid black;
                        line-height: 30px;
                        padding: 5px 0;
                        border: none;
                        border-right: 1px solid black;
                        margin-left: 10px;
                    }

                    .stretch-table td:nth-child(1), .stretch-table th:nth-child(1) {
                        flex: 0 0 50px;
                    }

                    .stretch-table td:nth-child(2), .stretch-table th:nth-child(2) {
                        flex: 0 0 200px;
                    }                    
                                        
                    .stretch-table td:last-child, .stretch-table th:last-child {
                        flex: 1;  // allows the last column to stretch to fill up available space
                        border-bottom: none;
                        border-right: none;
                    }

                    .stretch-table tr::after {
                        content: '';
                        display: block;
                        height: 10px;
                    }

                    .container {
                        display: flex;
                        justify-content: space-between;
                    }

                    .output {
                        flex: 1;
                        padding: 20px;
                    }

                    textarea#comment {
                        width: 420px;
                        height: 69px;
                        padding: 10px;
                        margin-bottom: 10px;
                        resize: vertical;
                        font-size: 1em;
                        border: 1px solid #ccc;
                        border-radius: 4px;
                    }

                </style>                
            </head>

            <body>
                <div class="container">
                    <div class="output">
    _END;

/*
    // Queries ran in MySQL client:
    // DATABASE NAME MUST MATCH $database IN login.php

    CREATE DATABASE CS174_A5;
    USE CS174_A5;

    CREATE TABLE UserCredentials (
        username VARCHAR(32) NOT NULL,
        password VARCHAR(32) NOT NULL,
        name VARCHAR(32) NOT NULL,
        PRIMARY KEY (username)
    );

    CREATE TABLE UserComments (
        username VARCHAR(32) NOT NULL,
        comment TEXT NOT NULL,
        FOREIGN KEY (username) REFERENCES UserCredentials(username)
        ON DELETE CASCADE
    );
*/

    //* THIS PROGRAM ASSUMES THAT THE DATABASE AND TABLES HAVE ALREADY BEEN CREATED *//

    // MySQL connection through PHP
    require_once 'login.php';

    $conn = new mysqli($hostname, $username, $password, $database);
    if ($conn->connect_error) die(mysql_fatal_error("Cannot connect to database.", $conn));

    if (!isset($_COOKIE['name'])) {
        // Print hello message
        echo "<h1>Hello!</h1>";

        // Forms for sign-up and login
        echo <<<_END
        <h2>Sign-Up</h2>
        <form method="post" action="" enctype="multipart/form-data" id="signupForm">
            <input type="hidden" name="form_type" value="sign-up">
            <label for="nameInput">Enter your name:</label>
            <input type="text" id="nameInput" name="name" required>
            <br>
            
            <label for="usernameSignup">Enter your username:</label>
            <input type="text" id="usernameSignup" name="username" required>
            <br>

            <label for="passwordSignup">Enter your password:</label>
            <input type="password" id="passwordSignup" name="password" required>
            <br>

            <label for="passwordConfirm">Confirm your password:</label>
            <input type="password" id="passwordConfirm" name="passwordConfirm" required>
            <br>

            <input type="submit" value="Sign-Up">
        </form>
        <br>
    
        <h2>Login</h2>
        <form method="post" action="" enctype="multipart/form-data">
            <input type="hidden" name="form_type" value="login">
            <!-- Other input fields -->
            <label for="usernameLogin">Username:</label>
            <input type="text" id="usernameLogin" name="username" required>
            <br>

            <label for="passwordLogin">Password:</label>
            <input type="password" id="passwordLogin" name="password" required>
            <br>

            <input type="submit" value="Login">
        </form>

        <script>
            document.getElementById('signupForm').addEventListener('submit', function(event) {
                var password = document.getElementById('passwordSignup').value;
                var confirmPassword = document.getElementById('passwordConfirm').value;
                
                if (password !== confirmPassword) {
                    alert('Passwords do not match.');
                    event.preventDefault();
                }
            });
        </script>

        _END;
    }
    else{
        // Print hello message with name from cookie
        echo "<h1>Hello, <u>" . $_COOKIE['name'] . "</u>!</h1>";

        // Logout button and comment form
        echo <<<_END
        <form method="post">
            <input type="hidden" name="form_type" value="logout">
            <input type="submit" value="Logout"/>
        </form>
        <br>

        <form method="post" action="" enctype="multipart/form-data">
            <label for="comment">Enter a comment:</label><br>
            <textarea id="comment" name="comment" required></textarea><br>
            <input type="hidden" name="form_type" value="comment">
            <input type="submit" value="Submit">
        </form>
        <br>
        _END;

        // Print the user's comments
        print_comments($conn);
    }


    // checks if the HTTP request method is POST
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST['form_type'])) {

            // Sign-Up
            if ($_POST['form_type'] === 'sign-up' && 
                isset($_POST['name'], $_POST['username'], $_POST['password'], $_POST['passwordConfirm'], $_POST['name'])) {

                // Call function to insert the username and password for sign-up
                sign_up($conn, $_POST['username'], $_POST['password'], $_POST['name']);
            }

            // Login
            elseif ($_POST['form_type'] === 'login' && isset($_POST['username'], $_POST['password'])) {
                // Call function to login
                login($conn, $_POST['username'], $_POST['password']);
                header("Refresh:1"); // Refresh the page in 1 second
            }

            // Add Comment
            elseif($_POST['form_type'] === 'comment' && isset($_POST['comment'])){
                // Call function to add comment
                comment($conn, $_POST['comment']);
            }

            // Logout
            elseif($_POST['form_type'] === 'logout'){
                // Call function to logout
                logout($conn);
            }
        }
    }

    // Close the db connection
    $conn->close();

    echo "</div>";  // close output div
    echo "</div>";  // close container div
    echo "</body></html>"; // close body and html

    ////////////////////////* END PROGRAM *////////////////////////


    ////////////////////////* FUNCTIONS *////////////////////////

    function print_comments($conn){
        // Database query to select respective username comments
        $query = "SELECT username FROM UserCredentials WHERE name = ?";

        // prepared statements for select
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $_COOKIE['name']);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result) {
            mysql_fatal_error('User not found.', $conn);
            return;
        }

        if ($result->num_rows === 0) {
            echo "No user found with the given name.";
            return;
        }

        $row = $result->fetch_assoc();
        $username = $row['username'];
        $result->close();

        // PRINT TABLE ENTRIES //
        $query = "SELECT comment FROM UserComments WHERE username = ?"; // Adjusted to select only the 'comment' column
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result) {
            mysql_fatal_error('An error occurred while retrieving comments.', $conn);
            return;
        }
        
        // Check if the number of comments is greater than 0
        if ($result->num_rows > 0) {
            echo "<table class='stretch-table'>";
            echo "<tr>";
            echo "<th>Comment</th>";
            echo "</tr>";
            echo "<table class='stretch-table'>";

            // Output data
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['comment']) . "</td>"; // Output the 'comment' column
                echo "</tr>";
            }
        
            echo "</table>";
        } else {
            echo "No comments found."; // Message to be displayed if no comments
        }

        echo "</table>";

        $result->close();
    }

    function comment($conn, $comment){
        // Sanitize comment //
        $sanitized_comment = html_entities_fix_string($comment);
        $sanitized_comment = mysql_fix_string($conn, $sanitized_comment);

        // Database query to select respective username
        $query = "SELECT username FROM UserCredentials WHERE name = ?";

        // prepared statements for select
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $_COOKIE['name']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if (!$result) {
            mysql_fatal_error('User not found.', $conn);
        }

        $row = $result->fetch_assoc();
        $result->close();
        $username = $row['username'];

        // Database query to insert new comment
        $query = "INSERT INTO UserComments (username, comment) VALUES(?, ?)";
        // prepared statements for insertion
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ss', $username, $sanitized_comment);
        $stmt->execute();
        $stmt->close();

        header("Refresh:0"); // Refresh the page
    }

    // Logs user out by unsetting cookie
    function logout(){
        // Get the current name value of the cookie
        $name = $_COOKIE['name'];

        // Unset cookie using a past date
        $one_month = 2592000; // 30 days
        setcookie('name', $name, time() - $one_month, '/');
        unset($_COOKIE['name']);

        echo "<br><h3>Logging out</h3><br>";

        header("Refresh:1"); // Refresh the page in 1 second
        ob_end_clean(); // End  output buffering
    }

    // Sets the cookie for user inputted name
    function set_cookie($name, $conn) {
        // Sanitize name //
        // Remove any characters that are not letters, numbers, or spaces
        $sanitized_name = preg_replace("/[^a-zA-Z0-9 ]/", "", $name);
        $sanitized_name = html_entities_fix_string($sanitized_name);
        $sanitized_name = mysql_fix_string($conn, $sanitized_name);
        $one_week = 60 * 60 * 24 * 7;

        // Set the cookie with the sanitized name
        setcookie('name', $sanitized_name, time() + $one_week, '/', '', false, true);  // expires in 7 days
    }

    // Login user
    function login($conn, $username, $password){
        // Sanitize username and password //
        $sanitized_username = html_entities_fix_string($username);
        $sanitized_username = mysql_fix_string($conn, $sanitized_username);

        $query = "SELECT * FROM UserCredentials WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $sanitized_username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if (!$result)
            die(mysql_fatal_error("User not found; cannot login.", $conn));
        else if ($result->num_rows)
        {
            $row = $result->fetch_array(MYSQLI_NUM);
            $result->close();
                    
            // Only sanitizing for MySQL because password will not be displayed via HTML
            $sanitized_password = mysql_fix_string($conn, $password);

            // Hash and salt the password to locate hashed password in database table
            $hashedsaltedPW = hashnsalt($sanitized_password);

            if($hashedsaltedPW == $row[1]){
                echo "<br><h3>Logging in as user: <b>" . htmlspecialchars($row[0], ENT_QUOTES, 'UTF-8') . "</h3></b><br>";

                $sanitized_username = html_entities_fix_string($username);
                $sanitized_username = mysql_fix_string($conn, $sanitized_username);

                $query = "SELECT name FROM UserCredentials WHERE username = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('s', $sanitized_username);
                $stmt->execute();
                $result = $stmt->get_result();

                if (!$result) {
                    mysql_fatal_error('Name not found.', $conn);
                }

                // Fetch the name from the result set
                if ($row = $result->fetch_assoc()) {
                    $name = $row['name'];
                } 

                // Set the cookie for the current user/name
                set_cookie($name, $conn);

                //return $username;
            }
            else
                die(mysql_fatal_error("Invalid username/password combination", $conn));
        }
        else
            die(mysql_fatal_error("Invalid username/password combination", $conn));
    }

    // Hash and salt the password to store securely
    function hashnsalt($sanitized_password){
        $saltedPW = "w5up," . $sanitized_password . "D0u9!";
        return hash('ripemd128', $saltedPW);
    }

    // Inserts the username and password from the sign-up form into the database table UserCredentials
    function sign_up($conn, $username, $password, $name){
        // Sanitize username
        $sanitized_username = html_entities_fix_string($username);
        $sanitized_username = mysql_fix_string($conn, $sanitized_username);

        // Check if the username already exists in the database
        $query = "SELECT * FROM UserCredentials WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $sanitized_username);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            // Username already exists
            echo "<br><h3>Username is already taken.</h3><br>";
            return false; // Return false to indicate failure
        }
      
        // Only sanitizing for MySQL because password will not be displayed via HTML
        $sanitized_password = mysql_fix_string($conn, $password);

        // Hash and salt the password to store securely
        $hashedsaltedPW = hashnsalt($sanitized_password);

        // Sanitize name
        $sanitized_name = html_entities_fix_string($name);
        $sanitized_name = mysql_fix_string($conn, $sanitized_name);

        // Database query to insert new user
        $query = "INSERT INTO UserCredentials (username , password, name) VALUES(?, ?, ?)";

        // prepared statements for insertion
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sss', $sanitized_username, $hashedsaltedPW, $sanitized_name);
        $stmt->execute();
        $stmt->close();

        echo "<br><h3>Account created!</h3>";
        return true;
    }

    //* sanitization *//
    // This function is used to escape strings before using them in an SQL query
    function mysql_fix_string($conn, $string) {
        return $conn->real_escape_string($string);
    }

    // This function is used to convert special characters to HTML entities before outputting them to HTML
    function html_entities_fix_string($string) {
        return htmlentities($string, ENT_QUOTES, 'UTF-8');
    }

    // Fatal error message! oh noes
    function mysql_fatal_error($msg, $conn){
        $msg2 = mysqli_error($conn);

        echo <<< _END

        We are sorry, but it was not possible to complete
        the requested task. The error message we got was:

        <p>$msg:$msg2</p>

        Please click the back button on your browser
        and try again. If you are still having problems,
        please <a href="mailto:admin@server.com">email
        our administrator</a>. Thank you.
        _END;
    }
?>
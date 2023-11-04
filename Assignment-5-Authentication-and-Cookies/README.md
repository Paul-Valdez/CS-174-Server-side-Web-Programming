# Assignment #5 - Authentication and Cookies
##### Due: November 2, 2023
You need to implement a web application that is split into three parts, namely, Webpage, PHP, and MySQL. Each of them will be used concurrently to solve the problem described below. Remember to implement the logic in the most secure way to your knowledge.

**PHP**

-   Implement a PHP function that reads in input a string (user's name, not username) from the user and stores it in a cookie.
-   Implement another function to read another string (a comment) in input and store it in a database (only available if the user logged in).
-   The web application should be able to implement the logic to let  **log in and sign up**  the users.
    -   Each user will have exclusive access to their uploaded comments.
    -   When a user logs in, all their private content will be displayed on the web page.
        -   If no user has logged in yet, no information from the database is printed on the webpage.

**Webpage**

-   The page should hail the student with the message "Hello!"
-   If the user has logged in, the message should be "Hello " plus the name of the user (using cookie to get the user's name).
-   During sign-up, the user must be able to input a string (user's name) using a text box, username, and password (no need to validate username and password).
-   Once logged in, the user must be able to input a string (a comment), using a text box.
-   The webpage allows users to input their credentials for both logging in and signing up (use a single page for both).
-   After a user logs in, the webpage prints their personal comments from the database.
    -   If there are no comments yet, nothing is shown for that specific user.

**MySQL**

-   You need to create a database that contains at least two tables. One to store the information in input to the webpage, the other to store the users' credentials.  
    -   The "credentials table" should contain at least these fields: username and password.
    -   The password should be stored in the most secure way to your knowledge.

**SUBMISSION**

-   You need to submit your web application in a .php file, no other format is allowed.
-   You will submit your 'login.php' file too.
-   No details about the database need to be submitted. The tables' information can be inferred by the PHP code already.
-   Provide sufficient screenshots of how your application works (Ex: Before and after signing up, logging in, uploading comments, etc.).
-   You may submit a zip archive file.

# Midterm #1
#####  Due:  September 28, 2023
Using a combination of HTML/PHP, implement a web page where the users can upload a text file, exclusively in .txt format, which contains a string of 400 numbers, such as:
```
71636269561882670428  
85861560789112949495  
65727333001053367881  
52584907711670556013  
53697817977846174064  
83972241375657056057  
82166370484403199890  
96983520312774506326  
12540698747158523863  
66896648950445244523  
05886116467109405077  
16427171479924442928  
17866458359124566529  
24219022671055626321  
07198403850962455444  
84580156166097919133  
62229893423380308135  
73167176531330624919  
30358907296290491560  
70172427121883998797
```
Your code should contain a PHP function that, accepting the string of 400 numbers in input, is able to find the greatest product of four adjacent numbers in all the four possible directions (up, down, left, right, or diagonally).

NOTE:

-   You will need to arrange the numbers in a grid of 20x20.
-   If the file doesn't contain 400 numbers or contains some characters in between, it should change them to 0, and your application should inform the user that the file was not formatted correctly.
-   You can ignore new lines and white spaces.
-   Add a tester function to check the behavior of your PHP function.
-   Follow the guidelines discussed and shown in class to avoid losing points.

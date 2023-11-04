<?php
    // HTML code with file upload form
    echo <<<_END
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <title>Midterm #1 - Largest Product of Four Factors in 20x20</title>
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
                        //margin: 100px 0;
                        //position: fixed;
                        //left: 350px;  // Adjust this to move the table horizontally
                    }
                
                    td, th {                        
                        text-align: center;
                        border: 1px solid black;
                        width: 25px;  // Width of the cell
                        height: 30px; // Height of the cell for perfect squares
                        line-height: 30px; // To vertically align content in the middle
                    }

                    .container {
                        display: flex;
                        justify-content: space-between;
                    }

                    .output {
                        flex: 1;
                        padding: 20px;
                    }
                </style>
            </head>

            <body>
                <div class="container">
                    <div class="output">
                        <form method="post" action="" enctype="multipart/form-data">
                            Select a .TXT File: <input type="file" name="filename" size="10" required>
                            <br>
                            <input type="submit" value="Upload">
                        </form>
    _END;


    const GRID_SIZE = 20;
    const NUMBER_OF_FACTORS = 4;


    // checks if the HTTP request method is POST
    // if the form has been submitted using the POST method, then perform the computations            
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES['filename'])) {
        $sanitizedFileTempName = htmlentities($_FILES["filename"]["tmp_name"]);
        $fileName = htmlentities($_FILES["filename"]["name"]);
        $mime = $_FILES["filename"]["type"];

        if ($mime === "text/plain") {
            $contents = htmlentities(file_get_contents($sanitizedFileTempName));

            if (!mb_check_encoding($contents, 'UTF-8')) {
                echo "Invalid file content: '$fileName'. Please try again.<br>";
            } else {
                echo "Uploaded: '" . basename($fileName) . "'<br><br>";

                // fill the grid using the uploaded file
                $gridUploaded = fillGrid($sanitizedFileTempName);

                // call the function to determine the largest product
                echo "Largest Product: " . largestProduct($gridUploaded);
            }
        } else {
            echo "Invalid file type: '$fileName'. Please try again.<br>";
        }
    }

    // call test function
    tester_function();

    echo "</div>";  // close output div
    echo "</div>";  // close container div
    echo "</body></html>"; // close body and html


    
    // function that fills a 20x20 2-D array by reading from a file
    function fillGrid($path){
        $grid = [];
        $file = fopen($path, "r+");
        $formatErrorMore = false;

        if ($file) {

            for ($row = 0; $row < GRID_SIZE; $row++) {
                for ($col = 0; $col < GRID_SIZE; $col++) {
                    $char = htmlentities(fgetc($file));

                    if ($char === false) {
                        // fill the remaining indexes with 0
                        for ($row; $row < GRID_SIZE; $row++){
                            for ($col; $col < GRID_SIZE; $col++){
                                $grid[$row][$col] = 0;
                            }
                            $col = 0;
                        }
                        echo "File was not formatted correctly: Less than 400 numbers. Missing numbers substituted with 0.<br><br>";
                        break;
                    }
                    else{
                        if (!is_numeric($char)) {
                            if (preg_match('/[\s]/', $char)) {
                                while (preg_match('/[\s]/', $char)) {
                                    $char = htmlentities(fgetc($file));
                                }
                                
                            } 
                            else if(!$formatErrorMore){
                                //echo "File: '" . $_FILES["filename"]["name"] . "'<br>was not formatted correctly;<br>character '$char' replaced with 0.<br><br>";
                                //echo "File was not formatted correctly;<br>character '$char' replaced with 0.<br><br>";
                                echo "File was not formatted correctly: All letters and symbols replaced with 0.<br><br>";
                                //$grid[$row][$col] = 0;
                                $formatErrorMore = true; // flag that format error was already outputted
                            }
                        }
                    }
                    $grid[$row][$col] = (int)$char;
                }
            };


            $char = htmlentities(fgetc($file));
            if($char == true)   echo "File was not formatted correctly: More than 400 numbers. Only the first 400 will be used.<br><br>";

            fclose($file);  // close the file

        } else {
            echo "Failed to open the file.";
        }
        printGridTable($grid);
        return $grid;
    }

    
    function tester_function(){
        // associative array of all test file names and their largest products
        $testFiles = ['/../test/test original.txt' => 5832, 
                      '/../test/test - less than 400 numbers.txt' => 4536,
                      '/../test/test - original altered (408 numbers).txt' => 5832,
                      '/../test/test - empty.txt' => 0,
                      '/../test/test - more than 400 + non-numbers.txt' => 6561,
                     ];
        

        // run rests on every file name in the array
        foreach($testFiles as $fileName => $product) {
            echo "<br><br><br>---- Testing: '$fileName' ----<br>";
            $testGrid = fillGrid("./$fileName");
            $testResult = largestProduct($testGrid);

            if($testResult === $product)
                echo "Test passed.<br>Expected result: $product<br>Actual result: $testResult";
            else
                echo "Test failed.<br>Expected result: $product<br>Actual result: $testResult";
        }
    } // end tester_function


    // function that determines the largest product in the grid using 4x4 sliding window
    function largestProduct($grid){
        $largestProduct = -1;           // holds current largest product found, -1 initial value
        $largestProductFactors = [];    // holds the factors of the absolute largest product
        $largestProductIndices = [];    // holds the indices for the largest product's factors
        $lastTravIndex = GRID_SIZE - NUMBER_OF_FACTORS;  // last starting index for traversal = 20 - 4 = 16 => [16][16]
        $currentFourFactors = [];       // temporarily holds four factors at a time
        //$foundLargestProduct = false;   // for debugging: flag if a larger product is found than the previous one


        for($travIndexRow = 0; $travIndexRow <= $lastTravIndex; $travIndexRow++){
            for($travIndexCol = 0; $travIndexCol <= $lastTravIndex; $travIndexCol++){

                // traverse top row to bottom row
                //echo "origin index: [$travIndexRow][$travIndexCol]<br>---- rows:<br>";
                for ($row = $travIndexRow; $row < $travIndexRow + NUMBER_OF_FACTORS; $row++) {
                    for ($col = $travIndexCol; $col < $travIndexCol + NUMBER_OF_FACTORS; $col++) {
                        $currentFourFactors[] = $grid[$row][$col];
                    }

                    $product = array_product($currentFourFactors);

                    if($product > $largestProduct){
                        //$foundLargestProduct = true;
                        $largestProduct = $product;
                        $largestProductFactors = $currentFourFactors;
                        $largestProductIndices = [ "factor 1" => [$row, $col-4],
                                                   "factor 2" => [$row, $col-3],
                                                   "factor 3" => [$row, $col-2],
                                                   "factor 4" => [$row, $col-1] ];
                    }
                    
                    //printDebugProductFactors($currentFourFactors, $product, $largestProduct);
                    //$foundLargestProduct = printDebugLargestProduct($foundLargestProduct, $largestProductIndices, $largestProductFactors);                    
                    $currentFourFactors = [];   // clear array
                }

                // traverse left column to right column
                //echo "<br>| columns:<br>";
                for ($col = $travIndexCol; $col < $travIndexCol + NUMBER_OF_FACTORS; $col++) {                    
                    for ($row = $travIndexRow; $row < $travIndexRow + NUMBER_OF_FACTORS; $row++) {
                        $currentFourFactors[] = $grid[$row][$col];  
                    }

                    $product = array_product($currentFourFactors);

                    if($product > $largestProduct){
                        //$foundLargestProduct = true;
                        $largestProduct = $product;
                        $largestProductFactors = $currentFourFactors;
                        $largestProductIndices = [ "factor 1" => [$row-4, $col],
                                                   "factor 2" => [$row-3, $col],
                                                   "factor 3" => [$row-2, $col],
                                                   "factor 4" => [$row-1, $col] ];
                    }

                    //printDebugProductFactors($currentFourFactors, $product, $largestProduct);
                    //$foundLargestProduct = printDebugLargestProduct($foundLargestProduct, $largestProductIndices, $largestProductFactors);                    
                    $currentFourFactors = [];   // clear array
                } // end for


                // traverse diagonally top left to bottom right
                //echo "<br>\ top-left to bottom-right diagonal:<br>";
                for ($row = $travIndexRow, $col = $travIndexCol; 
                    $row < $travIndexRow + NUMBER_OF_FACTORS, $col < $travIndexCol + NUMBER_OF_FACTORS;
                    $row++, $col++) {
                    $currentFourFactors[] = $grid[$row][$col];                    
                } // end for

                $product = array_product($currentFourFactors);

                if($product > $largestProduct){
                    //$foundLargestProduct = true;
                    $largestProduct = $product;
                    $largestProductFactors = $currentFourFactors;
                    $largestProductIndices = [ "factor 1" => [$row-4, $col-4],
                                               "factor 2" => [$row-3, $col-3],
                                               "factor 3" => [$row-2, $col-2],
                                               "factor 4" => [$row-1, $col-1] ];
                }

                //printDebugProductFactors($currentFourFactors, $product, $largestProduct);
                //$foundLargestProduct = printDebugLargestProduct($foundLargestProduct, $largestProductIndices, $largestProductFactors);                
                $currentFourFactors = [];   // clear array


                // traverse diagonally bottom left to top right
                //echo "<br>/ bottom-left to top-right diagonal:<br>";
                for ($row = $travIndexRow + NUMBER_OF_FACTORS - 1, $col = $travIndexCol; 
                    $row > $travIndexRow, $col < $travIndexCol + NUMBER_OF_FACTORS;
                    $row--, $col++) {
                    $currentFourFactors[] = $grid[$row][$col];                      
                } // end for

                $product = array_product($currentFourFactors);

                if($product > $largestProduct){
                    //$foundLargestProduct = true;
                    $largestProduct = $product;
                    $largestProductFactors = $currentFourFactors;
                    $largestProductIndices = [ "factor 1" => [$row+4, $col-4],
                                               "factor 2" => [$row+3, $col-3],
                                               "factor 3" => [$row+2, $col-2],
                                               "factor 4" => [$row+1, $col-1] ];
                }
                
                //printDebugProductFactors($currentFourFactors, $product, $largestProduct);
                //$foundLargestProduct = printDebugLargestProduct($foundLargestProduct, $largestProductIndices, $largestProductFactors);
                $currentFourFactors = [];   // clear array
            } // end 2nd for loop
        } // end main for loop
        
        /*
        // output result of absolute largest product
        echo "<br><b>Largest Product: " . $largestProduct . "</b><br>";
        echo "- - - - - - row col<br>";

        // output largest product's factors and their indexes
        $factorsIndex = 0;
        foreach ($largestProductIndices as $key => $pair){
            echo "factor " . $largestProductFactors[$factorsIndex] . ": [" . $pair[0] . "][" . $pair[1] . "]<br>";
            $factorsIndex++;
        }
        */
        return $largestProduct;
    } // end largestProduct()

    
    // for debugging & visualization
    // prints 20x20 array
    function printGridTable($grid){
        echo "<table>";
        echo "<tr>";
        echo "<th></th>";   // empty cell at the top left
        for ($i = 0; $i < GRID_SIZE; $i++) {
            echo "<th>" . $i . "</th>"; // column index numbers on top
        }
        echo "</tr>";

        // fill table with 400 numbers from file with index numbers
        for ($row = 0; $row < GRID_SIZE; $row++) {
            echo "<tr>";
            echo "<th>" . $row . "</th>";   // row index numbers on the left side

            // fill numbers row by row
            for ($col = 0; $col < GRID_SIZE; $col++) {
                echo "<td>" . $grid[$row][$col] . "</td>";
            }
            echo "</tr>";
        }
        echo "</table><br>";
    }


    // for debugging purposes
    // function to print the factors and their indexes if a new largest product is found
    function printFactorIndixes($largestProductIndices, $largestProductFactors){
        $factorsIndex = 0;

        foreach ($largestProductIndices as $key => $pair) {
            echo "factor " . $largestProductFactors[$factorsIndex] . ": [" . $pair[0] . "][" . $pair[1] . "]<br>";
            $factorsIndex++;
        }

        echo "<br>";
        //$foundLargestProduct = false;   // reset boolean to false
    }


    // for debugging purposes
    // prints the current four factors, their product, and the current largest product
    function printDebugProductFactors($currentFourFactors, $product, $largestProduct){
        echo json_encode($currentFourFactors) . " ";
        echo "product:  $product - largest: $largestProduct<br>";
    }


    // for debugging purposes
    // if a new largest product is found, output the four factors and their indices
    function printDebugLargestProduct($foundLargestProduct, $largestProductIndices, $largestProductFactors){
        if($foundLargestProduct){
            $factorsIndex = 0;
            printFactorIndixes($largestProductIndices, $largestProductFactors);
        }
        return false;   // reset boolean to false
    }

?>
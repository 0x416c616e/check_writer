<?php
//unit test for check_writer.php
//does 100 random unit tests every time you refresh the page
//I did it this way because there are 100 cents in every dollar, and this supports up to 999,999.99
//so I didn't want to iterate through every possibility
//random will get edge cases if you do enough of them and refresh it a few times
foreach(range(1, 100) as $i) {
    $number_of_digits = rand(1, 6);
    $decimal = "";
    $has_decimal = rand(0, 1);
    $full_number = "";
    foreach(range(1, $number_of_digits) as $j) {
        $full_number .= strval(rand(0,9));
    }
    if ($has_decimal == 1) {
        $decimal = rand(0, 99);
        $full_number .= "." . strval($decimal);
    }
    //you might want to change the file path
    $response = file_get_contents("http://localhost/todolistapp/check_writer/check_writer.php?amount=" . $full_number);
    echo "Test for $full_number: $response<br>";
}
?>
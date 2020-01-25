<?php
    //function that turns a single-digit number
    //(which is a string, not an int) and its
    //position in a bigger number (i.e. 1 = ones place)
    //into a written out English form, such as:
    //strnum_to_word("5", 2) returns "fifty" because 
    //it's 5 in the tens place
    function strnum_to_word($str_rep, &$position) {       
        $places = explode(",", "ones,tens,hundreds,thousands,ten thousands,hundred thousands");
        $ones = explode(" ", "zero one two three four five six seven eight nine");
        $tens = explode(" ", "ten twenty thirty forty fifty sixty seventy eighty ninety");
        $big_nums = array("thousand", "hundred");
        $word = "";
        $num = intval($str_rep);
        //do different things based on the position
        switch ($position) {
            case 6:
            case 3:
                //hundred thousands and hundreds
                $word = ($num == 0 ? "" : ($ones[$num] . " " . $big_nums[1]));
                break;
            case 5:
            case 2:
                //ten thousands and tens
                $word = ($num == 0 ? "" : ($tens[$num - 1]));
                break;
            case 4:
                //thousands
                $word = ($num == 0 ? $big_nums[0] : ($ones[$num] . " " . $big_nums[0]));
                break;
            case 1:
                //tens
                $word = ($num == 0 ? "" : ($ones[$num]));
                break;
            default:
                echo "error";
                break;
        }
        if ($position > 1) {
            $word .= " ";
        }
        $position -= 1;
        return $word;
    }
    //main which does everything
    function main ($arg_amount) {
        //getting query string input
        $num_amount = "";
        if (isset($_GET["amount"])) {
            $num_amount = $_GET["amount"];
        } else {
            $num_amount = $arg_amount;
        }
        //converting numberic input to string
        $str_representation = strval($num_amount);
        //getting rid of unnecessary characters
        $str_representation = str_replace(",", "", $str_representation);
        $str_representation = str_replace("$", "", $str_representation);
        $has_decimal = false;
        //doing stuff if there's a decimal
        if (strpos($str_representation, ".") !== false) {
            $has_decimal = true;
        }
        $decimal = "";
        //separate string representation of user input number
        //into two numbers: the whole numbers and the decimal numbers
        if ($has_decimal) {
            $len = strlen($str_representation);
            $decimal = substr($str_representation, ($len - 2), ($len - 1));
            $str_representation = substr($str_representation, 0, ($len - 3));
        }
        $amount_len = strlen($str_representation);
        //turn string to char array because each digit
        //will be converted into words separately
        $str_array = str_split($str_representation);
        //starting to build the final written string
        $final_string = "";
        $num_position = count($str_array);
        //for every digit in the number, convert it to an English written word
        foreach($str_array as $i) {
            $final_string .= strval(strnum_to_word($i, $num_position));
        }
        //edge case for zero dollars
        if ($final_string == "") {
            $final_string = "zero";
        }
        $final_string .= " dollar";
        //if more than $1.99, put S for plural dollars
        if ($num_amount >= 2.0) {
            $final_string .= "s";
        }
        //decimal-writing stuff
        if ($has_decimal) {
            //decimals to char array
            $dec_array = str_split($decimal);
            $final_string .= " and ";
            $dec_length = 2;
            $dec_string = "";
            //convert each of the two decimal digits to words
            foreach(range(0,1) as $i) {
                $dec_string .= strnum_to_word($dec_array[$i], $dec_length);
            }
            if ($dec_string == "") {
                $final_string .= " zero";
            } else {
                $final_string .= $dec_string;
            }
            $final_string .= " cent";
            //edge case for one cent plurality
            $final_string .= ($dec_string == " one" ? "" : "s");
        } else {
            //no decimal
            $final_string .= " and zero cents";
        }
        //fixing an oddity
        //the program would write stuff like "ten one" instead of "eleven" or "ten two" instead of "twelve"
        //so this find-and-replace operation fixes that
        $incorrect = explode(",", "ten one,ten two,ten three,ten four,ten five,ten six,ten seven,ten eight,ten nine");
        $correct = explode(" ", "eleven twelve thirteen fourteen fifteen sixteen seventeen eighteen nineteen");
        foreach(range(0, 8) as $i) {
            $final_string = str_replace($incorrect[$i], $correct[$i], $final_string);
        }
        //capitalize and display final result
        $final_string = ucfirst($final_string);
        echo $final_string;
    }
    //run main with error suppression and pass GET parameter as arg
    @main($_GET["amount"]);
?>
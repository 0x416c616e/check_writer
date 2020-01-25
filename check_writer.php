<?php

    //Check Writer
    //take a float and turn it into a written version of it
    //i.e. 54363.54 becomes:
    //"Fifty Four Thousand Three Hundred Sixty Three Dollars and 54 cents"
    //from $0 to $999,999.99
    //can be passed somethin like 99,299.05 or 99299.05
    //may or may not have cents, like 5000 or 5,000 instead of 5000.00

    //take a string representation of a single digit
    //and its position in a number (1 = tens, 2 = hundreds, etc)
    //and then turn it into an English spelling of the number
    //i.e. 1 in the thousands place return "one thousand"
    //example usage: some_string .= strnum_to_word("1", 3);
    function strnum_to_word($str_rep, &$position) {       
        //written number arrays
        $places = explode(",", "ones,tens,hundreds,thousands,ten thousands,hundred thousands");
        $ones = explode(" ", "zero one two three four five six seven eight nine");
        $tens = explode(" ", "ten twenty thirty forty fifty sixty seventy eighty ninety");
        $big_nums = array("thousand", "hundred");

        //the english words depend on the position of the number
        //position 1 = ones, 2 = tens, 3 = hundreds, 4 = thousands, etc.
        $word = "";
        $num = intval($str_rep);
        switch ($position) {
            case 6:
            case 3:
                //hundred thousands or hundreds
                $word = ($num == 0 ? "" : ($ones[$num] . " " . $big_nums[1]));
                break;

            case 5:
            case 2:
                //ten thousands or tens
                $word = ($num == 0 ? "" : ($tens[$num - 1]));
                break;

            case 4:
                //thousands
                $word = ($num == 0 ? $big_nums[0] : ($ones[$num] . " " . $big_nums[0]));
                break;

            case 1:
                //ones
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
    //where everything happens
    function main ($arg_amount) {
        //get the amount from the user
        $num_amount = "";
        if (isset($_GET["amount"])) {
            $num_amount = $_GET["amount"]; //check.php?amount=5000.00
        } else {
            $num_amount = $arg_amount;
        }

        //convert amount to string
        $str_representation = strval($num_amount);

        //get rid of commas, if any
        //i.e. 5,000 -> 5000
        $str_representation = str_replace(",", "", $str_representation);

        //get rid of dollar sign, if there is one
        $str_representation = str_replace("$", "", $str_representation);

        $has_decimal = false;

        //check if there's a decimal in the number
        if (strpos($str_representation, ".") !== false) {
            $has_decimal = true;
        }

        $decimal = "";
        //get rid of decimal from last part of string, if it has one
        if ($has_decimal) {
            $len = strlen($str_representation);
            //example number: 100.00
            //if length = 6, means indices are 0-5
            //for a length of 6, that means 0, 1, and 2 are ok
            //because 3 is the decimal point, and 4 and 5 are the cent values

            //decimal only:
            $decimal = substr($str_representation, ($len - 2), ($len - 1));

            //str_rep takes off the decimal, as the decimal is now stored in $decimal
            $str_representation = substr($str_representation, 0, ($len - 3));
        }

        //now left with $decimal for decimal place
        //and str_representation only has the dollar amount, no cents

        //length of dollar-only amount, no cents
        $amount_len = strlen($str_representation);

        //convert string to array of chars
        $str_array = str_split($str_representation);

        //
        $final_string = "";
        //num_position means ones place, tens place, hundreds place, etc.
        $num_position = count($str_array);

        foreach($str_array as $i) {
            $final_string .= strval(strnum_to_word($i, $num_position));
        }
        if ($final_string == "") {
            $final_string = "zero";
        }
        $final_string .= " dollar";
        if ($num_amount >= 2.0) {
            $final_string .= "s";
        }
        
        if ($has_decimal) {
            $dec_array = str_split($decimal);
            $final_string .= " and ";
            $dec_length = 2;
            $dec_string = "";
            foreach(range(0,1) as $i) {
                $dec_string .= strnum_to_word($dec_array[$i], $dec_length);
            }
            if ($dec_string == "") {
                $final_string .= " zero";
            } else {
                $final_string .= $dec_string;
            }
        
            $final_string .= " cent";
            $final_string .= ($dec_string == " one" ? "" : "s");
        } else {
            $final_string .= " and zero cents";
        }

        //it used to write "ten one" instead of "eleven",
        //"ten two" instead of "twelve", and so on
        //so this does find-and-replace operations to fix it
        $incorrect = explode(",", "ten one,ten two,ten three,ten four,ten five,ten six,ten seven,ten eight,ten nine");
        $correct = explode(" ", "eleven twelve thirteen fourteen fifteen sixteen seventeen eighteen nineteen");
        foreach(range(0, 8) as $i) {
            $final_string = str_replace($incorrect[$i], $correct[$i], $final_string);
        }

        $final_string = ucfirst($final_string);
        echo $final_string;
    }

    //run main with query string and error suppression
    //in case the script is invoked without the proper GET parameter
    @main($_GET["amount"]);

?>
<?php

$packArray = array(5000, 2000, 1000, 500, 250);     // An array of all available pack sizes.
$outputDict = dictCreate($packArray);               // A dictionary where the counts for pack amounts are stored for each pack size.
$amount = $_POST["amount"];                         // Variable containing our input amount.
$numericCondition = is_numeric($amount);            // Boolean Condition that ensures ONLY a number is allowed to be input (input sanitization).
$overflowCondition = $amount <= PHP_INT_MAX;        // Boolean Condition that ensures a input number is less than or equal to the MAXIMUM integer that can be represented.
$lastPos = count($packArray)-1;                     // Variable containing the position of the last element in $packArray.

// Function to dynamically create a dictionary, where the counts for pack amounts are stored. Returns a dictionary.
function dictCreate($packArray) {
    $outputDict = array();

    for ($i = 0; $i < count($packArray); $i++) {
        $outputDict["$packArray[$i]"] = 0;
    }

    return $outputDict;
}

// Function where all the magic happens. The key is division.
function widgetCalculate($widgetNum) {
    global $packArray, $outputDict, $lastPos;
    $loop = true;
    $position = 0;
    $amountAtStart = $widgetNum;

    while ($loop) {
        $multiplier = 0;
        $remainingValue = 0;

        $divResult = $widgetNum / $packArray[$position];
        $divValue = $divValue = (gettype($divResult) == 'integer') ? $divResult : floor($divResult);
        $divRemainder = $divResult - $divValue;

        // if the remainder is 0, the request divides perfectly into a pack, so increment the value in the dictionary and end the loop.
        if ($divRemainder == 0) {
            for ($i = 0; $i < $divValue; $i++) {
                $outputDict["$packArray[$position]"]++;
                $loop = false;
            }
        }

        else {

            // The standard case.
            for ($i = 0; $i < $divValue; $i++) {
                $outputDict["$packArray[$position]"]++;
                $multiplier++;
            }

            $remainingValue = $widgetNum - ($multiplier * $packArray[$position]);   // This is used to decide whether we want to add another pack, depending on if this value < smallest value in the $packArray.

            // If the value is less than the smallest value in $packArray, we consider it worthwile to simply add another pack since using smaller packs would be more costly.
            if (($packArray[$position] - $remainingValue) < $packArray[$lastPos]) {
                $outputDict["$packArray[$position]"]++;
                $loop = false;
            }

            else {
                $widgetNum = $remainingValue;
                $position++;
            }

        }
    }

    echo "You want to send ".$amountAtStart." widgets! In order to do this efficiently, see instructions below: <br> <br>";
    echo "You need to send: <br> <br>";

    for ($i = 0; $i <= $lastPos; $i++) {
        echo $packArray[$i]." Widget Pack x ".$outputDict["$packArray[$i]"].". <br>";
    }

}

if ($numericCondition && $overflowCondition) {
    widgetCalculate($amount);
}

else {
    if ($numericCondition) {
        exit("Error: Input too large! Please input a number smaller than or equal to ".PHP_INT_MAX.".");
    }

    if ($overflowCondition) {
        exit('Error: Please only input a number and NO other characters!');
    }
}

?>

<?php

/*
 * In The Name Of God *
 * Helper Functions *
*/

use App\Models\Question;

//Generate Transaction Number
if (!function_exists('rand_nm')) {
    function rand_nm()
    {
        do {
            $code = random_int(1000000, 9999999);
        } while (Question::where("code", "=", $code)->first());

        return $code;
    }
}

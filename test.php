<?php

/**
 * Types of Variables
 *
 * @var string
 * @var integer
 *
 */

$input = '@emma_rocks';

$user_name = str_replace( $input, '@', '' );
$user_name = '@' . $user_name;
$age = 26;

$new_age = $age / 2;

echo $user_name . '<br />';
echo $new_age;

?>

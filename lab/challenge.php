<?php
/**
 * Created by IntelliJ IDEA.
 * User: arik-so
 * Date: 4/13/14
 * Time: 12:59 AM
 */

// echo 'hello world';

$a = 1;
$b = 1;

while(true){

    echo $a.'<br/>';

    $c = $b;
    $b = $a+$b;
    $a = $c;

    if($a >= 1000){ break; }

}
<?php

try	{
    $phar = new Phar('ro.phar');
    $phar->extractTo('./', null, true); // Extract all files
}catch(Exception$e) {
    echo "There was an error <br />";
    print_r($s);
}


<?php
    header("Content-Type: application/javascript");
    $callback = $_GET["getResponse"];
    echo $callback . "(" . file_get_contents('aedos_today.json') . ")";


?>
<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

spl_autoload_register(function ($classname) {
    include "/opt/src/trivia/$classname.php";
});
        

$trivia = new TriviaController($_GET);

$trivia->run();
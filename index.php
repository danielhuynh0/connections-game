<!-- 
    Daniel Huynh (tap7ke)
    Eric Li (zzf5jx)
-->

<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

spl_autoload_register(function ($classname) {
    include "$classname.php";
});
        

$trivia = new CategoryGameController($_GET);

$trivia->run();
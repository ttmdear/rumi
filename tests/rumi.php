<?php
require __DIR__."/../vendor/autoload.php";

use \Rumi\functions\uniqid;

for ($i=0; $i < 10; $i++) {
    echo "\nuniqid -> ".\Rumi\functions\uniqid();
}



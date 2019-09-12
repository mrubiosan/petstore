<?php

use Mrubiosan\PetStore\Application\WebApplication;

require __DIR__.'/../vendor/autoload.php';

(new WebApplication())->create()->run();

<?php

require 'vendor/autoload.php';

$em = (new \Mrubiosan\PetStore\Application\WebApplication())->create()->getContainer()->get('entityManager');
return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($em);

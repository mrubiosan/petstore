#!/usr/bin/env php
<?php
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

require __DIR__.'/../vendor/autoload.php';


$loader = new Loader();
$loader->loadFromDirectory(__DIR__.'/../src/Application/Fixtures');
$em = (new \Mrubiosan\PetStore\Application\WebApplication())->create()->getContainer()->get('entityManager');


$purger = new ORMPurger();
$executor = new ORMExecutor($em, $purger);
$executor->execute($loader->getFixtures());

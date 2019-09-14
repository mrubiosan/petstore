#!/usr/bin/env php
<?php
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

require __DIR__.'/../vendor/autoload.php';


$loader = new Loader();
$loader->addFixture(new \Mrubiosan\PetStore\Application\Fixtures\CategoryFixtureLoader());
$loader->addFixture(new \Mrubiosan\PetStore\Application\Fixtures\TagFixtureLoader());
$loader->addFixture(new \Mrubiosan\PetStore\Application\Fixtures\PetFixtureLoader());
$loader->addFixture(new \Mrubiosan\PetStore\Application\Fixtures\PhotoUrlFixtureLoader());

$em = (new \Mrubiosan\PetStore\Application\WebApplication())->create()->getContainer()->get('entityManager');

$purger = new ORMPurger();
$executor = new ORMExecutor($em, $purger);
$executor->execute($loader->getFixtures());

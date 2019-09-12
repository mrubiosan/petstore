<?php
namespace Mrubiosan\PetStore\Application\Fixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Mrubiosan\PetStore\Domain\Pet\Category;

class CategoryFixtureLoader implements FixtureInterface
{
    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $categories = [
            1 => 'turtles',
            2 => 'cats',
            3 => 'dogs'
        ];

        foreach ($categories as $id => $name) {
            $manager->persist(new Category($id, $name));
        }

        $manager->flush();
    }
}

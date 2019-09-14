<?php
namespace Mrubiosan\PetStore\Application\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Mrubiosan\PetStore\Domain\Pet\Category;

class CategoryFixtureLoader extends AbstractFixture
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
            $category = new Category($id, $name);
            $manager->persist($category);
            $this->addReference('category-'.$id, $category);
        }

        $manager->flush();
    }
}

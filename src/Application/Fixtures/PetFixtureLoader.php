<?php
namespace Mrubiosan\PetStore\Application\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Mrubiosan\PetStore\Domain\Pet\Pet;

class PetFixtureLoader extends AbstractFixture
{
    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $pets = [
            (new Pet(0, 'Fido'))->setCategory($this->getReference('category-3'))->setTags($this->getReference('tag-1')),
            (new Pet(0, 'Bubbles'))->setCategory($this->getReference('category-2')),
        ];

        foreach ($pets as $i => $pet) {
            $manager->persist($pet);
            $this->addReference('pet-'.$i, $pet);
        }

        $manager->flush();
    }
}

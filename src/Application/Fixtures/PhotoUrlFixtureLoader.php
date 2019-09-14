<?php
namespace Mrubiosan\PetStore\Application\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Mrubiosan\PetStore\Domain\Pet\Pet;
use Mrubiosan\PetStore\Domain\Pet\PhotoUrl;

class PhotoUrlFixtureLoader extends AbstractFixture
{
    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $photoUrls = [
            new PhotoUrl('http://example.com/photo1.jpg', $this->getReference('pet-0')),
            new PhotoUrl('http://example.com/photo2.jpg', $this->getReference('pet-0')),
            new PhotoUrl('http://example.com/photo3.jpg', $this->getReference('pet-1')),
            new PhotoUrl('http://example.com/photo4.jpg', $this->getReference('pet-1')),
        ];

        foreach ($photoUrls as $i => $photoUrl) {
            $manager->persist($photoUrl);
        }

        $manager->flush();
    }
}

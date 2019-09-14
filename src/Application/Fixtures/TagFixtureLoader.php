<?php
namespace Mrubiosan\PetStore\Application\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Mrubiosan\PetStore\Domain\Pet\Tag;

class TagFixtureLoader extends AbstractFixture
{
    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $tags = [
            1 => 'brisbane',
            2 => 'sydney',
            3 => 'melbourne'
        ];

        foreach ($tags as $id => $name) {
            $tag = new Tag($id, $name);
            $manager->persist($tag);
            $this->addReference('tag-'.$id, $tag);
        }

        $manager->flush();
    }
}

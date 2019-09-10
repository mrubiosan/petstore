<?php
namespace Mrubiosan\PetStore\Infrastructure\Domain\Pet;

use Doctrine\DBAL\Connection;
use Mrubiosan\PetStore\Domain\Exception\EntityNotFoundException;
use Mrubiosan\PetStore\Domain\Pet\Pet;
use Mrubiosan\PetStore\Domain\Pet\PetRepositoryInterface;

class DbPetRepository implements PetRepositoryInterface
{
    /**
     * @var Connection
     */
    private $db;

    public function save(Pet $pet)
    {
        $isNewEntity = $pet->getId() === 0;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id): Pet
    {

    }
}

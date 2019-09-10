<?php
namespace Mrubiosan\PetStore\Domain\Pet;

use Mrubiosan\PetStore\Domain\Exception\EntityNotFoundException;

interface PetRepositoryInterface
{
    public function save(Pet $pet);

    /**
     * @param int $id
     * @return Pet
     * @throws EntityNotFoundException
     */
    public function getById(int $id) : Pet;
}

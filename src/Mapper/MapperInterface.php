<?php

declare(strict_types=1);

namespace App\Mapper;

interface MapperInterface
{
    public function mapToEntityFromArray(array $properties): object;

    public function mapToArrayFromEntity(object $entity): array;
}

<?php

namespace App\Core\Contracts;

interface FieldsConfigurator
{
    public static function configureFields(mixed $entity, ?string $pageName = null): iterable;
}

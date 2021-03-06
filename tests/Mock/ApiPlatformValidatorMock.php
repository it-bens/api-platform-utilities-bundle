<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUtilitiesBundle\Tests\Mock;

use ApiPlatform\Core\Validator\Exception\ValidationException;
use ApiPlatform\Core\Validator\ValidatorInterface;

final class ApiPlatformValidatorMock implements ValidatorInterface
{
    /**
     * @param object $data
     * @param array $context
     *
     * @phpstan-ignore-next-line
     */
    public function validate($data, array $context = []): void
    {
    }
}

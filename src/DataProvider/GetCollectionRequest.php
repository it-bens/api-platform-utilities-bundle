<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUtilitiesBundle\DataProvider;

abstract class GetCollectionRequest
{
    /** @var int $itemsPerPage */
    public int $itemsPerPage = 20;
    /** @var int $page */
    public int $page = 1;
    /** @var bool $pagination */
    public bool $pagination = true;
}

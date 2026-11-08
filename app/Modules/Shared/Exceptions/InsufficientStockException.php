<?php

namespace Modules\Shared\Exceptions;

use DomainException;

class InsufficientStockException extends DomainException
{
    public function __construct(string $title, int $available, int $requested)
    {
        parent::__construct(
            "Stok buku '{$title}' tidak cukup (tersedia: {$available}, diminta: {$requested})"
        );
    }
}

<?php

namespace Modules\Shared\Contracts;

interface MemberServiceInterface
{
    public function getMemberForLoan(int $memberId): ?array;
    public function canBorrow(int $memberId, int $bookCount): bool;
    public function recordLoanStarted(int $memberId, int $bookCount): void;
    public function recordLoanReturned(int $memberId, int $bookCount): void;
}

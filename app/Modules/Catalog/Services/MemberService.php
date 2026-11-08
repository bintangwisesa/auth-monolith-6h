<?php

namespace Modules\Membership\Services;

use Modules\Membership\Models\Member;
use Modules\Shared\Contracts\MemberServiceInterface;

class MemberService implements MemberServiceInterface
{
    public function getMemberForLoan(int $memberId): ?array
    {
        $m = Member::find($memberId);

        return $m ? [
            'id' => $m->id, 'name' => $m->name,
            'status' => $m->status, 'active_loans' => $m->active_loans,
        ] : null;
    }

    public function canBorrow(int $memberId, int $bookCount): bool
    {
        return (bool) Member::find($memberId)?->canBorrow($bookCount);
    }

    public function recordLoanStarted(int $memberId, int $bookCount): void
    {
        $member = Member::findOrFail($memberId);
        $member->increment('active_loans', $bookCount);
        $member->update(['status' => 'borrowing']); // update status anggota
    }

    public function recordLoanReturned(int $memberId, int $bookCount): void
    {
        $member = Member::findOrFail($memberId);
        $member->decrement('active_loans', $bookCount);
        $member->refresh();

        if ($member->active_loans <= 0) {
            $member->update(['status' => 'active']);
        }
    }
}
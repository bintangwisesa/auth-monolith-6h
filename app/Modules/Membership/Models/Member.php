<?php

namespace Modules\Membership\Models;

use Database\Factories\MemberFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'member_number', 'name', 'email', 'status', 'max_loans', 'active_loans',
    ];

    protected static function newFactory() { return MemberFactory::new(); }

    public function roles() { return $this->belongsToMany(Role::class); }

    public function canBorrow(int $count): bool
    {
        return in_array($this->status, ['active', 'borrowing'], true)
            && ($this->active_loans + $count) <= $this->max_loans;
    }
}
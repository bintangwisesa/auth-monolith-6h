<?php

namespace Modules\Membership\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Membership\Models\Member;

class MemberController extends Controller
{
    public function index()
    {
        return response()->json(Member::with('roles')->paginate(15));
    }

    public function destroy(int $id)
    {
        Member::findOrFail($id)->delete();
        return response()->json(['message' => 'Anggota dihapus (soft delete)']);
    }

    public function restore(int $id)
    {
        $member = Member::onlyTrashed()->findOrFail($id);
        $member->restore();
        return response()->json($member);
    }
}

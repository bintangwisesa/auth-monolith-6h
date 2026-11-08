<?php

namespace Modules\Circulation\Http\Controllers;

use App\Http\Controllers\Controller;
use DomainException;
use Illuminate\Http\Request;
use Modules\Circulation\Models\Loan;
use Modules\Circulation\Services\BorrowingService;

class LoanController extends Controller
{
    public function __construct(private BorrowingService $borrowing) {}

    public function index()
    {
        return response()->json(Loan::with('items')->latest()->paginate(15));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'member_id'        => 'required|integer',
            'days'             => 'nullable|integer|min:1|max:30',
            'items'            => 'required|array|min:1',
            'items.*.book_id'  => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            $loan = $this->borrowing->borrow($data['member_id'], $data['items'], $data['days'] ?? 7);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json($loan, 201);
    }

    public function return(int $id)
    {
        try {
            $loan = $this->borrowing->returnLoan(Loan::findOrFail($id));
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json($loan);
    }

    public function destroy(int $id)
    {
        Loan::findOrFail($id)->delete();
        return response()->json(['message' => 'Peminjaman dihapus (soft delete)']);
    }

    public function restore(int $id)
    {
        $loan = Loan::onlyTrashed()->findOrFail($id);
        $loan->restore();
        return response()->json($loan->load('items'));
    }
}

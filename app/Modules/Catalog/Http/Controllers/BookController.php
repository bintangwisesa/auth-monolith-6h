<?php

namespace Modules\Catalog\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Catalog\Models\Book;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $books = Book::with(['authors', 'categories', 'publisher'])
            ->when($request->boolean('available'), fn ($q) => $q->available())
            ->when($request->filled('q'), fn ($q) => $q->search($request->q))
            ->when($request->filled('category_id'), fn ($q) => $q->inCategory((int) $request->category_id))
            ->paginate(15);

        return response()->json($books);
    }

    public function show(int $id)
    {
        return response()->json(Book::with(['authors', 'categories', 'publisher'])->findOrFail($id));
    }

    public function destroy(int $id)
    {
        Book::findOrFail($id)->delete();
        return response()->json(['message' => 'Buku dihapus (soft delete)']);
    }

    public function restore(int $id)
    {
        $book = Book::onlyTrashed()->findOrFail($id);
        $book->restore();
        return response()->json($book);
    }
}

<?php

namespace App\Ai\Tools;

use App\Models\Book;
use App\Models\Categori;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class SearchBooks implements Tool
{
    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Search for books by title, author, or category. Returns book details including title, author, price, and stock availability.';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        // PERBAIKAN: Ambil parameter 'query'
        $query = $request->string('query');

        if (empty($query)) {
            // Coba ambil dari parameter lain
            $query = $request->string('author');
            if (empty($query)) {
                $query = $request->string('category');
            }
        }

        if (empty($query)) {
            return "❌ Silakan masukkan kata kunci untuk mencari buku.\n\nContoh: 'Harry Potter', 'James Clear', 'Programming'";
        }

        // Search books by title, author, or category name
        $books = Book::where('title', 'LIKE', "%{$query}%")
            ->orWhere('author', 'LIKE', "%{$query}%")
            ->orWhereHas('category', function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            })
            ->with('category')
            ->limit(5)
            ->get();

        if ($books->isEmpty()) {
            // Try to find books from similar categories
            $similarCategory = Categori::where('name', 'LIKE', "%{$query}%")->first();

            if ($similarCategory) {
                $booksFromCategory = Book::where('category_id', $similarCategory->id)
                    ->with('category')
                    ->limit(3)
                    ->get();

                if ($booksFromCategory->isNotEmpty()) {
                    $result = "📚 Buku dengan kata kunci '{$query}' tidak ditemukan.\n";
                    $result .= "📁 Namun ditemukan kategori '{$similarCategory->name}'.\n\n";
                    $result .= "💡 Rekomendasi buku dari kategori tersebut:\n";

                    foreach ($booksFromCategory as $index => $book) {
                        $result .= ($index + 1) . ". {$book->title} - {$book->author}\n";
                        $result .= "   Harga: Rp " . number_format($book->price, 0, ',', '.') . "\n";
                        $result .= "   Stok: " . ($book->stock > 0 ? $book->stock . " copy" : "Habis") . "\n";
                        if ($index < $booksFromCategory->count() - 1) {
                            $result .= "\n";
                        }
                    }
                    $result .= "\nAtau coba dengan kata kunci yang berbeda ya! 😊";
                    return $result;
                }
            }

            // Fallback: show random books
            $randomBooks = Book::with('category')->inRandomOrder()->limit(3)->get();

            if ($randomBooks->isNotEmpty()) {
                $result = "📚 Maaf, buku dengan kata kunci '{$query}' tidak ditemukan.\n\n";
                $result .= "💡 Rekomendasi buku lain yang mungkin Anda suka:\n";

                foreach ($randomBooks as $index => $book) {
                    $result .= ($index + 1) . ". {$book->title} - {$book->author}\n";
                    $result .= "   Harga: Rp " . number_format($book->price, 0, ',', '.') . "\n";
                    $result .= "   Stok: " . ($book->stock > 0 ? $book->stock . " copy" : "Habis") . "\n";
                    $result .= "   Kategori: " . ($book->category ? $book->category->name : '-') . "\n";
                    if ($index < $randomBooks->count() - 1) {
                        $result .= "\n";
                    }
                }
                $result .= "\nAtau coba dengan kata kunci yang berbeda ya! 😊";
                return $result;
            }

            return "📚 Maaf, tidak ada buku yang ditemukan dengan kata kunci '{$query}'. Silakan coba kata kunci lain.";
        }

        // Build result for found books
        $result = "📚 Ditemukan " . $books->count() . " buku untuk kata kunci '{$query}':\n\n";

        foreach ($books as $index => $book) {
            $result .= ($index + 1) . ". *{$book->title}*\n";
            $result .= "   ✍️ Penulis: {$book->author}\n";
            $result .= "   💰 Harga: Rp " . number_format($book->price, 0, ',', '.') . "\n";
            $result .= "   📦 Stok: " . ($book->stock > 0 ? "✅ Tersedia ({$book->stock} copy)" : "❌ Habis") . "\n";
            $result .= "   📚 Kategori: " . ($book->category ? $book->category->name : '-') . "\n";

            if ($book->publisher) {
                $result .= "   🏢 Penerbit: {$book->publisher}\n";
            }
            if ($book->year) {
                $result .= "   📅 Tahun: {$book->year}\n";
            }

            if ($index < $books->count() - 1) {
                $result .= "\n";
            }
        }

        return $result;
    }

    /**
     * Get the tool's schema definition.
     * PERBAIKAN: Schema harus sesuai dengan handle function
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string()
                ->description('Search query for finding books by title, author, or category name. Examples: "Harry Potter", "James Clear", "Programming"')
                ->required(),
        ];
    }
}

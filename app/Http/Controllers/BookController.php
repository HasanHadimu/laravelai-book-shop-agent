<?php

namespace App\Http\Controllers;

use App\Ai\Agents\BookFinderAgent;
use App\Models\Book;
use App\Models\Categori;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BookController extends Controller
{
    /**
     * Search using AI Agent.
     */
    public function search(Request $request)
    {
        try {
            $request->validate([
                'query' => 'required|string|min:1|max:500',
            ]);

            $queryText = $request->input('query');
            $lowerQuery = strtolower(trim($queryText));

            // Get or create conversation ID
            $conversationId = $request->input('conversation_id');
            if (!$conversationId) {
                $conversationId = $request->session()->get('chat_conversation_id');
            }
            if (!$conversationId) {
                $conversationId = (string) Str::uuid();
                $request->session()->put('chat_conversation_id', $conversationId);
            }

            // ==================== HANDLE GREETINGS ====================
            $greetings = ['hy', 'hai', 'halo', 'hello', 'hi', 'hey', 'hallo', 'helo', 'hola', 'greetings', 'selamat datang'];
            if (in_array($lowerQuery, $greetings)) {
                return response()->json([
                    'success' => true,
                    'response' => $this->getGreetingResponse(),
                    'user_query' => $queryText,
                    'books' => [],
                    'is_greeting' => true,
                    'conversation_id' => $conversationId,
                ]);
            }

            // ==================== HANDLE HELP ====================
            $helps = ['help', 'tolong', 'bantuan', '?', 'what can you do', 'bisa apa', 'perintah', 'command'];
            if (in_array($lowerQuery, $helps)) {
                return response()->json([
                    'success' => true,
                    'response' => $this->getHelpResponse(),
                    'user_query' => $queryText,
                    'books' => [],
                    'is_help' => true,
                    'conversation_id' => $conversationId,
                ]);
            }

            // ==================== HANDLE DAFTAR BUKU ====================
            $listCommands = ['daftar buku', 'semua buku', 'list buku', 'tampilkan buku', 'show books', 'all books', 'buku apa saja'];
            $isListCommand = false;
            foreach ($listCommands as $cmd) {
                if (str_contains($lowerQuery, $cmd)) {
                    $isListCommand = true;
                    break;
                }
            }

            if ($isListCommand || $lowerQuery === 'buku') {
                $allBooks = Book::with('category')->limit(15)->get();
                if ($allBooks->isNotEmpty()) {
                    return response()->json([
                        'success' => true,
                        'response' => $this->getAllBooksResponse($allBooks),
                        'user_query' => $queryText,
                        'books' => $allBooks->toArray(),
                        'is_list' => true,
                        'conversation_id' => $conversationId,
                    ]);
                }
            }

            // ==================== HANDLE KATEGORI ====================
            $categories = Categori::all();
            foreach ($categories as $cat) {
                if (str_contains($lowerQuery, strtolower($cat->name))) {
                    $categoryBooks = Book::with('category')
                        ->where('category_id', $cat->id)
                        ->limit(10)
                        ->get();

                    if ($categoryBooks->isNotEmpty()) {
                        return response()->json([
                            'success' => true,
                            'response' => $this->getCategoryResponse($cat->name, $categoryBooks),
                            'user_query' => $queryText,
                            'books' => $categoryBooks->toArray(),
                            'category' => $cat->name,
                            'conversation_id' => $conversationId,
                        ]);
                    }
                }
            }

            // ==================== HANDLE CEK STOK ====================
            if (str_contains($lowerQuery, 'stok') || str_contains($lowerQuery, 'tersedia') || str_contains($lowerQuery, 'stock')) {
                // Extract book title from query
                $bookTitle = str_replace(['stok', 'stock', 'tersedia', 'buku', 'cek'], '', $lowerQuery);
                $bookTitle = trim($bookTitle);

                if (!empty($bookTitle)) {
                    $book = Book::with('category')
                        ->where('title', 'LIKE', "%{$bookTitle}%")
                        ->first();

                    if ($book) {
                        return response()->json([
                            'success' => true,
                            'response' => $this->getStockResponse($book),
                            'user_query' => $queryText,
                            'books' => [$book->toArray()],
                            'conversation_id' => $conversationId,
                        ]);
                    }
                }
            }

            // ==================== EXTRACT BUDGET ====================
            $budget = $this->extractBudgetFromQuery($queryText);

            // ==================== SEARCH BOOKS ====================
            if ($budget) {
                // Search by budget
                $books = Book::with('category')
                    ->where('price', '<=', $budget)
                    ->orderBy('price', 'desc')
                    ->limit(5)
                    ->get();

                if ($books->isEmpty()) {
                    $cheapestBook = Book::with('category')
                        ->orderBy('price', 'asc')
                        ->first();

                    if ($cheapestBook) {
                        return response()->json([
                            'success' => true,
                            'response' => $this->getNoBudgetResponse($budget, $cheapestBook),
                            'user_query' => $queryText,
                            'books' => [],
                            'budget' => $budget,
                            'conversation_id' => $conversationId,
                        ]);
                    }
                }
            } else {
                // Search by title, author, or category
                $books = Book::with('category')
                    ->where('title', 'LIKE', "%{$queryText}%")
                    ->orWhere('author', 'LIKE', "%{$queryText}%")
                    ->orWhereHas('category', function ($q) use ($queryText) {
                        $q->where('name', 'LIKE', "%{$queryText}%");
                    })
                    ->limit(5)
                    ->get();
            }

            // ==================== GENERATE RESPONSE ====================
            $response = $this->generateResponse($queryText, $books, $budget);

            return response()->json([
                'success' => true,
                'response' => $response,
                'user_query' => $queryText,
                'books' => $books->toArray(),
                'budget' => $budget,
                'conversation_id' => $conversationId,
            ]);

        } catch (Exception $e) {
            Log::error('AI Chat Error: ' . $e->getMessage());

            $books = Book::with('category')->limit(5)->get();
            $fallbackResponse = $this->generateFallbackResponse($request->input('query', ''), $books);

            return response()->json([
                'success' => false,
                'response' => $fallbackResponse,
                'user_query' => $request->input('query', ''),
                'books' => $books->toArray(),
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get greeting response.
     */
    private function getGreetingResponse(): string
    {
        return "👋 *Halo! Selamat datang di Book Assistant!*\n\n" .
               "Saya asisten buku Anda. Ada yang bisa saya bantu?\n\n" .
               "📚 *Yang bisa saya lakukan:*\n" .
               "• 🔍 Mencari buku berdasarkan judul atau penulis\n" .
               "• 💰 Mengecek harga buku\n" .
               "• 🎯 Merekomendasikan buku sesuai budget\n" .
               "• 📖 Menampilkan daftar buku yang tersedia\n" .
               "• 📁 Mencari buku berdasarkan kategori\n" .
               "• 📦 Mengecek stok buku\n\n" .
               "💡 *Contoh pertanyaan:*\n" .
               "• \"Buku programming\"\n" .
               "• \"Atomic Habits harganya berapa?\"\n" .
               "• \"Rekomendasi buku budget 200rb\"\n" .
               "• \"Daftar semua buku\"\n" .
               "• \"Buku kategori self development\"\n\n" .
               "Silakan ketik pertanyaan Anda! 😊";
    }

    /**
     * Get help response.
     */
    private function getHelpResponse(): string
    {
        return "📚 *Book Assistant - Panduan Penggunaan*\n\n" .
               "✨ *Yang bisa saya lakukan:*\n\n" .
               "1. 🔍 *Mencari Buku*\n" .
               "   Contoh: \"buku programming\", \"novel fiksi\"\n\n" .
               "2. 💰 *Cek Harga*\n" .
               "   Contoh: \"Atomic Habits harganya berapa?\"\n\n" .
               "3. 🎯 *Rekomendasi Budget*\n" .
               "   Contoh: \"rekomendasi buku budget 200rb\"\n\n" .
               "4. 📖 *Daftar Buku*\n" .
               "   Contoh: \"daftar semua buku\", \"tampilkan buku\"\n\n" .
               "5. ✍️ *Cari berdasarkan Penulis*\n" .
               "   Contoh: \"buku karya Andrea Hirata\"\n\n" .
               "6. 📁 *Cari berdasarkan Kategori*\n" .
               "   Contoh: \"buku self development\", \"buku teknologi\"\n\n" .
               "7. 📦 *Cek Stok*\n" .
               "   Contoh: \"stok Atomic Habits\", \"buku tersedia\"\n\n" .
               "8. 💰 *Filter Harga*\n" .
               "   Contoh: \"buku dibawah 100rb\", \"buku maksimal 150rb\"\n\n" .
               "Silakan coba salah satu pertanyaan di atas! 😊";
    }

    /**
     * Get all books response.
     */
    private function getAllBooksResponse($books): string
    {
        $response = "📚 *Daftar Buku yang Tersedia*\n\n";

        // Group by category
        $grouped = $books->groupBy('category.name');

        foreach ($grouped as $category => $categoryBooks) {
            $response .= "📁 *{$category}*\n";
            foreach ($categoryBooks as $index => $book) {
                $response .= "   {$book->title} - {$book->author}\n";
                $response .= "   💰 Rp " . number_format($book->price, 0, ',', '.') . "\n";
                if ($index < $categoryBooks->count() - 1) {
                    $response .= "\n";
                }
            }
            $response .= "\n";
        }

        $response .= "📊 *Total:* " . Book::count() . " buku tersedia\n";
        $response .= "💡 Ketik 'help' untuk bantuan lebih lanjut";

        return $response;
    }

    /**
     * Get category response.
     */
    private function getCategoryResponse(string $categoryName, $books): string
    {
        $response = "📁 *Buku Kategori: {$categoryName}*\n\n";
        $response .= "Ditemukan " . $books->count() . " buku:\n\n";

        foreach ($books as $index => $book) {
            $response .= ($index + 1) . ". 📖 *{$book->title}*\n";
            $response .= "   ✍️ Penulis: {$book->author}\n";
            $response .= "   💰 Harga: Rp " . number_format($book->price, 0, ',', '.') . "\n";
            $response .= "   📦 Stok: " . ($book->stock > 0 ? "✅ Tersedia ({$book->stock})" : "❌ Habis") . "\n\n";
        }

        return $response;
    }

    /**
     * Get stock response.
     */
    private function getStockResponse($book): string
    {
        $response = "📖 *Cek Stok Buku*\n\n";
        $response .= "• Judul: *{$book->title}*\n";
        $response .= "• Penulis: {$book->author}\n";
        $response .= "• Harga: Rp " . number_format($book->price, 0, ',', '.') . "\n";

        if ($book->stock > 0) {
            $response .= "• Stok: ✅ *Tersedia* ({$book->stock} copy)\n";
            if ($book->stock < 5) {
                $response .= "⚠️ *Stok terbatas!* Segera pesan sebelum habis.\n";
            }
        } else {
            $response .= "• Stok: ❌ *Habis*\n";
            $response .= "💡 Silakan cek buku lain yang tersedia.\n";
        }

        return $response;
    }

    /**
     * Get no budget response.
     */
    private function getNoBudgetResponse(int $budget, $cheapestBook): string
    {
        $response = "📚 *Rekomendasi Buku dengan Budget Rp " . number_format($budget, 0, ',', '.') . "*\n\n";
        $response .= "❌ Maaf, tidak ada buku yang harganya ≤ Rp " . number_format($budget, 0, ',', '.') . "\n\n";
        $response .= "💡 *Rekomendasi:*\n";
        $response .= "Buku termurah kami:\n";
        $response .= "• *{$cheapestBook->title}* - {$cheapestBook->author}\n";
        $response .= "  💰 Rp " . number_format($cheapestBook->price, 0, ',', '.') . "\n\n";
        $response .= "📊 *Statistik Harga Buku:*\n";
        $response .= "• Termurah: Rp " . number_format(Book::min('price'), 0, ',', '.') . "\n";
        $response .= "• Termahal: Rp " . number_format(Book::max('price'), 0, ',', '.') . "\n";
        $response .= "• Rata-rata: Rp " . number_format(Book::avg('price'), 0, ',', '.') . "\n\n";
        $response .= "💰 *Saran:* Tingkatkan budget Anda menjadi sekitar Rp " . number_format($cheapestBook->price, 0, ',', '.') . " untuk bisa membeli buku. 😊";

        return $response;
    }

    /**
     * Extract budget from user query.
     */
    private function extractBudgetFromQuery(string $query): ?int
    {
        $patterns = [
            '/budget\s*[:]?\s*(\d+(?:[\.]\d+)?)\s*(?:rb|ribu|k)?/i',
            '/anggaran\s*[:]?\s*(\d+(?:[\.]\d+)?)\s*(?:rb|ribu|k)?/i',
            '/dibawah\s*(\d+(?:[\.]\d+)?)\s*(?:rb|ribu|k)?/i',
            '/kurang dari\s*(\d+(?:[\.]\d+)?)\s*(?:rb|ribu|k)?/i',
            '/maksimal\s*(\d+(?:[\.]\d+)?)\s*(?:rb|ribu|k)?/i',
            '/max\s*(\d+(?:[\.]\d+)?)\s*(?:rb|ribu|k)?/i',
            '/rp\s*(\d+(?:[\.]\d+)?)\s*(?:rb|ribu|k)?/i',
            '/(\d+(?:[\.]\d+)?)\s*(?:rb|ribu|k)?\s*(?:budget|anggaran)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $query, $matches)) {
                $amount = str_replace('.', '', $matches[1]);
                $amount = (int) $amount;

                if (isset($matches[2]) && preg_match('/(rb|ribu|k)/i', $matches[2])) {
                    $amount = $amount * 1000;
                } elseif (preg_match('/(rb|ribu|k)/i', $matches[0])) {
                    $amount = $amount * 1000;
                }

                if ($amount >= 1000) {
                    return $amount;
                }
            }
        }

        if (preg_match('/(\d+)\s*(?:rb|ribu|k)/i', $query, $matches)) {
            $amount = (int) $matches[1] * 1000;
            if ($amount >= 1000) {
                return $amount;
            }
        }

        if (preg_match('/(\d{2,4})(?:\s*(?:rb|ribu))?/i', $query, $matches)) {
            $amount = (int) $matches[1];
            if ($amount < 1000 && $amount >= 10) {
                return $amount * 1000;
            }
            if ($amount >= 1000) {
                return $amount;
            }
        }

        return null;
    }

    /**
     * Generate response based on search results.
     */
    private function generateResponse(string $query, $books, ?int $budget = null): string
    {
        if ($budget && $books->isNotEmpty()) {
            $response = "📚 *Rekomendasi Buku dengan Budget Rp " . number_format($budget, 0, ',', '.') . "*\n\n";
            $response .= "Ditemukan " . $books->count() . " buku yang sesuai:\n\n";

            $totalPrice = 0;
            foreach ($books as $index => $book) {
                $response .= ($index + 1) . ". 📖 *{$book->title}*\n";
                $response .= "   ✍️ Penulis: {$book->author}\n";
                $response .= "   💰 Harga: Rp " . number_format($book->price, 0, ',', '.') . "\n";
                if ($book->category) {
                    $response .= "   📚 Kategori: {$book->category->name}\n";
                }
                $response .= "   📦 Stok: " . ($book->stock > 0 ? "✅ Tersedia ({$book->stock})" : "❌ Habis") . "\n\n";
                $totalPrice += $book->price;
            }

            $response .= "──────────────────────────────────\n";
            $response .= "💰 *Total belanja:* Rp " . number_format($totalPrice, 0, ',', '.') . "\n";

            if ($totalPrice <= $budget) {
                $response .= "✅ *Masih dalam budget!* Sisa: Rp " . number_format($budget - $totalPrice, 0, ',', '.') . "\n";
            }

            $response .= "\n💡 *Tips:* ";
            if ($books->count() > 1) {
                $response .= "Anda bisa memilih salah satu buku di atas yang paling sesuai dengan minat Anda.";
            } else {
                $response .= "Buku ini sangat direkomendasikan untuk Anda!";
            }

            return $response;
        }

        if ($budget && $books->isEmpty()) {
            $cheapest = Book::orderBy('price', 'asc')->first();
            $avgPrice = Book::avg('price');

            $response = "📚 *Pencarian dengan Budget Rp " . number_format($budget, 0, ',', '.') . "*\n\n";
            $response .= "❌ Maaf, tidak ada buku dengan harga ≤ Rp " . number_format($budget, 0, ',', '.') . "\n\n";
            $response .= "📊 *Info Harga Buku:*\n";
            $response .= "• Termurah: Rp " . number_format($cheapest->price ?? 0, 0, ',', '.') . "\n";
            $response .= "• Rata-rata: Rp " . number_format($avgPrice, 0, ',', '.') . "\n\n";
            $response .= "💡 *Saran:*\n";
            $response .= "• Tingkatkan budget menjadi sekitar Rp " . number_format($cheapest->price ?? 0, 0, ',', '.') . "\n";
            $response .= "• Atau cari buku bekas\n";
            $response .= "• Atau lihat kategori buku yang lebih murah\n";

            return $response;
        }

        if ($books->isNotEmpty()) {
            $response = "📚 *Hasil Pencarian Buku*\n\n";
            $response .= "Ditemukan " . $books->count() . " buku untuk kata kunci '{$query}':\n\n";

            foreach ($books as $index => $book) {
                $response .= ($index + 1) . ". 📖 *{$book->title}*\n";
                $response .= "   ✍️ Penulis: {$book->author}\n";
                $response .= "   💰 Harga: Rp " . number_format($book->price, 0, ',', '.') . "\n";
                if ($book->category) {
                    $response .= "   📚 Kategori: {$book->category->name}\n";
                }
                $response .= "   📦 Stok: " . ($book->stock > 0 ? "✅ Tersedia ({$book->stock})" : "❌ Habis") . "\n\n";
            }

            return $response;
        }

        return "📚 Maaf, untuk pencarian '{$query}' tidak ditemukan buku.\n\n" .
               "💡 *Saran:*\n" .
               "• Coba dengan kata kunci yang berbeda\n" .
               "• Cek judul atau penulis yang lebih spesifik\n" .
               "• Atau ketik 'daftar buku' untuk melihat semua koleksi kami\n\n" .
               "Ada yang bisa saya bantu lagi? 😊";
    }

    /**
     * Generate fallback response.
     */
    private function generateFallbackResponse(string $query, $books): string
    {
        if ($books->isEmpty()) {
            return "📚 Maaf, untuk pencarian '{$query}' tidak ditemukan buku.\n\nSilakan coba kata kunci lain. 😊";
        }

        $response = "📚 *Hasil Pencarian Buku*\n\n";
        $response .= "Ditemukan " . $books->count() . " buku:\n\n";

        foreach ($books as $index => $book) {
            $response .= ($index + 1) . ". *{$book->title}*\n";
            $response .= "   ✍️ {$book->author}\n";
            $response .= "   💰 Rp " . number_format($book->price, 0, ',', '.') . "\n\n";
        }

        return $response;
    }
}

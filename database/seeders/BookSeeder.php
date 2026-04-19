<?php
// database/seeders/BookSeeder.php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Categori;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan kategori sudah ada
        $categories = [
            'Teknologi', 'Fiksi', 'Non-Fiksi', 'Bisnis', 'Self Development',
            'Psikologi', 'Sejarah', 'Filsafat', 'Agama', 'Kesehatan',
            'Pendidikan', 'Seni', 'Travel', 'Memasak', 'Olahraga',
            'Politik', 'Ekonomi', 'Sains', 'Biografi', 'Anak'
        ];

        foreach ($categories as $catName) {
            Categori::firstOrCreate(['name' => $catName]);
        }

        $books = [
            // TEKNOLOGI (1-15)
            ['title' => 'Laravel Untuk Pemula', 'author' => 'Andi Saputra', 'publisher' => 'TechPress', 'year' => 2024, 'price' => 150000, 'stock' => 10, 'category' => 'Teknologi'],
            ['title' => 'Clean Code', 'author' => 'Robert Martin', 'publisher' => 'Prentice Hall', 'year' => 2008, 'price' => 250000, 'stock' => 8, 'category' => 'Teknologi'],
            ['title' => 'The Pragmatic Programmer', 'author' => 'David Thomas', 'publisher' => 'Addison-Wesley', 'year' => 2019, 'price' => 280000, 'stock' => 5, 'category' => 'Teknologi'],
            ['title' => 'JavaScript: The Good Parts', 'author' => 'Douglas Crockford', 'publisher' => 'O\'Reilly', 'year' => 2008, 'price' => 180000, 'stock' => 6, 'category' => 'Teknologi'],
            ['title' => 'Python Crash Course', 'author' => 'Eric Matthes', 'publisher' => 'No Starch Press', 'year' => 2019, 'price' => 220000, 'stock' => 7, 'category' => 'Teknologi'],
            ['title' => 'Database Design', 'author' => 'Michael J. Hernandez', 'publisher' => 'Addison-Wesley', 'year' => 2013, 'price' => 195000, 'stock' => 4, 'category' => 'Teknologi'],
            ['title' => 'React: Up & Running', 'author' => 'Stoyan Stefanov', 'publisher' => 'O\'Reilly', 'year' => 2021, 'price' => 210000, 'stock' => 6, 'category' => 'Teknologi'],
            ['title' => 'Docker Deep Dive', 'author' => 'Nigel Poulton', 'publisher' => 'Self Published', 'year' => 2020, 'price' => 175000, 'stock' => 5, 'category' => 'Teknologi'],
            ['title' => 'Kubernetes Up & Running', 'author' => 'Kelsey Hightower', 'publisher' => 'O\'Reilly', 'year' => 2017, 'price' => 320000, 'stock' => 3, 'category' => 'Teknologi'],
            ['title' => 'Machine Learning Yearning', 'author' => 'Andrew Ng', 'publisher' => 'DeepLearning.AI', 'year' => 2018, 'price' => 150000, 'stock' => 4, 'category' => 'Teknologi'],
            ['title' => 'Artificial Intelligence', 'author' => 'Stuart Russell', 'publisher' => 'Pearson', 'year' => 2020, 'price' => 350000, 'stock' => 2, 'category' => 'Teknologi'],
            ['title' => 'Cybersecurity Essentials', 'author' => 'Charles J. Brooks', 'publisher' => 'Sybex', 'year' => 2018, 'price' => 290000, 'stock' => 5, 'category' => 'Teknologi'],
            ['title' => 'Cloud Computing', 'author' => 'Thomas Erl', 'publisher' => 'Prentice Hall', 'year' => 2013, 'price' => 310000, 'stock' => 3, 'category' => 'Teknologi'],
            ['title' => 'DevOps Handbook', 'author' => 'Gene Kim', 'publisher' => 'IT Revolution', 'year' => 2016, 'price' => 265000, 'stock' => 4, 'category' => 'Teknologi'],
            ['title' => 'Code Complete', 'author' => 'Steve McConnell', 'publisher' => 'Microsoft Press', 'year' => 2004, 'price' => 340000, 'stock' => 2, 'category' => 'Teknologi'],

            // FIKSI (16-30)
            ['title' => 'Bumi Manusia', 'author' => 'Pramoedya Ananta Toer', 'publisher' => 'Hasta Mitra', 'year' => 1980, 'price' => 95000, 'stock' => 10, 'category' => 'Fiksi'],
            ['title' => 'Laskar Pelangi', 'author' => 'Andrea Hirata', 'publisher' => 'Bentang Pustaka', 'year' => 2005, 'price' => 89000, 'stock' => 12, 'category' => 'Fiksi'],
            ['title' => 'Dilan 1990', 'author' => 'Pidi Baiq', 'publisher' => 'Pastel Books', 'year' => 2014, 'price' => 75000, 'stock' => 15, 'category' => 'Fiksi'],
            ['title' => 'Harry Potter and the Sorcerer\'s Stone', 'author' => 'J.K. Rowling', 'publisher' => 'Scholastic', 'year' => 1997, 'price' => 185000, 'stock' => 8, 'category' => 'Fiksi'],
            ['title' => 'The Alchemist', 'author' => 'Paulo Coelho', 'publisher' => 'HarperCollins', 'year' => 1988, 'price' => 120000, 'stock' => 10, 'category' => 'Fiksi'],
            ['title' => 'To Kill a Mockingbird', 'author' => 'Harper Lee', 'publisher' => 'J.B. Lippincott', 'year' => 1960, 'price' => 135000, 'stock' => 6, 'category' => 'Fiksi'],
            ['title' => 'Pride and Prejudice', 'author' => 'Jane Austen', 'publisher' => 'T. Egerton', 'year' => 1813, 'price' => 110000, 'stock' => 5, 'category' => 'Fiksi'],
            ['title' => 'The Great Gatsby', 'author' => 'F. Scott Fitzgerald', 'publisher' => 'Scribner', 'year' => 1925, 'price' => 105000, 'stock' => 7, 'category' => 'Fiksi'],
            ['title' => '1984', 'author' => 'George Orwell', 'publisher' => 'Secker & Warburg', 'year' => 1949, 'price' => 125000, 'stock' => 9, 'category' => 'Fiksi'],
            ['title' => 'Animal Farm', 'author' => 'George Orwell', 'publisher' => 'Secker & Warburg', 'year' => 1945, 'price' => 95000, 'stock' => 8, 'category' => 'Fiksi'],
            ['title' => 'The Hobbit', 'author' => 'J.R.R. Tolkien', 'publisher' => 'George Allen & Unwin', 'year' => 1937, 'price' => 145000, 'stock' => 6, 'category' => 'Fiksi'],
            ['title' => 'The Lord of the Rings', 'author' => 'J.R.R. Tolkien', 'publisher' => 'George Allen & Unwin', 'year' => 1954, 'price' => 295000, 'stock' => 4, 'category' => 'Fiksi'],
            ['title' => 'Cantik Itu Luka', 'author' => 'Eka Kurniawan', 'publisher' => 'Gramedia', 'year' => 2002, 'price' => 105000, 'stock' => 7, 'category' => 'Fiksi'],
            ['title' => 'Sang Pemimpi', 'author' => 'Andrea Hirata', 'publisher' => 'Bentang Pustaka', 'year' => 2006, 'price' => 85000, 'stock' => 9, 'category' => 'Fiksi'],
            ['title' => 'Perahu Kertas', 'author' => 'Dee Lestari', 'publisher' => 'Bentang Pustaka', 'year' => 2009, 'price' => 95000, 'stock' => 8, 'category' => 'Fiksi'],

            // SELF DEVELOPMENT (31-45)
            ['title' => 'Atomic Habits', 'author' => 'James Clear', 'publisher' => 'Gramedia', 'year' => 2019, 'price' => 89000, 'stock' => 15, 'category' => 'Self Development'],
            ['title' => 'The 7 Habits of Highly Effective People', 'author' => 'Stephen Covey', 'publisher' => 'Free Press', 'year' => 1989, 'price' => 175000, 'stock' => 8, 'category' => 'Self Development'],
            ['title' => 'How to Win Friends and Influence People', 'author' => 'Dale Carnegie', 'publisher' => 'Simon & Schuster', 'year' => 1936, 'price' => 125000, 'stock' => 10, 'category' => 'Self Development'],
            ['title' => 'Think and Grow Rich', 'author' => 'Napoleon Hill', 'publisher' => 'The Ralston Society', 'year' => 1937, 'price' => 110000, 'stock' => 7, 'category' => 'Self Development'],
            ['title' => 'The Power of Habit', 'author' => 'Charles Duhigg', 'publisher' => 'Random House', 'year' => 2012, 'price' => 140000, 'stock' => 6, 'category' => 'Self Development'],
            ['title' => 'Mindset', 'author' => 'Carol S. Dweck', 'publisher' => 'Random House', 'year' => 2006, 'price' => 155000, 'stock' => 5, 'category' => 'Self Development'],
            ['title' => 'Grit', 'author' => 'Angela Duckworth', 'publisher' => 'Scribner', 'year' => 2016, 'price' => 148000, 'stock' => 6, 'category' => 'Self Development'],
            ['title' => 'The Subtle Art of Not Giving a F*ck', 'author' => 'Mark Manson', 'publisher' => 'HarperOne', 'year' => 2016, 'price' => 135000, 'stock' => 9, 'category' => 'Self Development'],
            ['title' => 'You Are a Badass', 'author' => 'Jen Sincero', 'publisher' => 'Running Press', 'year' => 2013, 'price' => 120000, 'stock' => 7, 'category' => 'Self Development'],
            ['title' => 'The 5 AM Club', 'author' => 'Robin Sharma', 'publisher' => 'HarperCollins', 'year' => 2018, 'price' => 165000, 'stock' => 5, 'category' => 'Self Development'],
            ['title' => 'Deep Work', 'author' => 'Cal Newport', 'publisher' => 'Grand Central Publishing', 'year' => 2016, 'price' => 160000, 'stock' => 4, 'category' => 'Self Development'],
            ['title' => 'Ikigai', 'author' => 'Hector Garcia', 'publisher' => 'Penguin Books', 'year' => 2016, 'price' => 115000, 'stock' => 8, 'category' => 'Self Development'],
            ['title' => 'The Miracle Morning', 'author' => 'Hal Elrod', 'publisher' => 'Hal Elrod', 'year' => 2012, 'price' => 125000, 'stock' => 6, 'category' => 'Self Development'],
            ['title' => 'Start With Why', 'author' => 'Simon Sinek', 'publisher' => 'Portfolio', 'year' => 2009, 'price' => 155000, 'stock' => 5, 'category' => 'Self Development'],
            ['title' => 'The Courage to Be Disliked', 'author' => 'Ichiro Kishimi', 'publisher' => 'Atria Books', 'year' => 2013, 'price' => 130000, 'stock' => 7, 'category' => 'Self Development'],

            // BISNIS & EKONOMI (46-60)
            ['title' => 'Psychology of Money', 'author' => 'Morgan Housel', 'publisher' => 'Harriman House', 'year' => 2020, 'price' => 120000, 'stock' => 10, 'category' => 'Bisnis'],
            ['title' => 'Rich Dad Poor Dad', 'author' => 'Robert Kiyosaki', 'publisher' => 'Plata Publishing', 'year' => 1997, 'price' => 105000, 'stock' => 12, 'category' => 'Bisnis'],
            ['title' => 'The Intelligent Investor', 'author' => 'Benjamin Graham', 'publisher' => 'HarperCollins', 'year' => 1949, 'price' => 195000, 'stock' => 5, 'category' => 'Bisnis'],
            ['title' => 'Zero to One', 'author' => 'Peter Thiel', 'publisher' => 'Crown Business', 'year' => 2014, 'price' => 170000, 'stock' => 6, 'category' => 'Bisnis'],
            ['title' => 'The Lean Startup', 'author' => 'Eric Ries', 'publisher' => 'Crown Business', 'year' => 2011, 'price' => 160000, 'stock' => 7, 'category' => 'Bisnis'],
            ['title' => 'Good to Great', 'author' => 'Jim Collins', 'publisher' => 'HarperBusiness', 'year' => 2001, 'price' => 185000, 'stock' => 4, 'category' => 'Bisnis'],
            ['title' => 'Thinking, Fast and Slow', 'author' => 'Daniel Kahneman', 'publisher' => 'Farrar, Straus and Giroux', 'year' => 2011, 'price' => 190000, 'stock' => 5, 'category' => 'Bisnis'],
            ['title' => 'The Personal MBA', 'author' => 'Josh Kaufman', 'publisher' => 'Portfolio', 'year' => 2010, 'price' => 175000, 'stock' => 6, 'category' => 'Bisnis'],
            ['title' => 'Freakonomics', 'author' => 'Steven Levitt', 'publisher' => 'William Morrow', 'year' => 2005, 'price' => 145000, 'stock' => 7, 'category' => 'Bisnis'],
            ['title' => 'The E-Myth Revisited', 'author' => 'Michael Gerber', 'publisher' => 'HarperCollins', 'year' => 1995, 'price' => 140000, 'stock' => 5, 'category' => 'Bisnis'],
            ['title' => 'Sapiens', 'author' => 'Yuval Noah Harari', 'publisher' => 'Harper', 'year' => 2011, 'price' => 165000, 'stock' => 8, 'category' => 'Sejarah'],
            ['title' => 'Homo Deus', 'author' => 'Yuval Noah Harari', 'publisher' => 'Harper', 'year' => 2015, 'price' => 170000, 'stock' => 6, 'category' => 'Sejarah'],
            ['title' => 'Guns, Germs, and Steel', 'author' => 'Jared Diamond', 'publisher' => 'W.W. Norton', 'year' => 1997, 'price' => 185000, 'stock' => 4, 'category' => 'Sejarah'],
            ['title' => 'The Silk Roads', 'author' => 'Peter Frankopan', 'publisher' => 'Knopf', 'year' => 2015, 'price' => 210000, 'stock' => 3, 'category' => 'Sejarah'],
            ['title' => 'Sejarah Dunia yang Disembunyikan', 'author' => 'John Keegan', 'publisher' => 'Pustaka Alvabet', 'year' => 2014, 'price' => 195000, 'stock' => 4, 'category' => 'Sejarah'],

            // PSIKOLOGI & FILSAFAT (61-75)
            ['title' => 'Filsafat Teras', 'author' => 'Henry Manampiring', 'publisher' => 'Gramedia', 'year' => 2018, 'price' => 89000, 'stock' => 10, 'category' => 'Filsafat'],
            ['title' => 'Sophie\'s World', 'author' => 'Jostein Gaarder', 'publisher' => 'Berkley Books', 'year' => 1991, 'price' => 135000, 'stock' => 6, 'category' => 'Filsafat'],
            ['title' => 'Meditations', 'author' => 'Marcus Aurelius', 'publisher' => 'Penguin Classics', 'year' => 180, 'price' => 110000, 'stock' => 7, 'category' => 'Filsafat'],
            ['title' => 'The Art of War', 'author' => 'Sun Tzu', 'publisher' => 'Oxford University Press', 'year' => 500, 'price' => 95000, 'stock' => 8, 'category' => 'Filsafat'],
            ['title' => 'Thus Spoke Zarathustra', 'author' => 'Friedrich Nietzsche', 'publisher' => 'Penguin Classics', 'year' => 1883, 'price' => 125000, 'stock' => 5, 'category' => 'Filsafat'],
            ['title' => 'The Republic', 'author' => 'Plato', 'publisher' => 'Penguin Classics', 'year' => 375, 'price' => 115000, 'stock' => 6, 'category' => 'Filsafat'],
            ['title' => 'Critique of Pure Reason', 'author' => 'Immanuel Kant', 'publisher' => 'Penguin Classics', 'year' => 1781, 'price' => 145000, 'stock' => 4, 'category' => 'Filsafat'],
            ['title' => 'Being and Nothingness', 'author' => 'Jean-Paul Sartre', 'publisher' => 'Washington Square Press', 'year' => 1943, 'price' => 160000, 'stock' => 3, 'category' => 'Filsafat'],
            ['title' => 'The Myth of Sisyphus', 'author' => 'Albert Camus', 'publisher' => 'Vintage Books', 'year' => 1942, 'price' => 105000, 'stock' => 6, 'category' => 'Filsafat'],
            ['title' => 'Psychology of Happiness', 'author' => 'Michael Argyle', 'publisher' => 'Routledge', 'year' => 2001, 'price' => 155000, 'stock' => 5, 'category' => 'Psikologi'],
            ['title' => 'Emotional Intelligence', 'author' => 'Daniel Goleman', 'publisher' => 'Bantam Books', 'year' => 1995, 'price' => 148000, 'stock' => 7, 'category' => 'Psikologi'],
            ['title' => 'The Psychology of Money', 'author' => 'Morgan Housel', 'publisher' => 'Harriman House', 'year' => 2020, 'price' => 120000, 'stock' => 10, 'category' => 'Psikologi'],
            ['title' => 'Influence', 'author' => 'Robert Cialdini', 'publisher' => 'HarperBusiness', 'year' => 1984, 'price' => 138000, 'stock' => 6, 'category' => 'Psikologi'],
            ['title' => 'Thinking, Fast and Slow', 'author' => 'Daniel Kahneman', 'publisher' => 'Farrar, Straus and Giroux', 'year' => 2011, 'price' => 190000, 'stock' => 5, 'category' => 'Psikologi'],
            ['title' => 'The Happiness Project', 'author' => 'Gretchen Rubin', 'publisher' => 'Harper', 'year' => 2009, 'price' => 125000, 'stock' => 7, 'category' => 'Psikologi'],

            // KESEHATAN & OLAHRAGA (76-85)
            ['title' => 'Why We Sleep', 'author' => 'Matthew Walker', 'publisher' => 'Scribner', 'year' => 2017, 'price' => 155000, 'stock' => 6, 'category' => 'Kesehatan'],
            ['title' => 'The Body Keeps the Score', 'author' => 'Bessel van der Kolk', 'publisher' => 'Viking', 'year' => 2014, 'price' => 175000, 'stock' => 5, 'category' => 'Kesehatan'],
            ['title' => 'How Not to Die', 'author' => 'Michael Greger', 'publisher' => 'Flatiron Books', 'year' => 2015, 'price' => 165000, 'stock' => 4, 'category' => 'Kesehatan'],
            ['title' => 'The China Study', 'author' => 'T. Colin Campbell', 'publisher' => 'BenBella Books', 'year' => 2005, 'price' => 145000, 'stock' => 5, 'category' => 'Kesehatan'],
            ['title' => 'Atomic Habits for Health', 'author' => 'James Clear', 'publisher' => 'Gramedia', 'year' => 2021, 'price' => 95000, 'stock' => 8, 'category' => 'Kesehatan'],
            ['title' => 'Born to Run', 'author' => 'Christopher McDougall', 'publisher' => 'Knopf', 'year' => 2009, 'price' => 135000, 'stock' => 6, 'category' => 'Olahraga'],
            ['title' => 'The Sports Gene', 'author' => 'David Epstein', 'publisher' => 'Portfolio', 'year' => 2013, 'price' => 140000, 'stock' => 5, 'category' => 'Olahraga'],
            ['title' => 'Open', 'author' => 'Andre Agassi', 'publisher' => 'Knopf', 'year' => 2009, 'price' => 125000, 'stock' => 4, 'category' => 'Olahraga'],
            ['title' => 'The Inner Game of Tennis', 'author' => 'W. Timothy Gallwey', 'publisher' => 'Random House', 'year' => 1974, 'price' => 115000, 'stock' => 5, 'category' => 'Olahraga'],
            ['title' => 'Fitness Mindset', 'author' => 'Brian Keane', 'publisher' => 'Aster', 'year' => 2017, 'price' => 120000, 'stock' => 6, 'category' => 'Olahraga'],

            // SENI & TRAVEL (86-95)
            ['title' => 'Steal Like an Artist', 'author' => 'Austin Kleon', 'publisher' => 'Workman Publishing', 'year' => 2012, 'price' => 95000, 'stock' => 8, 'category' => 'Seni'],
            ['title' => 'The War of Art', 'author' => 'Steven Pressfield', 'publisher' => 'Black Irish Entertainment', 'year' => 2002, 'price' => 105000, 'stock' => 6, 'category' => 'Seni'],
            ['title' => 'Show Your Work!', 'author' => 'Austin Kleon', 'publisher' => 'Workman Publishing', 'year' => 2014, 'price' => 89000, 'stock' => 7, 'category' => 'Seni'],
            ['title' => 'Creative Confidence', 'author' => 'Tom Kelley', 'publisher' => 'Crown Business', 'year' => 2013, 'price' => 145000, 'stock' => 5, 'category' => 'Seni'],
            ['title' => 'Art & Fear', 'author' => 'David Bayles', 'publisher' => 'Image Continuum Press', 'year' => 1993, 'price' => 110000, 'stock' => 5, 'category' => 'Seni'],
            ['title' => 'Lonely Planet: Indonesia', 'author' => 'Lonely Planet', 'publisher' => 'Lonely Planet', 'year' => 2022, 'price' => 185000, 'stock' => 4, 'category' => 'Travel'],
            ['title' => 'Eat, Pray, Love', 'author' => 'Elizabeth Gilbert', 'publisher' => 'Viking', 'year' => 2006, 'price' => 130000, 'stock' => 6, 'category' => 'Travel'],
            ['title' => 'A Walk in the Woods', 'author' => 'Bill Bryson', 'publisher' => 'Broadway Books', 'year' => 1998, 'price' => 120000, 'stock' => 5, 'category' => 'Travel'],
            ['title' => 'The Art of Travel', 'author' => 'Alain de Botton', 'publisher' => 'Pantheon', 'year' => 2002, 'price' => 115000, 'stock' => 5, 'category' => 'Travel'],
            ['title' => 'Into the Wild', 'author' => 'Jon Krakauer', 'publisher' => 'Villard', 'year' => 1996, 'price' => 108000, 'stock' => 6, 'category' => 'Travel'],

            // PENDIDIKAN & AGAMA (96-100)
            ['title' => 'Pedagogy of the Oppressed', 'author' => 'Paulo Freire', 'publisher' => 'Continuum', 'year' => 1968, 'price' => 125000, 'stock' => 5, 'category' => 'Pendidikan'],
            ['title' => 'The First 20 Hours', 'author' => 'Josh Kaufman', 'publisher' => 'Portfolio', 'year' => 2013, 'price' => 135000, 'stock' => 5, 'category' => 'Pendidikan'],
            ['title' => 'Make It Stick', 'author' => 'Peter C. Brown', 'publisher' => 'Belknap Press', 'year' => 2014, 'price' => 145000, 'stock' => 5, 'category' => 'Pendidikan'],
            ['title' => 'The Miracle of Mindfulness', 'author' => 'Thich Nhat Hanh', 'publisher' => 'Beacon Press', 'year' => 1975, 'price' => 95000, 'stock' => 6, 'category' => 'Agama'],
            ['title' => 'The Power of Now', 'author' => 'Eckhart Tolle', 'publisher' => 'New World Library', 'year' => 1997, 'price' => 125000, 'stock' => 6, 'category' => 'Agama'],
        ];

        foreach ($books as $bookData) {
            $category = Categori::where('name', $bookData['category'])->first();

            if ($category) {
                Book::create([
                    'title' => $bookData['title'],
                    'author' => $bookData['author'],
                    'publisher' => $bookData['publisher'],
                    'year' => $bookData['year'],
                    'price' => $bookData['price'],
                    'stock' => $bookData['stock'],
                    'category_id' => $category->id
                ]);
            }
        }

        $this->command->info('100 books seeded successfully!');
    }
}

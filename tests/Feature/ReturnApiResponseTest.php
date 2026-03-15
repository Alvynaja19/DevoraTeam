<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use App\Models\Borrowing;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReturnApiResponseTest extends TestCase
{
    use RefreshDatabase;

    public function test_lookup_qr()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create(['role' => 'member']);
        $book = Book::factory()->create(['stock' => 5]);

        $borrowing = Borrowing::create([
            'user_id' => $member->id,
            'book_id' => $book->id,
            'tanggal_pinjam' => now(),
            'tanggal_kembali' => now()->addDays(7),
            'status' => 'dipinjam',
            'return_qr' => 'RET-12345'
        ]);

        $response = $this->actingAs($admin)->getJson('/return/lookup?code=RET-12345');
        
        $response->assertStatus(200);
        $response->assertJson(['return_qr' => 'RET-12345']);
    }
}

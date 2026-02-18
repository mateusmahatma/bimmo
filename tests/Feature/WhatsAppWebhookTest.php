<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\Transaksi;

class WhatsAppWebhookTest extends TestCase
{
    // use RefreshDatabase; // Careful with this if connecting to real DB. Better to manually clean up or use transaction.
    // Given the environment, I'll just use the existing DB or create data and clean up.

    public function test_webhook_can_record_income()
    {
        // Setup
        $user = User::first(); // Assuming user 1 exists
        if (!$user) {
            $user = User::factory()->create();
        }

        // Ensure category exists
        $category = Pemasukan::firstOrCreate(
        ['nama' => 'GAJI_UNIQUE', 'id_user' => $user->id],
        ['kode_pemasukan' => 'M9999']
        );

        $payload = [
            'message' => 'MASUK 5000000 GAJI_UNIQUE Gaji Percobaan',
            'sender' => '628123456789'
        ];

        // Act
        $response = $this->postJson('/api/webhook/whatsapp', $payload);

        // Assert
        $response->assertStatus(200)
            ->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('transaksi', [
            'id_user' => $user->id,
            'pemasukan' => $category->id,
            'nominal_pemasukan' => 5000000,
            'keterangan' => 'Gaji Percobaan'
        ]);

        // Clean up (Optional, but good practice if not using RefreshDatabase)
        Transaksi::where('keterangan', 'Gaji Percobaan')->delete();
        $category->delete();
    }

    public function test_webhook_can_record_expense()
    {
        // Setup
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create();
        }

        // Ensure category exists
        $category = Pengeluaran::firstOrCreate(
        ['nama' => 'MAKAN_TEST', 'id_user' => $user->id],
        ['kode_pengeluaran' => 'K9999']
        );

        $payload = [
            'message' => 'KELUAR 25000 MAKAN_TEST Makan Siang',
            'sender' => '628123456789'
        ];

        // Act
        $response = $this->postJson('/api/webhook/whatsapp', $payload);

        // Assert
        $response->assertStatus(200)
            ->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('transaksi', [
            'id_user' => $user->id,
            'pengeluaran' => $category->id,
            'nominal' => 25000,
            'keterangan' => 'Makan Siang'
        ]);

        // Clean up
        Transaksi::where('keterangan', 'Makan Siang')->delete();
        $category->delete();
    }
}

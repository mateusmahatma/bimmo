<?php

namespace Tests\Feature;

use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class EditCategoryTest extends TestCase
{
    protected function createUser()
    {
        return User::create([
            'name' => 'Test User ' . uniqid(),
            'username' => 'testuser' . uniqid(),
            'email' => 'test' . uniqid() . '@example.com',
            'password' => Hash::make('password'),
        ]);
    }

    public function test_edit_pemasukan_returns_json_on_ajax()
    {
        $user = $this->createUser();
        $pemasukan = Pemasukan::create([
            'nama' => 'Test Pemasukan',
            'id_user' => $user->id
        ]);

        $response = $this->actingAs($user)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->getJson(route('pemasukan.edit', $pemasukan->id));

        $response->assertStatus(200)
            ->assertJson([
            'result' => [
                'id' => $pemasukan->id,
                'nama' => 'Test Pemasukan'
            ]
        ]);

        // Cleanup
        $pemasukan->delete();
        $user->delete();
    }

    public function test_update_pemasukan_returns_json_on_ajax()
    {
        $user = $this->createUser();
        $pemasukan = Pemasukan::create([
            'nama' => 'Test Pemasukan',
            'id_user' => $user->id
        ]);

        $response = $this->actingAs($user)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->putJson(route('pemasukan.update', $pemasukan->id), [
            'nama' => 'Updated Pemasukan'
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('pemasukan', [
            'id' => $pemasukan->id,
            'nama' => 'Updated Pemasukan'
        ]);

        // Cleanup
        $pemasukan->delete();
        $user->delete();
    }

    public function test_edit_pengeluaran_returns_json_on_ajax()
    {
        $user = $this->createUser();
        $pengeluaran = Pengeluaran::create([
            'nama' => 'Test Pengeluaran',
            'id_user' => $user->id
        ]);

        $response = $this->actingAs($user)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->getJson(route('pengeluaran.edit', $pengeluaran->id));

        $response->assertStatus(200)
            ->assertJson([
            'result' => [
                'id' => $pengeluaran->id,
                'nama' => 'Test Pengeluaran'
            ]
        ]);

        // Cleanup
        $pengeluaran->delete();
        $user->delete();
    }

    public function test_update_pengeluaran_returns_json_on_ajax()
    {
        $user = $this->createUser();
        $pengeluaran = Pengeluaran::create([
            'nama' => 'Test Pengeluaran',
            'id_user' => $user->id
        ]);

        $response = $this->actingAs($user)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->putJson(route('pengeluaran.update', $pengeluaran->id), [
            'nama' => 'Updated Pengeluaran'
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('pengeluaran', [
            'id' => $pengeluaran->id,
            'nama' => 'Updated Pengeluaran'
        ]);

        // Cleanup
        $pengeluaran->delete();
        $user->delete();
    }
}

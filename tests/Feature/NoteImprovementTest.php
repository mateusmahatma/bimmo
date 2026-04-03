<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteImprovementTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_user_can_store_note_with_rich_text()
    {
        $content = "<b>Important</b> task with <i>italics</i> and <ul><li>list</li></ul>";

        $response = $this->actingAs($this->user)->postJson('/notes', [
            'content' => $content
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('notes', [
            'id_user' => $this->user->id,
            'content' => $content
        ]);
    }

    public function test_note_content_is_sanitized()
    {
        $dirtyContent = "<script>alert('XSS')</script><b>Allowed</b> <img src='x' onerror='alert(1)'>";
        $expectedContent = "<b>Allowed</b> "; // strip_tags should remove script and img

        $response = $this->actingAs($this->user)->postJson('/notes', [
            'content' => $dirtyContent
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('notes', [
            'content' => $expectedContent
        ]);
    }

    public function test_user_can_update_note_content_and_status()
    {
        $note = Note::create([
            'id_user' => $this->user->id,
            'content' => 'Old content',
            'is_checked' => false
        ]);

        $response = $this->actingAs($this->user)->putJson("/notes/{$note->id}", [
            'content' => 'New <strong>bold</strong> content',
            'is_checked' => true
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
            'content' => 'New <strong>bold</strong> content',
            'is_checked' => true
        ]);
    }

    public function test_user_cannot_access_others_notes()
    {
        $otherUser = User::factory()->create();
        $note = Note::create([
            'id_user' => $otherUser->id,
            'content' => 'Secret note'
        ]);

        $response = $this->actingAs($this->user)->putJson("/notes/{$note->id}", [
            'content' => 'Hacked content'
        ]);

        $response->assertStatus(403);
    }
}

<?php

namespace Tests\Feature\Universal;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UniversalMessagingControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $user;
    protected $recipient;
    protected $conversation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'sender@test.com',
        ]);

        $this->recipient = User::factory()->create([
            'email' => 'recipient@test.com',
        ]);

        $this->conversation = Conversation::factory()->create([
            'user_id' => $this->user->id,
            'recipient_id' => $this->recipient->id,
        ]);

        $this->actingAs($this->user);
    }

    /** @test */
    public function test_can_view_messaging_dashboard()
    {
        $response = $this->get('/messaging');

        $response->assertStatus(200)
            ->assertViewIs('messaging.index')
            ->assertViewHas(['conversations', 'unreadCount']);
    }

    /** @test */
    public function test_can_get_conversations_api()
    {
        // Create additional conversations
        Conversation::factory(5)->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/messaging/conversations');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'subject',
                        'participant',
                        'last_message',
                        'unread_count',
                        'created_at',
                    ],
                ],
                'pagination',
            ]);
    }

    /** @test */
    public function test_can_start_new_conversation()
    {
        $newRecipient = User::factory()->create();

        $conversationData = [
            'recipient_id' => $newRecipient->id,
            'subject' => 'Test Conversation',
            'message' => 'This is a test message',
        ];

        $response = $this->postJson('/api/messaging/conversations', $conversationData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'subject',
                    'participants',
                    'created_at',
                ],
            ]);

        $this->assertDatabaseHas('conversations', [
            'user_id' => $this->user->id,
            'recipient_id' => $newRecipient->id,
            'subject' => 'Test Conversation',
        ]);

        $this->assertDatabaseHas('messages', [
            'sender_id' => $this->user->id,
            'content' => 'This is a test message',
        ]);
    }

    /** @test */
    public function test_can_get_conversation_messages()
    {
        // Create messages in the conversation
        Message::factory(10)->create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->user->id,
        ]);

        Message::factory(5)->create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->recipient->id,
        ]);

        $response = $this->getJson("/api/messaging/conversations/{$this->conversation->id}/messages");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'content',
                        'sender',
                        'attachments',
                        'read_at',
                        'created_at',
                    ],
                ],
                'pagination',
            ]);
    }

    /** @test */
    public function test_can_send_message()
    {
        $messageData = [
            'content' => 'This is a test message',
            'message_type' => 'text',
        ];

        $response = $this->postJson("/api/messaging/conversations/{$this->conversation->id}/messages", $messageData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'content',
                    'sender',
                    'created_at',
                ],
            ]);

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->user->id,
            'content' => 'This is a test message',
        ]);
    }

    /** @test */
    public function test_can_send_message_with_attachment()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('test.jpg');

        $messageData = [
            'content' => 'Message with attachment',
            'message_type' => 'file',
            'attachment' => $file,
        ];

        $response = $this->postJson("/api/messaging/conversations/{$this->conversation->id}/messages", $messageData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'content',
                    'attachments',
                    'sender',
                ],
            ]);

        Storage::disk('public')->assertExists('messages/'.$file->hashName());

        $this->assertDatabaseHas('message_attachments', [
            'original_name' => 'test.jpg',
            'mime_type' => 'image/jpeg',
        ]);
    }

    /** @test */
    public function test_can_mark_messages_as_read()
    {
        $messages = Message::factory(3)->create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->recipient->id,
            'read_at' => null,
        ]);

        $messageIds = $messages->pluck('id')->toArray();

        $response = $this->patchJson('/api/messaging/messages/mark-read', [
            'message_ids' => $messageIds,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Messages marked as read',
            ]);

        foreach ($messages as $message) {
            $this->assertNotNull($message->fresh()->read_at);
        }
    }

    /** @test */
    public function test_can_search_conversations()
    {
        // Create conversations with different subjects
        Conversation::factory()->create([
            'user_id' => $this->user->id,
            'subject' => 'Job Application Discussion',
        ]);

        Conversation::factory()->create([
            'user_id' => $this->user->id,
            'subject' => 'Interview Scheduling',
        ]);

        $response = $this->getJson('/api/messaging/conversations?search=Job');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');

        $response = $this->getJson('/api/messaging/conversations?search=Interview');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function test_can_filter_conversations_by_status()
    {
        // Create read and unread conversations
        $readConversation = Conversation::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'read',
        ]);

        $unreadConversation = Conversation::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'unread',
        ]);

        $response = $this->getJson('/api/messaging/conversations?status=unread');

        $response->assertStatus(200);

        $conversationIds = collect($response->json('data'))->pluck('id');
        $this->assertTrue($conversationIds->contains($unreadConversation->id));
        $this->assertFalse($conversationIds->contains($readConversation->id));
    }

    /** @test */
    public function test_can_delete_conversation()
    {
        $response = $this->deleteJson("/api/messaging/conversations/{$this->conversation->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Conversation deleted successfully',
            ]);

        $this->assertSoftDeleted('conversations', [
            'id' => $this->conversation->id,
        ]);
    }

    /** @test */
    public function test_can_archive_conversation()
    {
        $response = $this->patchJson("/api/messaging/conversations/{$this->conversation->id}/archive");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Conversation archived successfully',
            ]);

        $this->assertDatabaseHas('conversations', [
            'id' => $this->conversation->id,
            'archived_at' => now(),
        ]);
    }

    /** @test */
    public function test_can_get_message_statistics()
    {
        // Create various messages
        Message::factory(10)->create(['sender_id' => $this->user->id]);
        Message::factory(5)->create(['sender_id' => $this->user->id, 'created_at' => now()->subDays(7)]);

        $response = $this->getJson('/api/messaging/statistics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'total_conversations',
                    'unread_count',
                    'messages_sent_today',
                    'messages_received_today',
                    'average_response_time',
                    'conversation_trends',
                ],
            ]);
    }

    /** @test */
    public function test_cannot_access_unauthorized_conversation()
    {
        $otherUser = User::factory()->create();
        $otherConversation = Conversation::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->getJson("/api/messaging/conversations/{$otherConversation->id}/messages");

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized access to conversation',
            ]);
    }

    /** @test */
    public function test_validates_required_fields_for_new_conversation()
    {
        $response = $this->postJson('/api/messaging/conversations', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['recipient_id', 'subject', 'message']);
    }

    /** @test */
    public function test_validates_message_content()
    {
        $response = $this->postJson("/api/messaging/conversations/{$this->conversation->id}/messages", [
            'content' => '', // Empty content
            'message_type' => 'text',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['content']);
    }

    /** @test */
    public function test_validates_file_upload_size()
    {
        Storage::fake('public');

        // Create a file that's too large (assume 10MB limit)
        $largeFile = UploadedFile::fake()->create('large.pdf', 11000); // 11MB

        $response = $this->postJson("/api/messaging/conversations/{$this->conversation->id}/messages", [
            'content' => 'Large file',
            'message_type' => 'file',
            'attachment' => $largeFile,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['attachment']);
    }

    /** @test */
    public function test_can_get_conversation_participants()
    {
        $response = $this->getJson("/api/messaging/conversations/{$this->conversation->id}/participants");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'avatar',
                        'last_seen_at',
                        'is_online',
                    ],
                ],
            ]);
    }

    /** @test */
    public function test_real_time_message_event_dispatched()
    {
        Event::fake();

        $messageData = [
            'content' => 'Real-time test message',
            'message_type' => 'text',
        ];

        $this->postJson("/api/messaging/conversations/{$this->conversation->id}/messages", $messageData);

        Event::assertDispatched(\App\Events\MessageSent::class);
    }

    /** @test */
    public function test_can_update_typing_status()
    {
        $response = $this->postJson("/api/messaging/conversations/{$this->conversation->id}/typing", [
            'is_typing' => true,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Typing status updated',
            ]);
    }

    /** @test */
    public function test_can_export_conversation()
    {
        // Create messages for export
        Message::factory(5)->create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->user->id,
        ]);

        $response = $this->getJson("/api/messaging/conversations/{$this->conversation->id}/export");

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJsonStructure([
                'success',
                'data' => [
                    'conversation_info',
                    'messages' => [
                        '*' => [
                            'content',
                            'sender',
                            'timestamp',
                            'attachments',
                        ],
                    ],
                ],
            ]);
    }

    /** @test */
    public function test_pagination_works_correctly()
    {
        // Create many messages
        Message::factory(50)->create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->user->id,
        ]);

        $response = $this->getJson("/api/messaging/conversations/{$this->conversation->id}/messages?per_page=10");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'pagination' => [
                    'current_page',
                    'total_pages',
                    'per_page',
                    'total_items',
                ],
            ]);

        $this->assertEquals(10, count($response->json('data')));
    }

    /** @test */
    public function test_message_attachment_download()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('document.pdf', 100);

        // Create message with attachment
        $message = Message::factory()->create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->user->id,
        ]);

        $attachment = $message->attachments()->create([
            'original_name' => 'document.pdf',
            'stored_name' => $file->hashName(),
            'path' => 'messages/'.$file->hashName(),
            'mime_type' => 'application/pdf',
            'size' => 100,
        ]);

        $response = $this->get("/api/messaging/attachments/{$attachment->id}/download");

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');
    }

    /** @test */
    public function test_conversation_auto_archive_after_inactivity()
    {
        // Mock a conversation that's been inactive for 30 days
        $oldConversation = Conversation::factory()->create([
            'user_id' => $this->user->id,
            'updated_at' => now()->subDays(31),
        ]);

        // Run the auto-archive command
        $this->artisan('messaging:auto-archive');

        $this->assertDatabaseHas('conversations', [
            'id' => $oldConversation->id,
            'archived_at' => now(),
        ]);
    }
}

<?php

namespace Tests\Unit;

use App\Models\Admin;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatFactoriesTest extends TestCase
{
  use RefreshDatabase;

  public function test_chat_session_factory_creates_session_with_user(): void
  {
    $session = ChatSession::factory()->create();

    $this->assertDatabaseHas('chat_sessions', [
      'id' => $session->id,
      'user_id' => $session->user_id,
      'status' => 'open',
    ]);

    $this->assertInstanceOf(User::class, $session->user);
  }

  public function test_chat_session_factory_can_create_closed_session(): void
  {
    $session = ChatSession::factory()->closed()->create();

    $this->assertEquals('closed', $session->status);
  }

  public function test_chat_session_factory_can_create_session_without_messages(): void
  {
    $session = ChatSession::factory()->withoutMessages()->create();

    $this->assertNull($session->last_message_at);
  }

  public function test_chat_message_factory_creates_user_message(): void
  {
    $user = User::factory()->create();
    $session = ChatSession::factory()->create(['user_id' => $user->id]);

    $message = ChatMessage::factory()
      ->fromUser($user)
      ->create(['chat_session_id' => $session->id]);

    $this->assertDatabaseHas('chat_messages', [
      'id' => $message->id,
      'chat_session_id' => $session->id,
      'sender_type' => 'user',
      'sender_id' => $user->id,
    ]);

    $this->assertTrue($message->isFromUser());
    $this->assertFalse($message->isFromAdmin());
  }

  public function test_chat_message_factory_creates_admin_message(): void
  {
    $admin = Admin::factory()->create();
    $session = ChatSession::factory()->create();

    $message = ChatMessage::factory()
      ->fromAdmin($admin)
      ->create(['chat_session_id' => $session->id]);

    $this->assertDatabaseHas('chat_messages', [
      'id' => $message->id,
      'chat_session_id' => $session->id,
      'sender_type' => 'admin',
      'sender_id' => $admin->id,
    ]);

    $this->assertTrue($message->isFromAdmin());
    $this->assertFalse($message->isFromUser());
  }

  public function test_chat_message_factory_can_create_read_message(): void
  {
    $message = ChatMessage::factory()->read()->create();

    $this->assertNotNull($message->read_at);
  }

  public function test_chat_message_factory_can_create_unread_message(): void
  {
    $message = ChatMessage::factory()->unread()->create();

    $this->assertNull($message->read_at);
  }

  public function test_chat_message_factory_can_create_custom_message(): void
  {
    $customMessage = 'This is a custom test message';
    $message = ChatMessage::factory()->withMessage($customMessage)->create();

    $this->assertEquals($customMessage, $message->message);
  }

  public function test_chat_session_has_messages_relationship(): void
  {
    $session = ChatSession::factory()->create();
    $user = $session->user;

    ChatMessage::factory()
      ->count(3)
      ->fromUser($user)
      ->create(['chat_session_id' => $session->id]);

    $this->assertCount(3, $session->messages);
  }

  public function test_chat_message_belongs_to_session(): void
  {
    $session = ChatSession::factory()->create();
    $message = ChatMessage::factory()->create(['chat_session_id' => $session->id]);

    $this->assertEquals($session->id, $message->chatSession->id);
  }
}

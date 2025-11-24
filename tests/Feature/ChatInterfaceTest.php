<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ChatSession;
use App\Models\ChatMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatInterfaceTest extends TestCase
{
  use RefreshDatabase;

  /**
   * Test that authenticated users can access the chat interface.
   */
  public function test_authenticated_user_can_access_chat_interface(): void
  {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('chat.index'));

    $response->assertStatus(200);
    $response->assertViewIs('chat.index');
    $response->assertViewHas('session');
    $response->assertViewHas('messages');
  }

  /**
   * Test that unauthenticated users cannot access the chat interface.
   */
  public function test_unauthenticated_user_cannot_access_chat_interface(): void
  {
    $response = $this->get(route('chat.index'));

    $response->assertRedirect(route('login'));
  }

  /**
   * Test that the chat interface displays the empty state when no messages exist.
   */
  public function test_chat_interface_displays_empty_state_when_no_messages(): void
  {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('chat.index'));

    $response->assertStatus(200);
    $response->assertSee('Start a conversation');
    $response->assertSee('Send us a message');
  }

  /**
   * Test that the chat interface displays existing messages.
   */
  public function test_chat_interface_displays_existing_messages(): void
  {
    $user = User::factory()->create();

    // Create a chat session with messages
    $session = ChatSession::create([
      'user_id' => $user->id,
      'status' => 'open',
    ]);

    $userMessage = ChatMessage::create([
      'chat_session_id' => $session->id,
      'sender_type' => 'user',
      'sender_id' => $user->id,
      'message' => 'Hello, I need help with my item.',
    ]);

    $response = $this->actingAs($user)->get(route('chat.index'));

    $response->assertStatus(200);
    $response->assertSee('Hello, I need help with my item.');
    // The empty state should not be visible in the messages container when messages exist
    $response->assertSee('data-message-timestamp', false);
  }

  /**
   * Test that user and admin messages are styled differently.
   */
  public function test_user_and_admin_messages_have_different_styles(): void
  {
    $user = User::factory()->create();

    $session = ChatSession::create([
      'user_id' => $user->id,
      'status' => 'open',
    ]);

    ChatMessage::create([
      'chat_session_id' => $session->id,
      'sender_type' => 'user',
      'sender_id' => $user->id,
      'message' => 'User message',
    ]);

    ChatMessage::create([
      'chat_session_id' => $session->id,
      'sender_type' => 'admin',
      'sender_id' => 1,
      'message' => 'Admin response',
    ]);

    $response = $this->actingAs($user)->get(route('chat.index'));

    $response->assertStatus(200);
    // User messages should have green background
    $response->assertSee('bg-sacli-green-600', false);
    // Admin messages should have Support label
    $response->assertSee('Support');
    $response->assertSee('User message');
    $response->assertSee('Admin response');
  }

  /**
   * Test that the message input form is present.
   */
  public function test_message_input_form_is_present(): void
  {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('chat.index'));

    $response->assertStatus(200);
    $response->assertSee('message-input', false);
    $response->assertSee('Type your message...');
    $response->assertSee('Send');
  }
}

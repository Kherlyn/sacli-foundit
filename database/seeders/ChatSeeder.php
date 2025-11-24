<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\User;
use Illuminate\Database\Seeder;

class ChatSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Get or create some users and admins
    $users = User::factory()->count(5)->create();
    $admins = Admin::factory()->count(2)->create();

    // Create chat sessions with messages for each user
    foreach ($users as $index => $user) {
      // Create a chat session for this user
      $session = ChatSession::factory()->create([
        'user_id' => $user->id,
        'status' => $index < 3 ? 'open' : 'closed', // First 3 open, rest closed
      ]);

      // Create a conversation with multiple messages
      $messageCount = rand(3, 10);
      $lastMessageTime = now()->subDays(rand(1, 7));

      for ($i = 0; $i < $messageCount; $i++) {
        // Alternate between user and admin messages
        $isUserMessage = $i % 2 === 0;
        $messageTime = $lastMessageTime->copy()->addMinutes($i * rand(5, 30));

        if ($isUserMessage) {
          ChatMessage::factory()
            ->fromUser($user)
            ->create([
              'chat_session_id' => $session->id,
              'message' => $this->getUserMessage($i),
              'created_at' => $messageTime,
              'read_at' => $i < $messageCount - 2 ? $messageTime->copy()->addMinutes(rand(1, 10)) : null,
            ]);
        } else {
          ChatMessage::factory()
            ->fromAdmin($admins->random())
            ->create([
              'chat_session_id' => $session->id,
              'message' => $this->getAdminMessage($i),
              'created_at' => $messageTime,
              'read_at' => $i < $messageCount - 1 ? $messageTime->copy()->addMinutes(rand(1, 10)) : null,
            ]);
        }
      }

      // Update session's last_message_at
      $session->update([
        'last_message_at' => $session->messages()->latest()->first()->created_at,
      ]);
    }

    $this->command->info('Chat sessions and messages seeded successfully!');
  }

  /**
   * Get a sample user message based on the message index.
   */
  private function getUserMessage(int $index): string
  {
    $messages = [
      "Hi, I need help with something.",
      "I found an item but I'm not sure how to report it.",
      "Can you help me verify my lost item?",
      "I haven't received any updates on my submission.",
      "How long does the verification process usually take?",
      "Thank you for your help!",
      "I have another question about the process.",
      "Is there a way to edit my submission?",
      "I think I made a mistake in my item description.",
      "Can you check the status of my item?",
    ];

    return $messages[$index % count($messages)];
  }

  /**
   * Get a sample admin message based on the message index.
   */
  private function getAdminMessage(int $index): string
  {
    $messages = [
      "Hello! I'm here to help. What do you need assistance with?",
      "To report a found item, click on 'Submit Item' in the navigation menu.",
      "I can help you with that. Let me check your submission.",
      "Your item is currently under review. It typically takes 24-48 hours.",
      "I've checked your submission and everything looks good.",
      "You're welcome! Is there anything else I can help you with?",
      "Sure, I'd be happy to answer your questions.",
      "Yes, you can edit your submission from the 'My Items' page.",
      "No problem, I can help you update that information.",
      "Let me look into that for you right away.",
    ];

    return $messages[$index % count($messages)];
  }
}

<?php

namespace App\Http\Controllers;

use App\Services\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ChatController extends Controller
{
  /**
   * Create a new ChatController instance.
   */
  public function __construct(
    private ChatService $chatService
  ) {
    // Middleware is applied in routes
  }

  /**
   * Show the chat interface for the authenticated user.
   *
   * @return View
   */
  public function index(): View
  {
    $user = Auth::user();
    $session = $this->chatService->getOrCreateUserSession($user);
    $messages = $this->chatService->getSessionMessages($session);

    // Mark admin messages as read when user views chat
    $this->chatService->markSessionAsReadBy($session, 'user');

    return view('chat.index', [
      'session' => $session,
      'messages' => $messages,
    ]);
  }

  /**
   * Send a message from the authenticated user.
   *
   * @param Request $request
   * @return JsonResponse
   */
  public function sendMessage(Request $request): JsonResponse
  {
    try {
      $request->validate([
        'message' => 'required|string|max:5000',
      ]);

      $user = Auth::user();
      $message = $this->chatService->sendUserMessage($user, $request->input('message'));

      return response()->json([
        'success' => true,
        'message' => [
          'id' => $message->id,
          'chat_session_id' => $message->chat_session_id,
          'sender_type' => $message->sender_type,
          'sender_id' => $message->sender_id,
          'message' => $message->message,
          'created_at' => $message->created_at->toIso8601String(),
          'read_at' => $message->read_at?->toIso8601String(),
        ],
      ], 201);
    } catch (ValidationException $e) {
      return response()->json([
        'success' => false,
        'errors' => $e->errors(),
      ], 422);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Failed to send message',
      ], 500);
    }
  }

  /**
   * Get messages for the authenticated user's chat session.
   * Supports polling by accepting a 'since' timestamp parameter.
   *
   * @param Request $request
   * @return JsonResponse
   */
  public function getMessages(Request $request): JsonResponse
  {
    try {
      $user = Auth::user();
      $session = $this->chatService->getOrCreateUserSession($user);

      // If 'since' parameter is provided, get only new messages
      if ($request->has('since')) {
        $since = $request->input('since');

        try {
          $sinceDate = \Carbon\Carbon::parse($since);
          $messages = app(\App\Repositories\ChatRepository::class)
            ->getMessagesSince($session->id, $sinceDate);
        } catch (\Exception $e) {
          return response()->json([
            'success' => false,
            'message' => 'Invalid timestamp parameter',
          ], 400);
        }
      } else {
        // Get all messages
        $messages = $this->chatService->getSessionMessages($session);
      }

      return response()->json([
        'success' => true,
        'messages' => $messages->map(function ($message) {
          return [
            'id' => $message->id,
            'chat_session_id' => $message->chat_session_id,
            'sender_type' => $message->sender_type,
            'sender_id' => $message->sender_id,
            'message' => $message->message,
            'created_at' => $message->created_at->toIso8601String(),
            'read_at' => $message->read_at?->toIso8601String(),
          ];
        }),
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Failed to retrieve messages',
      ], 500);
    }
  }

  /**
   * Get the unread message count for the authenticated user.
   *
   * @return JsonResponse
   */
  public function getUnreadCount(): JsonResponse
  {
    try {
      $user = Auth::user();
      $unreadCount = $this->chatService->getUnreadCountForUser($user);

      return response()->json([
        'success' => true,
        'unread_count' => $unreadCount,
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Failed to retrieve unread count',
      ], 500);
    }
  }
}

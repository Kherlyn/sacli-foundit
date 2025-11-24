<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use App\Services\ChatService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ChatController extends Controller
{
  /**
   * Create a new ChatController instance.
   */
  public function __construct(
    private ChatService $chatService
  ) {
    // Middleware is applied at the route level in routes/admin.php
  }

  /**
   * Display all chat sessions with unread counts.
   *
   * GET /admin/chat
   *
   * @return View
   */
  public function index(): View
  {
    $sessions = $this->chatService->getAllSessionsForAdmin();

    return view('admin.chat.index', [
      'sessions' => $sessions,
    ]);
  }

  /**
   * Display a specific chat session.
   *
   * GET /admin/chat/{session}
   *
   * @param ChatSession $session
   * @return View
   */
  public function show(ChatSession $session): View
  {
    // Get messages for the session
    $messages = $this->chatService->getSessionMessages($session);

    // Mark user messages as read when admin views them
    $this->chatService->markSessionAsReadBy($session, 'admin');

    return view('admin.chat.show', [
      'session' => $session->load('user'),
      'messages' => $messages,
    ]);
  }

  /**
   * Send a message to a user (admin response).
   *
   * POST /admin/chat/{session}/messages
   *
   * @param Request $request
   * @param ChatSession $session
   * @return JsonResponse
   */
  public function sendMessage(Request $request, ChatSession $session): JsonResponse
  {
    try {
      $request->validate([
        'message' => 'required|string|max:5000',
      ]);

      /** @var \App\Models\Admin $admin */
      $admin = auth()->guard('admin')->user();

      $message = $this->chatService->sendAdminMessage(
        $admin,
        $session,
        $request->input('message')
      );

      return response()->json([
        'success' => true,
        'message' => [
          'id' => $message->id,
          'message' => $message->message,
          'sender_type' => $message->sender_type,
          'sender_id' => $message->sender_id,
          'created_at' => $message->created_at->toIso8601String(),
        ],
      ], 201);
    } catch (\Illuminate\Validation\ValidationException $e) {
      return response()->json([
        'success' => false,
        'errors' => $e->errors(),
      ], 422);
    } catch (\Exception $e) {
      Log::error('Failed to send admin message', [
        'session_id' => $session->id,
        'error' => $e->getMessage(),
      ]);

      return response()->json([
        'success' => false,
        'message' => 'Failed to send message. Please try again.',
      ], 500);
    }
  }

  /**
   * Get messages for a session (AJAX polling).
   *
   * GET /admin/chat/{session}/messages
   *
   * @param Request $request
   * @param ChatSession $session
   * @return JsonResponse
   */
  public function getMessages(Request $request, ChatSession $session): JsonResponse
  {
    try {
      $since = $request->query('since');

      if ($since) {
        // Get messages since the provided timestamp
        $sinceDate = Carbon::parse($since);
        $messages = $this->chatService->getSessionMessages($session)
          ->filter(function ($message) use ($sinceDate) {
            return $message->created_at->isAfter($sinceDate);
          });

        // Mark user messages as read when admin is viewing them
        if ($messages->isNotEmpty()) {
          $this->chatService->markSessionAsReadBy($session, 'admin');
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
            'message' => $message->message,
            'sender_type' => $message->sender_type,
            'sender_id' => $message->sender_id,
            'created_at' => $message->created_at->toIso8601String(),
            'read_at' => $message->read_at?->toIso8601String(),
          ];
        })->values(),
      ]);
    } catch (\Exception $e) {
      Log::error('Failed to get messages', [
        'session_id' => $session->id,
        'error' => $e->getMessage(),
      ]);

      return response()->json([
        'success' => false,
        'message' => 'Failed to retrieve messages. Please try again.',
      ], 500);
    }
  }
}

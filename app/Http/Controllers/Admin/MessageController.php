<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Services\MessageService;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    protected $messageService;
    
    /**
     * Create a new controller instance.
     *
     * @param MessageService $messageService
     * @return void
     */
    public function __construct(MessageService $messageService)
    {
        $this->middleware('auth');
        $this->messageService = $messageService;
    }
    
    /**
     * Display a listing of the messages.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $messages = $this->messageService->getAllMessages($perPage);
        
        return response()->json([
            'success' => true,
            'data' => $messages,
            'unread_count' => $this->messageService->getUnreadCount()
        ]);
    }

    /**
     * Store a newly created message in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string',
        ]);
        
        $message = $this->messageService->createMessage($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Message created successfully',
            'data' => $message
        ]);
    }

    /**
     * Display the specified message.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $message = $this->messageService->getMessage($id);
        
        // If the message wasn't read yet, mark it as read
        if (!$message->read) {
            $this->messageService->markAsRead($id);
        }
        
        return response()->json([
            'success' => true,
            'data' => $message
        ]);
    }

    /**
     * Update the specified message in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string',
            'read' => 'boolean'
        ]);
        
        $message = $this->messageService->updateMessage($id, $validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Message updated successfully',
            'data' => $message
        ]);
    }

    /**
     * Remove the specified message from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $this->messageService->deleteMessage($id);
        
        return response()->json([
            'success' => true,
            'message' => 'Message deleted successfully'
        ]);
    }
    
    /**
     * Mark a message as read
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead($id)
    {
        $message = $this->messageService->markAsRead($id);
        
        return response()->json([
            'success' => true,
            'message' => 'Message marked as read',
            'data' => $message
        ]);
    }
    
    /**
     * Mark a message as unread
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsUnread($id)
    {
        $message = $this->messageService->markAsUnread($id);
        
        return response()->json([
            'success' => true,
            'message' => 'Message marked as unread',
            'data' => $message
        ]);
    }
}

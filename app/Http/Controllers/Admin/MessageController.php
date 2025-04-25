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
        // Temporarily remove or comment out auth middleware for testing
        // $this->middleware('auth');
        $this->messageService = $messageService;
    }
    
    /**
     * Display a listing of the messages.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $search = $request->input('search');
            
            // Using messageService with search parameter if provided
            if ($search) {
                $messages = $this->messageService->searchMessages($search, false, false, $perPage);
            } else {
                $messages = $this->messageService->getAllMessages($perPage);
            }
            
            return response()->json([
                'success' => true,
                'data' => $messages,
                'unread_count' => $this->messageService->getUnreadCount()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching messages: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching messages',
                'error' => $e->getMessage()
            ], 500);
        }
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

    /**
     * Display a listing of the archived messages.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function archived(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $search = $request->input('search');
            
            // Using messageService with search parameter if provided
            if ($search) {
                $messages = $this->messageService->searchMessages($search, true, false, $perPage);
            } else {
                $messages = $this->messageService->getArchivedMessages($perPage);
            }
            
            return response()->json([
                'success' => true,
                'data' => $messages,
                'unread_count' => $this->messageService->getUnreadCount()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching archived messages: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching archived messages',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a listing of sent messages.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sent(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $search = $request->input('search');
            
            // Using messageService with search parameter if provided
            if ($search) {
                $messages = $this->messageService->searchMessages($search, false, true, $perPage);
            } else {
                $messages = $this->messageService->getSentMessages($perPage);
            }
            
            return response()->json([
                'success' => true,
                'data' => $messages
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching sent messages: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching sent messages',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Archive a message
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function archive($id)
    {
        try {
            $message = $this->messageService->archiveMessage($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Message archived successfully',
                'data' => $message
            ]);
        } catch (\Exception $e) {
            \Log::error('Error archiving message: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error archiving message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unarchive a message
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function unarchive($id)
    {
        try {
            $message = $this->messageService->unarchiveMessage($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Message unarchived successfully',
                'data' => $message
            ]);
        } catch (\Exception $e) {
            \Log::error('Error unarchiving message: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error unarchiving message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send a message from admin to user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string',
        ]);
        
        try {
            // Create a sent message
            $message = $this->messageService->createSentMessage($validated);
            
            // Optionally, send an actual email here
            // $this->sendEmail($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully',
                'data' => $message
            ]);
        } catch (\Exception $e) {
            \Log::error('Error sending message: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error sending message',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

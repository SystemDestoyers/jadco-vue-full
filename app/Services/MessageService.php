<?php

namespace App\Services;

use App\Models\Message;
use Illuminate\Pagination\LengthAwarePaginator;

class MessageService
{
    /**
     * Get all messages with pagination (excluding archived ones)
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllMessages(int $perPage = 10): LengthAwarePaginator
    {
        return Message::where('archived', false)->latest()->paginate($perPage);
    }
    
    /**
     * Get all archived messages with pagination
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getArchivedMessages(int $perPage = 10): LengthAwarePaginator
    {
        return Message::where('archived', true)->latest()->paginate($perPage);
    }
    
    /**
     * Get all sent messages with pagination
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getSentMessages(int $perPage = 10): LengthAwarePaginator
    {
        return Message::where('sent', true)->latest()->paginate($perPage);
    }
    
    /**
     * Get a message by ID
     *
     * @param int $id
     * @return Message
     */
    public function getMessage(int $id): Message
    {
        return Message::findOrFail($id);
    }
    
    /**
     * Create a new message
     *
     * @param array $data
     * @return Message
     */
    public function createMessage(array $data): Message
    {
        return Message::create($data);
    }
    
    /**
     * Update a message
     *
     * @param int $id
     * @param array $data
     * @return Message
     */
    public function updateMessage(int $id, array $data): Message
    {
        $message = $this->getMessage($id);
        $message->update($data);
        return $message;
    }
    
    /**
     * Delete a message
     *
     * @param int $id
     * @return bool
     */
    public function deleteMessage(int $id): bool
    {
        return $this->getMessage($id)->delete();
    }
    
    /**
     * Mark a message as read
     *
     * @param int $id
     * @return Message
     */
    public function markAsRead(int $id): Message
    {
        return $this->updateMessage($id, ['read' => true]);
    }
    
    /**
     * Mark a message as unread
     *
     * @param int $id
     * @return Message
     */
    public function markAsUnread(int $id): Message
    {
        return $this->updateMessage($id, ['read' => false]);
    }
    
    /**
     * Get unread message count
     *
     * @return int
     */
    public function getUnreadCount(): int
    {
        return Message::where('read', false)->count();
    }
    
    /**
     * Mark a message as archived
     *
     * @param int $id
     * @return Message
     */
    public function archiveMessage(int $id): Message
    {
        return $this->updateMessage($id, ['archived' => true]);
    }
    
    /**
     * Unarchive a message
     *
     * @param int $id
     * @return Message
     */
    public function unarchiveMessage(int $id): Message
    {
        return $this->updateMessage($id, ['archived' => false]);
    }
    
    /**
     * Create a sent message
     *
     * @param array $data
     * @return Message
     */
    public function createSentMessage(array $data): Message
    {
        $data['sent'] = true;
        return $this->createMessage($data);
    }
    
    /**
     * Search messages by term with various filters
     *
     * @param string $searchTerm
     * @param bool $archived Whether to search in archived messages
     * @param bool $sent Whether to search in sent messages
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function searchMessages(string $searchTerm, bool $archived = false, bool $sent = false, int $perPage = 10)
    {
        $query = Message::query();
        
        // Add filters for archived/sent status
        if ($archived) {
            $query->where('archived', true);
        } else if ($sent) {
            $query->where('sent', true);
        } else {
            $query->where('archived', false);
        }
        
        // Add search conditions
        $query->where(function($q) use ($searchTerm) {
            $q->where('first_name', 'like', "%{$searchTerm}%")
              ->orWhere('last_name', 'like', "%{$searchTerm}%")
              ->orWhere('email', 'like', "%{$searchTerm}%")
              ->orWhere('message', 'like', "%{$searchTerm}%")
              ->orWhere('phone', 'like', "%{$searchTerm}%");
        });
        
        return $query->latest()->paginate($perPage);
    }
} 
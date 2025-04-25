<?php

namespace App\Services;

use App\Models\Message;
use Illuminate\Pagination\LengthAwarePaginator;

class MessageService
{
    /**
     * Get all messages with pagination
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllMessages(int $perPage = 10): LengthAwarePaginator
    {
        return Message::latest()->paginate($perPage);
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
} 
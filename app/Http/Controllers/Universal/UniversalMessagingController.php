    /**
     * Display messaging interface
     */
    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            
            // Get conversations with latest message and participant info
            $conversations = DB::table('conversations')
                ->join('conversation_participants', 'conversations.id', '=', 'conversation_participants.conversation_id')
                ->join('users', 'conversation_participants.user_id', '=', 'users.id')
                ->leftJoin('messages', function($join) {
                    $join->on('conversations.id', '=', 'messages.conversation_id')
                         ->whereRaw('messages.id = (SELECT MAX(id) FROM messages WHERE conversation_id = conversations.id)');
                })
                ->where('conversation_participants.user_id', '!=', $user->id)
                ->where('conversations.id', 'IN', function($query) use ($user) {
                    $query->select('conversation_id')
                          ->from('conversation_participants')
                          ->where('user_id', $user->id);
                })
                ->select([
                    'conversations.id as conversation_id',
                    'conversations.subject',
                    'conversations.updated_at as last_activity',
                    'users.id as participant_id',
                    'users.name as participant_name',
                    'users.email as participant_email',
                    'users.avatar as participant_avatar',
                    'messages.content as last_message',
                    'messages.created_at as last_message_time',
                    'messages.user_id as last_message_sender',
                    DB::raw('(SELECT COUNT(*) FROM messages WHERE conversation_id = conversations.id AND user_id != ' . $user->id . ' AND read_at IS NULL) as unread_count')
                ])
                ->orderBy('conversations.updated_at', 'desc')
                ->get();
            
            // Get active conversation if specified
            $activeConversation = null;
            $messages = collect();
            
            if ($request->has('conversation')) {
                $conversationId = $request->get('conversation');
                $activeConversation = $conversations->firstWhere('conversation_id', $conversationId);
                
                if ($activeConversation) {
                    // Get messages for this conversation
                    $messages = DB::table('messages')
                        ->join('users', 'messages.user_id', '=', 'users.id')
                        ->where('messages.conversation_id', $conversationId)
                        ->select([
                            'messages.*',
                            'users.name as sender_name',
                            'users.avatar as sender_avatar'
                        ])
                        ->orderBy('messages.created_at', 'asc')
                        ->get();
                    
                    // Mark messages as read
                    DB::table('messages')
                        ->where('conversation_id', $conversationId)
                        ->where('user_id', '!=', $user->id)
                        ->whereNull('read_at')
                        ->update(['read_at' => now()]);
                }
            }
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'conversations' => $conversations,
                        'active_conversation' => $activeConversation,
                        'messages' => $messages
                    ]
                ]);
            }
            
            return view('messaging.index', compact('conversations', 'activeConversation', 'messages'));
            
        } catch (\Exception $e) {
            Log::error('Messaging index error', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Unable to load messages')
                ], 500);
            }
            
            return redirect()->back()->with('error', __('Unable to load messages'));
        }
    }

    /**
     * Start new conversation
     */
    public function startConversation(Request $request)
    {
        try {
            $validated = $request->validate([
                'recipient_id' => 'required|exists:users,id|different:auth_user_id',
                'subject' => 'required|string|max:255',
                'message' => 'required|string|max:5000'
            ]);
            
            $user = auth()->user();
            
            // Check if conversation already exists between these users
            $existingConversation = DB::table('conversations')
                ->join('conversation_participants as cp1', 'conversations.id', '=', 'cp1.conversation_id')
                ->join('conversation_participants as cp2', 'conversations.id', '=', 'cp2.conversation_id')
                ->where('cp1.user_id', $user->id)
                ->where('cp2.user_id', $validated['recipient_id'])
                ->where('cp1.user_id', '!=', 'cp2.user_id')
                ->select('conversations.id')
                ->first();
            
            if ($existingConversation) {
                // Use existing conversation
                $conversationId = $existingConversation->id;
            } else {
                // Create new conversation
                $conversationId = DB::table('conversations')->insertGetId([
                    'subject' => $validated['subject'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                // Add participants
                DB::table('conversation_participants')->insert([
                    [
                        'conversation_id' => $conversationId,
                        'user_id' => $user->id,
                        'joined_at' => now()
                    ],
                    [
                        'conversation_id' => $conversationId,
                        'user_id' => $validated['recipient_id'],
                        'joined_at' => now()
                    ]
                ]);
            }
            
            // Send initial message
            $messageId = DB::table('messages')->insertGetId([
                'conversation_id' => $conversationId,
                'user_id' => $user->id,
                'content' => $validated['message'],
                'type' => 'text',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Update conversation timestamp
            DB::table('conversations')
                ->where('id', $conversationId)
                ->update(['updated_at' => now()]);
            
            // Send notification to recipient
            $recipient = User::findOrFail($validated['recipient_id']);
            $recipient->notify(new \App\Notifications\NewMessageNotification([
                'sender_name' => $user->name,
                'subject' => $validated['subject'],
                'conversation_id' => $conversationId
            ]));
            
            Log::info('New conversation started', [
                'conversation_id' => $conversationId,
                'sender_id' => $user->id,
                'recipient_id' => $validated['recipient_id']
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Message sent successfully'),
                    'data' => [
                        'conversation_id' => $conversationId,
                        'message_id' => $messageId
                    ]
                ]);
            }
            
            return redirect()->route('messaging.index', ['conversation' => $conversationId])
                          ->with('success', __('Message sent successfully'));
            
        } catch (\Exception $e) {
            Log::error('Start conversation error', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'data' => $request->all()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Unable to send message')
                ], 500);
            }
            
            return redirect()->back()->with('error', __('Unable to send message'));
        }
    }

    /**
     * Send message to existing conversation
     */
    public function sendMessage(Request $request, $conversationId)
    {
        try {
            $validated = $request->validate([
                'message' => 'required|string|max:5000',
                'type' => 'in:text,file,image',
                'attachment' => 'nullable|file|max:10240' // 10MB max
            ]);
            
            $user = auth()->user();
            
            // Verify user is participant in conversation
            $isParticipant = DB::table('conversation_participants')
                ->where('conversation_id', $conversationId)
                ->where('user_id', $user->id)
                ->exists();
            
            if (!$isParticipant) {
                throw new \Exception('Unauthorized access to conversation');
            }
            
            $messageData = [
                'conversation_id' => $conversationId,
                'user_id' => $user->id,
                'content' => $validated['message'],
                'type' => $validated['type'] ?? 'text',
                'created_at' => now(),
                'updated_at' => now()
            ];
            
            // Handle file attachment
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('messages/attachments', $filename, 'public');
                
                $messageData['attachment_path'] = $path;
                $messageData['attachment_name'] = $file->getClientOriginalName();
                $messageData['attachment_size'] = $file->getSize();
                $messageData['attachment_type'] = $file->getMimeType();
            }
            
            // Insert message
            $messageId = DB::table('messages')->insertGetId($messageData);
            
            // Update conversation timestamp
            DB::table('conversations')
                ->where('id', $conversationId)
                ->update(['updated_at' => now()]);
            
            // Get other participants for notifications
            $otherParticipants = DB::table('conversation_participants')
                ->join('users', 'conversation_participants.user_id', '=', 'users.id')
                ->where('conversation_participants.conversation_id', $conversationId)
                ->where('conversation_participants.user_id', '!=', $user->id)
                ->select('users.*')
                ->get();
            
            // Send notifications
            foreach ($otherParticipants as $participant) {
                User::find($participant->id)->notify(new \App\Notifications\NewMessageNotification([
                    'sender_name' => $user->name,
                    'message_preview' => Str::limit($validated['message'], 100),
                    'conversation_id' => $conversationId
                ]));
            }
            
            Log::info('Message sent', [
                'message_id' => $messageId,
                'conversation_id' => $conversationId,
                'sender_id' => $user->id,
                'has_attachment' => $request->hasFile('attachment')
            ]);
            
            // Get the created message with sender info for response
            $message = DB::table('messages')
                ->join('users', 'messages.user_id', '=', 'users.id')
                ->where('messages.id', $messageId)
                ->select([
                    'messages.*',
                    'users.name as sender_name',
                    'users.avatar as sender_avatar'
                ])
                ->first();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Message sent successfully'),
                    'data' => [
                        'message' => $message
                    ]
                ]);
            }
            
            return redirect()->back()->with('success', __('Message sent successfully'));
            
        } catch (\Exception $e) {
            Log::error('Send message error', [
                'error' => $e->getMessage(),
                'conversation_id' => $conversationId,
                'user_id' => auth()->id(),
                'data' => $request->all()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Unable to send message')
                ], 500);
            }
            
            return redirect()->back()->with('error', __('Unable to send message'));
        }
    }

    /**
     * Mark conversation as read
     */
    public function markAsRead(Request $request, $conversationId)
    {
        try {
            $user = auth()->user();
            
            // Mark all unread messages in conversation as read
            $updatedCount = DB::table('messages')
                ->where('conversation_id', $conversationId)
                ->where('user_id', '!=', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
            
            Log::info('Conversation marked as read', [
                'conversation_id' => $conversationId,
                'user_id' => $user->id,
                'messages_marked' => $updatedCount
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Messages marked as read'),
                    'marked_count' => $updatedCount
                ]);
            }
            
            return redirect()->back()->with('success', __('Messages marked as read'));
            
        } catch (\Exception $e) {
            Log::error('Mark conversation as read error', [
                'error' => $e->getMessage(),
                'conversation_id' => $conversationId,
                'user_id' => auth()->id()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Unable to mark messages as read')
                ], 500);
            }
            
            return redirect()->back()->with('error', __('Unable to mark messages as read'));
        }
    }

    /**
     * Search conversations and messages
     */
    public function search(Request $request)
    {
        try {
            $validated = $request->validate([
                'query' => 'required|string|min:2|max:100'
            ]);
            
            $user = auth()->user();
            $searchTerm = '%' . $validated['query'] . '%';
            
            // Search in conversations (subject) and messages (content)
            $results = DB::table('conversations')
                ->join('conversation_participants', 'conversations.id', '=', 'conversation_participants.conversation_id')
                ->leftJoin('messages', 'conversations.id', '=', 'messages.conversation_id')
                ->leftJoin('users', 'conversation_participants.user_id', '=', 'users.id')
                ->where('conversation_participants.user_id', $user->id)
                ->where(function($query) use ($searchTerm) {
                    $query->where('conversations.subject', 'like', $searchTerm)
                          ->orWhere('messages.content', 'like', $searchTerm);
                })
                ->select([
                    'conversations.id as conversation_id',
                    'conversations.subject',
                    'messages.content as message_content',
                    'messages.created_at as message_time',
                    'users.name as participant_name'
                ])
                ->orderBy('messages.created_at', 'desc')
                ->limit(50)
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'results' => $results,
                    'query' => $validated['query'],
                    'count' => $results->count()
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Message search error', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'query' => $request->get('query')
            ]);
            
            return response()->json([
                'success' => false,
                'message' => __('Search failed')
            ], 500);
        }
    }

    /**
     * Delete message
     */
    public function deleteMessage(Request $request, $messageId)
    {
        try {
            $user = auth()->user();
            
            // Verify user owns the message
            $message = DB::table('messages')
                ->where('id', $messageId)
                ->where('user_id', $user->id)
                ->first();
            
            if (!$message) {
                throw new \Exception('Message not found or unauthorized');
            }
            
            // Delete attachment file if exists
            if ($message->attachment_path) {
                Storage::disk('public')->delete($message->attachment_path);
            }
            
            // Delete message
            DB::table('messages')->where('id', $messageId)->delete();
            
            Log::info('Message deleted', [
                'message_id' => $messageId,
                'user_id' => $user->id,
                'conversation_id' => $message->conversation_id
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Message deleted successfully')
                ]);
            }
            
            return redirect()->back()->with('success', __('Message deleted successfully'));
            
        } catch (\Exception $e) {
            Log::error('Delete message error', [
                'error' => $e->getMessage(),
                'message_id' => $messageId,
                'user_id' => auth()->id()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Unable to delete message')
                ], 500);
            }
            
            return redirect()->back()->with('error', __('Unable to delete message'));
        }
    }

    /**
     * Get unread messages count
     */
    public function getUnreadCount(Request $request)
    {
        try {
            $user = auth()->user();
            
            $count = DB::table('messages')
                ->join('conversation_participants', 'messages.conversation_id', '=', 'conversation_participants.conversation_id')
                ->where('conversation_participants.user_id', $user->id)
                ->where('messages.user_id', '!=', $user->id)
                ->whereNull('messages.read_at')
                ->count();
            
            return response()->json([
                'success' => true,
                'count' => $count
            ]);
            
        } catch (\Exception $e) {
            Log::error('Get unread messages count error', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'count' => 0
            ]);
        }
    }

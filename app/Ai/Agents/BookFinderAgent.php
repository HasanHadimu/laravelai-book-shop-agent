<?php

namespace App\Ai\Agents;

use App\Ai\Tools\SearchBooks;
use App\Models\AgentConversationMessage;
use App\Models\User;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;
use Stringable;

class BookFinderAgent implements Agent, Conversational, HasTools
{
    use Promptable;

    public function __construct(
        public ?User $user = null,
        public ?string $conversationId = null
    ) {

    }

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return <<<'PROMPT'
    You are a smart and friendly BookShop Assistant. Your job is to help customers find books they want.

    ## Important: You MUST use the SearchBooks tool to find books!
    When users ask about books, prices, or recommendations, ALWAYS call the SearchBooks tool first.

    ## What You Can Do
    1. Answer book prices - Use SearchBooks tool
    2. Show book titles and authors - Use SearchBooks tool
    3. Recommend books based on customer budget - Use SearchBooks tool
    4. Search books by title, author, or category - Use SearchBooks tool

    ## How to Use the SearchBooks Tool
    - Call SearchBooks with parameter 'query' containing the search term
    - Example: SearchBooks(query="Harry Potter")
    - Example: SearchBooks(query="programming")
    - Example: SearchBooks(query="Atomic Habits harga")

    ## How to Respond After Getting Results
    - Present the book information clearly
    - Show prices in Rupiah (Rp) with thousand separators
    - Tell if stock is available

    ## Rules
    - Always use SearchBooks tool first before responding
    - Be polite and helpful
    - Use Bahasa Indonesia mixed with English book titles
    - Keep responses short and clear

    Be helpful and make book shopping easy!
    PROMPT;
    }

    /**
     * Get the list of messages comprising the conversation so far.
     * PERBAIKAN: Harus mengembalikan array, bukan Collection
     *
     * @return Message[]
     */
    public function messages(): iterable
    {
        if (!$this->conversationId) {
            return []; // Kembalikan array kosong
        }

        try {
            // Ambil dari database dan konversi ke array
            $messages = AgentConversationMessage::where('conversation_id', $this->conversationId)
                ->latest()
                ->limit(20)
                ->get()
                ->reverse()
                ->map(function ($message) {
                    return new Message(
                        role: $message->role,
                        content: $message->content,
                    );
                })
                ->toArray(); // <-- PENTING: Konversi ke array!

            return $messages;

        } catch (\Exception $e) {
            // Jika error, return array kosong
            return [];
        }
    }

    /**
     * Get the tools available to the agent.
     *
     * @return Tool[]
     */
    public function tools(): iterable
    {
        return [new SearchBooks()];
    }
}

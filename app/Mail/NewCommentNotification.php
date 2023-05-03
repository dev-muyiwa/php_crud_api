<?php

namespace App\Mail;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class NewCommentNotification extends Mailable
{
    use Queueable, SerializesModels;


    /**
     * Create a new message instance.
     */
    public function __construct(
        protected Comment $comment
    )
    {
    }


    public function build(): NewCommentNotification
    {
        $commenterId = $this->comment->commenter_id;
        $commenter = User::findOrFail($commenterId); // or use Auth()::user()
        $html = "<p>New comment on your post by {$commenter->name}: {$this->comment->comment}</p>";
        return $this->subject("New comment")
            ->html($html);
    }
}

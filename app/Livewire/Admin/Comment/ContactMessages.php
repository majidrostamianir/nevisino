<?php

namespace App\Livewire\Admin\Comment;

use Livewire\Component;
use App\Models\ContactMessage;
use Livewire\WithPagination;

class ContactMessages extends Component
{
    use WithPagination;

    public function markAsRead($id)
    {
        $message = ContactMessage::find($id);
        $message->update(['is_read' => true]);
        session()->flash('success', 'پیام به عنوان خوانده شده علامت‌گذاری شد.');
    }

    public function delete($id)
    {
        ContactMessage::find($id)->delete();
        session()->flash('success', 'پیام با موفقیت حذف شد.');
    }

    public function render()
    {
        $messages = ContactMessage::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.comment.contact-messages', [
            'messages' => $messages
        ])->layout('components.layouts.admin');
    }
}
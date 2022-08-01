<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Facades\Larafirebase;
use Kutia\Larafirebase\Messages\FirebaseMessage;
use Carbon\Carbon;

class VesselAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    public $title;
    public $fcmTokens;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($title,$fcmTokens)
    {
        $this->title = $title;
        $this->fcmTokens = $fcmTokens;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['firebase','database','broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    //public function toMail($notifiable)
    //{
    //    return (new MailMessage)
    //                ->line('The introduction to the notification.')
    //                ->action('Notification Action', url('/'))
    //                ->line('Thank you for using our application!');
    //}


    public function toDatabase($notifiable)
    {
        return [
            'Title' => $this->title,
            'Body' => "You've been assigned to a new vessel!",

        ];
    }


    public function toFirebase($notifiable)
    {
        return (new FirebaseMessage)
            ->withTitle($this->title)
            ->withBody("You've been assigned to a new vessel!")
            ->withPriority('high')->asMessage($this->fcmTokens);
    }
    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }


    public function toBroadcast($notifiable)
    {
        $timestamp = Carbon::now()->addSecond()->toDateTimeString();
        return new BroadcastMessage([
            'Title' => $this->title,
            'Body' => "You've been assigned to a new vessel!",
            'Created_date' => $timestamp,

        ]);
    }
}

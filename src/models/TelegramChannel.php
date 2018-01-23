<?php

namespace rias\telegramnotificationchannel\models;

use rias\notifications\models\Notification;
use rias\telegramnotificationchannel\exceptions\CouldNotSendNotification;

class TelegramChannel
{
    /**
     * @var Telegram
     */
    protected $telegram;

    /**
     * Channel constructor.
     *
     * @param Telegram $telegram
     */
    public function __construct(Telegram $telegram)
    {
        $this->telegram = $telegram;
    }

    /**
     * Send the given notification.
     *
     * @param mixed        $notifiable
     * @param Notification $notification
     *
     * @throws CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toTelegram();

        if (is_string($message)) {
            $message = TelegramMessage::create($message);
        }

        if ($message->toNotGiven()) {
            if (empty($notifiable)) {
                throw CouldNotSendNotification::chatIdNotProvided();
            }
            $message->to($notifiable);
        }

        $params = $message->toArray();
        $this->telegram->sendMessage($params);
    }
}

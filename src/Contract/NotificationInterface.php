<?php

namespace SeaGM\HitPoints\Contract;

interface NotificationInterface
{
    /**
     * Get HitPoints Callback notification
     *
     * @return array
     */
    public function getNotification();

    /**
     * Response HitPoints that the notification has received
     *
     * @return void
     */
    public function ackNotificationReceived();
}
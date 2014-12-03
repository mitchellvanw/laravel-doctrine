<?php namespace Mitch\LaravelDoctrine\Traits;

trait EmailReminder
{
    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail()
    {
        return $this->email;
    }
} 
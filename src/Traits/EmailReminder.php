<?php namespace Mitch\LaravelDoctrine\Traits;

trait EmailReminder {

    public function getReminderEmail() {
        return $this->email;
    }
} 
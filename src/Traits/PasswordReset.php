<?php namespace Mitch\LaravelDoctrine\Traits;

trait PasswordReset {

    /**
     * Get the e-mail address where password reset links are sent.
     * @return string
     */
    public function getEmailForPasswordReset() {
        return $this->email;
    }
} 
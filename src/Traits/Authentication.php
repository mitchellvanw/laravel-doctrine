<?php namespace Mitch\LaravelDoctrine\Traits;

use Doctrine\ORM\Mapping AS ORM;

trait Authentication {

    /**
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(name="remember_token", type="string", nullable=true)
     */
    private $rememberToken;

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     * Get the unique identifier for the user.
     * @return mixed
     */
    public function getAuthIdentifier() {
	    return method_exists($this, 'getKey') ? $this->getKey() : $this->id;
    }

    /**
     * Get the password for the user.
     * @return string
     */
    public function getAuthPassword() {
        return $this->getPassword();
    }

    /**
     * Get the token value for the "remember me" session.
     * @return string
     */
    public function getRememberToken() {
        return $this->rememberToken;
    }

    /**
     * Set the token value for the "remember me" session.
     * @param  string $value
     * @return void
     */
    public function setRememberToken($value) {
        $this->rememberToken = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     * @return string
     */
    public function getRememberTokenName() {
        return 'rememberToken';
    }
} 

<?php  namespace Mitch\LaravelDoctrine\Traits; 

trait Authentication
{
    use RememberToken;

    /**
     * @Column(type="string")
     */
    private $password;

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getId();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        $this->getPassword();
    }
} 

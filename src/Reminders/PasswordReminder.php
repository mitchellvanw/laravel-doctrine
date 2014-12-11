<?php

namespace Mitch\LaravelDoctrine\Reminders;

use DateTime;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="password_reminders")
 */

class PasswordReminder {

    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     */

    protected $email;

    /**
     * @ORM\Column(type="string")
     */

    protected $token;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     * @var DateTime
     */

    private $createdAt;

    /**
     * Constructs the PasswordReminder.
     *
     * @param string $email
     * @param string $token
     */

    public function __construct($email, $token) {
        $this->email = $email;
        $this->token = $token;
        $this->createdAt = new DateTime();
    }

    /**
     * Returns when the reminder was created.
     *
     * @return DateTime
     */

    public function getCreatedAt() {
        return $this->createdAt;
    }

}

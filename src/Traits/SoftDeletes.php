<?php namespace Mitch\LaravelDoctrine\Traits;

use Doctrine\ORM\Mapping AS ORM;
use DateTime;

trait SoftDeletes
{
    /**
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     * @var \DateTime
     */
    private $deletedAt;

    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(DateTime $deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }

    public function isDeleted()
    {
        return new DateTime > $this->deletedAt;
    }
}

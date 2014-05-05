<?php namespace Mitch\LaravelDoctrine\Traits;

use DateTime;

trait SoftDeletes
{
    /**
     * @Column(name="deleted_at", type="datetime", nullable=true)
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
        return $this->deletedAt !== null && $this->deletedAt > new DateTime;
    }
}

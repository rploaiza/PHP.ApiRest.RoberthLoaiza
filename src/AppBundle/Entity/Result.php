<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Result
 *
 * @ORM\Table(name="results",indexes={
 *          @ORM\Index(name="fk_results_users_idx", columns={"users_id"})
 *      })
 * @ORM\Entity()
 */
class Result implements \JsonSerializable
{
    const ID = 'id';
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="result", type="integer", nullable=false)
     */
    private $result;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time", type="datetime", nullable=false)
     */
    private $time;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="users_id", referencedColumnName="id")
     * })
     */
    protected $users;

    /**
     * Result constructor.
     * @param int $result
     * @param \DateTime $time
     * @param User $users
     */
    public function __construct($result, $users, $time)
    {
        $this->id = 0;
        $this->result = $result;
        $this->users = $users;
        $this->time = $time;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set result
     *
     * @param string $result
     *
     * @return Result
     */
    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }

    /**
     * Get result
     *
     * @return string
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set user
     *
     * @param User $users
     *
     * @return Result
     */
    public function setUser($users)
    {
        $this->users = $users;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->users;
    }

    /**
     * Set time
     *
     * @param \DateTime $time
     *
     * @return Result
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Representation of User as string
     *
     * @return string
     */
    public function __toString(): string
    {
        return sprintf(
            '%3d - %3d - %30s - %s',
            $this->id,
            $this->result,
            $this->users,
            $this->time->format('Y-m-d H:i:s')
        );
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since  5.4.0
     */
    public function jsonSerialize(): array
    {
        return array(
            'id' => $this->id,
            'result' => $this->result,
            'user' => $this->users,
            'time' => $this->time
        );
    }
}


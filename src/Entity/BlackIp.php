<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\BlackIpRepository;

/**
 * @ORM\Entity(repositoryClass=BlackIpRepository::class)
 */
class BlackIp
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ip;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param mixed $ip
     * @return $this
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
        return $this;
    }


}
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AttendanceRepository")
 */
class Attendance
{
    const FINES=
        [
            'late'=>'Опоздание',
            'sabotage'=>'Саботаж',
            'denial'=>'Отказ от работы',
            'manually deleted' => 'Удален вручную',
            'no'=>'Нет Штрафа',
        ];
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $login;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateTime;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sector;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $direction;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fine;

    /**
     * @ORM\Column(type="string", length=1024, nullable=true)
     */
    private $comment;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $fine_approved;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $shift;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getDateTime(): ?\DateTimeInterface
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTimeInterface $dateTime): self
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getSector(): ?string
    {
        return $this->sector;
    }

    public function setSector(?string $sector): self
    {
        $this->sector = $sector;

        return $this;
    }

    public function getDirection(): ?string
    {
        return $this->direction;
    }

    public function setDirection(string $direction): self
    {
        $this->direction = $direction;

        return $this;
    }

    public function getFine(): ?string
    {
        return $this->fine;
    }

    public function setFine(?string $fine): self
    {
        $this->fine = $fine;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getFineApproved(): ?bool
    {
        return $this->fine_approved;
    }

    public function setFineApproved(?bool $fine_approved): self
    {
        $this->fine_approved = $fine_approved;

        return $this;
    }

    public function getShift(): ?int
    {
        return $this->shift;
    }

    public function setShift(?int $shift): self
    {
        $this->shift = $shift;

        return $this;
    }

}

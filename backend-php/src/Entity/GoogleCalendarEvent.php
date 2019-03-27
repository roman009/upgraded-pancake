<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GoogleCalendarEventRepository")
 */
class GoogleCalendarEvent
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_FINSHED = 'finished';
    public const STATUS_IN_PROGRESS = 'in_progress';
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $googleEventId;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $summary;

    /**
     * @ORM\Column(type="datetime")
     */
    private $start;

    /**
     * @ORM\Column(type="datetime")
     */
    private $end;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $realEndTime;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\GoogleCalendarEventAttendee", mappedBy="googleCalendarEvent")
     */
    private $googleCalendarEventAttendees;

    private $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGoogleEventId(): ?string
    {
        return $this->googleEventId;
    }

    public function setGoogleEventId(string $googleEventId): self
    {
        $this->googleEventId = $googleEventId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     * @return GoogleCalendarEvent
     */
    public function setDescription($description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @param mixed $summary
     * @return GoogleCalendarEvent
     */
    public function setSummary($summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param mixed $start
     * @return GoogleCalendarEvent
     */
    public function setStart($start): self
    {
        $this->start = $start;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param mixed $end
     * @return GoogleCalendarEvent
     */
    public function setEnd($end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getAttendees(): array
    {
        if (null === $this->googleCalendarEventAttendees) {
            return [];
        }

        return array_map(function (GoogleCalendarEventAttendee $googleCalendarEventAttendee) {
            return $googleCalendarEventAttendee->getAttendee();
        }, $this->googleCalendarEventAttendees->toArray());
    }

    /**
     * @return mixed
     */
    public function getRealEndTime()
    {
        return $this->realEndTime;
    }

    /**
     * @param mixed $realEndTime
     * @return GoogleCalendarEvent
     */
    public function setRealEndTime($realEndTime): self
    {
        $this->realEndTime = $realEndTime;

        return $this;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getStatus()
    {
        $currentTime = new \DateTime;

        if (null !== $this->getRealEndTime()) {
            return self::STATUS_FINSHED;
        }

        $endTime = $this->getEnd();

        if ($this->getStart() < $currentTime && $currentTime < $this->getEnd()) {
            return self::STATUS_IN_PROGRESS;
        }

        if ($endTime < $currentTime) {
            return self::STATUS_FINSHED;
        }

        return self::STATUS_PENDING;
    }

    /**
     * @param mixed $status
     * @return GoogleCalendarEvent
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}

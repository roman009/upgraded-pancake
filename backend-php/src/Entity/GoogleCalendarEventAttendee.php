<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GoogleCalendarEventAttendeeRepository")
 */
class GoogleCalendarEventAttendee
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Attendee", inversedBy="googleCalendarEventAttendees")
     * @ORM\JoinColumn(nullable=false)
     */
    private $attendee;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\GoogleCalendarEvent", inversedBy="googleCalendarEventAttendees")
     * @ORM\JoinColumn(nullable=false)
     */
    private $googleCalendarEvent;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getGoogleCalendarEvent()
    {
        return $this->googleCalendarEvent;
    }

    /**
     * @param mixed $googleCalendarEvent
     */
    public function setGoogleCalendarEvent($googleCalendarEvent): self
    {
        $this->googleCalendarEvent = $googleCalendarEvent;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAttendee()
    {
        return $this->attendee;
    }

    /**
     * @param mixed $attendee
     */
    public function setAttendee($attendee): void
    {
        $this->attendee = $attendee;
    }
}

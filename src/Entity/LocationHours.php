<?php

namespace OHMedia\ContactBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OHMedia\ContactBundle\Repository\LocationHoursRepository;

#[ORM\Entity(repositoryClass: LocationHoursRepository::class)]
class LocationHours
{
    // ISO 8601 numeric representation of the day of the week
    public const DAY_MONDAY = 1;
    public const DAY_TUESDAY = 2;
    public const DAY_WEDNESDAY = 3;
    public const DAY_THURSDAY = 4;
    public const DAY_FRIDAY = 5;
    public const DAY_SATURDAY = 6;
    public const DAY_SUNDAY = 7;
    public const DAY_HOLIDAY = 99;

    public static function getDayChoices(): array
    {
        return [
            'Monday' => self::DAY_MONDAY,
            'Tuesday' => self::DAY_TUESDAY,
            'Wednesday' => self::DAY_WEDNESDAY,
            'Thursday' => self::DAY_THURSDAY,
            'Friday' => self::DAY_FRIDAY,
            'Saturday' => self::DAY_SATURDAY,
            'Sunday' => self::DAY_SUNDAY,
            'Holidays' => self::DAY_HOLIDAY,
        ];
    }

    public static function getDayMap(): array
    {
        return array_flip(self::getDayChoices());
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?bool $closed = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $day = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $open = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $close = null;

    #[ORM\Column(nullable: true)]
    private ?bool $next_day_close = null;

    #[ORM\ManyToOne(inversedBy: 'hours')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Location $location = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isClosed(): ?bool
    {
        return $this->closed;
    }

    public function setClosed(?bool $closed): static
    {
        $this->closed = $closed;

        return $this;
    }

    public function getDay(): ?int
    {
        return $this->day;
    }

    public function setDay(int $day): static
    {
        $this->day = $day;

        return $this;
    }

    public function getOpen(): ?\DateTimeImmutable
    {
        return $this->open;
    }

    public function setOpen(?\DateTimeImmutable $open): static
    {
        $this->open = $open;

        return $this;
    }

    public function getClose(): ?\DateTimeImmutable
    {
        return $this->close;
    }

    public function setClose(?\DateTimeImmutable $close): static
    {
        $this->close = $close;

        return $this;
    }

    public function isNextDayClose(): ?bool
    {
        return $this->next_day_close;
    }

    public function setNextDayClose(?bool $next_day_close): static
    {
        $this->next_day_close = $next_day_close;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): static
    {
        $this->location = $location;

        return $this;
    }
}

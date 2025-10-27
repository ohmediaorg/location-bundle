<?php

namespace OHMedia\ContactBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OHMedia\ContactBundle\Repository\LocationRepository;
use OHMedia\UtilityBundle\Entity\BlameableEntityTrait;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LocationRepository::class)]
class Location
{
    use BlameableEntityTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $ordinal = 9999;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private ?string $address = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private ?string $city = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private ?string $province = 'SK';

    #[ORM\Column(length: 3)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 3)]
    private ?string $country = 'CAN';

    #[ORM\Column(length: 10)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 10)]
    private ?string $postal_code = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Length(max: 50)]
    private ?string $phone = null;

    #[ORM\Column(length: 180, nullable: true)]
    #[Assert\Length(max: 180)]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(nullable: true, name: 'main')]
    private ?bool $primary = null;

    #[ORM\Column(nullable: true)]
    private ?bool $contact = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Length(max: 50)]
    private ?string $subject = null;

    /**
     * @var Collection<int, LocationHours>
     */
    #[ORM\OneToMany(targetEntity: LocationHours::class, mappedBy: 'location', orphanRemoval: true, cascade: ['persist', 'remove'])]
    #[ORM\OrderBy(['day' => 'ASC', 'open' => 'ASC'])]
    private Collection $hours;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Length(max: 50)]
    private ?string $fax = null;

    public function __construct()
    {
        $this->hours = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrdinal(): ?int
    {
        return $this->ordinal;
    }

    public function setOrdinal(int $ordinal): self
    {
        $this->ordinal = $ordinal;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getProvince(): ?string
    {
        return $this->province;
    }

    public function setProvince(?string $province): static
    {
        $this->province = $province;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postal_code;
    }

    public function setPostalCode(?string $postal_code): static
    {
        $this->postal_code = $postal_code;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function isPrimary(): ?bool
    {
        return $this->primary;
    }

    public function setPrimary(?bool $primary): static
    {
        $this->primary = $primary;

        return $this;
    }

    public function isContact(): ?bool
    {
        return $this->contact;
    }

    public function setContact(?bool $contact): static
    {
        $this->contact = $contact;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function isContactEligible(): bool
    {
        return $this->email && $this->contact && $this->subject;
    }

    /**
     * @return Collection<int, LocationHours>
     */
    public function getHours(): Collection
    {
        return $this->hours;
    }

    public function addHour(LocationHours $hour): static
    {
        if (!$this->hours->contains($hour)) {
            $this->hours->add($hour);
            $hour->setLocation($this);
        }

        return $this;
    }

    public function removeHour(LocationHours $hour): static
    {
        if ($this->hours->removeElement($hour)) {
            // set the owning side to null (unless already changed)
            if ($hour->getLocation() === $this) {
                $hour->setLocation(null);
            }
        }

        return $this;
    }

    public function getHoursFormatted()
    {
        $map = LocationHours::getDayMap();

        $hours = [];

        foreach ($map as $day => $dayFull) {
            $hours[$dayFull] = [];
        }

        foreach ($this->hours as $locationHours) {
            $day = $locationHours->getDay();
            $dayFull = $map[$day];

            if ($locationHours->isClosed()) {
                continue;
            }

            $open = $locationHours->getOpen();
            $close = $locationHours->getClose();

            $openHours = $open->format('g');
            $openMinutes = $open->format('i');
            $openAmPm = $open->format('a');

            $closeHours = $close->format('g');
            $closeMinutes = $close->format('i');
            $closeAmPm = $close->format('a');

            if ('00' !== $openMinutes) {
                $openHours .= ':'.$openMinutes;
            }

            $openHours .= $openAmPm;

            if ('00' !== $closeMinutes) {
                $closeHours .= ':'.$closeMinutes;
            }

            $closeHours .= $closeAmPm;

            $hours[$dayFull][] = sprintf(
                '%s-%s',
                $openHours,
                $closeHours
            );
        }

        $flattened = [];

        foreach ($hours as $dayFull => $array) {
            $flattened[$dayFull] = $array ? implode(', ', $array) : 'Closed';
        }

        return $flattened;
    }

    public function getHoursSchema()
    {
        $nextDayMap = [
            LocationHours::DAY_SUNDAY => LocationHours::DAY_MONDAY,
            LocationHours::DAY_MONDAY => LocationHours::DAY_TUESDAY,
            LocationHours::DAY_TUESDAY => LocationHours::DAY_WEDNESDAY,
            LocationHours::DAY_WEDNESDAY => LocationHours::DAY_THURSDAY,
            LocationHours::DAY_THURSDAY => LocationHours::DAY_FRIDAY,
            LocationHours::DAY_FRIDAY => LocationHours::DAY_SATURDAY,
            LocationHours::DAY_SATURDAY => LocationHours::DAY_MONDAY,
        ];

        $schema = [];

        foreach ($this->hours as $locationHours) {
            if ($locationHours->isClosed()) {
                continue;
            }

            $day = $locationHours->getDay();

            if (LocationHours::DAY_HOLIDAY === $day) {
                continue;
            }

            if ($locationHours->isNextDayClose()) {
                $nextDay = $nextDayMap[$day];

                $schema[] = sprintf(
                    '%s %s:00-23:59:59',
                    $day,
                    $locationHours->getOpen()->format('H:i'),
                );

                $schema[] = sprintf(
                    '%s 00:00:00-%s:00',
                    $nextDay,
                    $locationHours->getClose()->format('H:i'),
                );
            } else {
                $schema[] = sprintf(
                    '%s %s:00-%s:00',
                    $day,
                    $locationHours->getOpen()->format('H:i'),
                    $locationHours->getClose()->format('H:i'),
                );
            }
        }

        return $schema;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(?string $fax): static
    {
        $this->fax = $fax;

        return $this;
    }
}

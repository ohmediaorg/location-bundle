<?php

namespace OHMedia\LocationBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OHMedia\LocationBundle\Repository\LocationRepository;
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
        $map = LocationHours::getDayMap();

        $leftovers = LocationHours::getDayMap();

        unset($leftovers[LocationHours::DAY_HOLIDAY]);

        $schema = [];

        // https://schema.org/OpeningHoursSpecification states:
        // The place is open if the `opens` property is specified,
        // and closed otherwise.

        foreach ($this->hours as $locationHours) {
            $day = $locationHours->getDay();

            if (LocationHours::DAY_HOLIDAY === $day) {
                continue;
            }

            if (isset($leftovers[$day])) {
                unset($leftovers[$day]);
            }

            $dayFull = $map[$day];

            $entry = [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => 'https://schema.org/'.$dayFull,
            ];

            if (!$locationHours->isClosed()) {
                $entry['opens'] = $locationHours->getOpen()->format('H:i:00');
                $entry['closes'] = $locationHours->getClose()->format('H:i:00');
            }

            $schema[] = $entry;
        }

        foreach ($leftovers as $day => $dayFull) {
            $schema[] = [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => 'https://schema.org/'.$dayFull,
            ];
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

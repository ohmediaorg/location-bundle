<?php

namespace OHMedia\LocationBundle\Twig;

use OHMedia\LocationBundle\Entity\Location;
use OHMedia\LocationBundle\Repository\LocationRepository;
use OHMedia\SettingsBundle\Service\Settings;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LocationExtension extends AbstractExtension
{
    private array $schemas = [];

    public function __construct(
        private LocationRepository $locationRepository,
        private Settings $settings
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('location_primary', [$this, 'locationPrimary']),
            new TwigFunction('locations', [$this, 'locations']),
            new TwigFunction('locations_schema', [$this, 'locationsSchema'], [
                'is_safe' => ['html'],
            ]),
            new TwigFunction('location_schema', [$this, 'locationSchema'], [
                'is_safe' => ['html'],
            ]),
        ];
    }

    public function locationPrimary(): ?Location
    {
        return $this->locationRepository->findPrimary();
    }

    public function locations(): array
    {
        return $this->locationRepository->findAllOrdered();
    }

    public function locationsSchema(): string
    {
        $locations = $this->locationRepository->findAll();

        $output = '';

        foreach ($locations as $location) {
            $output .= $this->locationSchema($location);
        }

        return $output;
    }

    public function locationSchema(?Location $location): string
    {
        if (!$location) {
            return '';
        }

        $id = $location->getId();

        if (isset($this->schemas[$id])) {
            return '';
        }

        $this->schemas[$id] = true;

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => $location->getName(),
            'openingHours' => $location->getHoursSchema(),
            'address' => [[
                '@type' => 'PostalAddress',
                'addressLocality' => $location->getCity(),
                'addressRegion' => $location->getProvince(),
                'addressCountry' => $location->getCountry(),
                'streetAddress' => $location->getAddress(),
                'postalCode' => $location->getPostalCode(),
            ]],
        ];

        if ($email = $location->getEmail()) {
            $schema['email'] = $email;
        }

        if ($phone = $location->getPhone()) {
            $schema['telephone'] = $phone;
        }

        if ($fax = $location->getFax()) {
            $schema['faxNumber'] = $fax;
        }

        $organizationName = $this->settings->get('schema_organization_name');

        if ($organizationName) {
            $schema['parentOrganization'] = [
                '@type' => 'Organization',
                'name' => $organizationName,
            ];
        }

        return '<script type="application/ld+json">'.json_encode($schema).'</script>';
    }
}

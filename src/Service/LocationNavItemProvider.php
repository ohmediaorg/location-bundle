<?php

namespace OHMedia\ContactBundle\Service;

use OHMedia\BackendBundle\Service\AbstractNavItemProvider;
use OHMedia\BootstrapBundle\Component\Nav\NavItemInterface;
use OHMedia\BootstrapBundle\Component\Nav\NavLink;
use OHMedia\ContactBundle\Entity\Location;
use OHMedia\ContactBundle\Security\Voter\LocationVoter;

class LocationNavItemProvider extends AbstractNavItemProvider
{
    public function getNavItem(): ?NavItemInterface
    {
        if ($this->isGranted(LocationVoter::INDEX, new Location())) {
            return (new NavLink('Locations', 'location_index'))
                ->setIcon('buildings-fill');
        }

        return null;
    }
}

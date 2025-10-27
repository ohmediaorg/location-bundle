<?php

namespace OHMedia\LocationBundle\Service;

use OHMedia\LocationBundle\Entity\Location;
use OHMedia\SecurityBundle\Service\EntityChoiceInterface;

class LocationEntityChoice implements EntityChoiceInterface
{
    public function getLabel(): string
    {
        return 'Locations';
    }

    public function getEntities(): array
    {
        return [Location::class];
    }
}

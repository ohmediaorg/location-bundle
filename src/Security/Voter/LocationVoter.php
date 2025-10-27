<?php

namespace OHMedia\LocationBundle\Security\Voter;

use OHMedia\LocationBundle\Entity\Location;
use OHMedia\SecurityBundle\Entity\User;
use OHMedia\SecurityBundle\Security\Voter\AbstractEntityVoter;

class LocationVoter extends AbstractEntityVoter
{
    public const INDEX = 'index';
    public const CREATE = 'create';
    public const EDIT = 'edit';
    public const DELETE = 'delete';

    protected function getAttributes(): array
    {
        return [
            self::INDEX,
            self::CREATE,
            self::EDIT,
            self::DELETE,
        ];
    }

    protected function getEntityClass(): string
    {
        return Location::class;
    }

    protected function canIndex(Location $location, User $loggedIn): bool
    {
        return true;
    }

    protected function canCreate(Location $location, User $loggedIn): bool
    {
        return true;
    }

    protected function canEdit(Location $location, User $loggedIn): bool
    {
        return true;
    }

    protected function canDelete(Location $location, User $loggedIn): bool
    {
        return true;
    }
}

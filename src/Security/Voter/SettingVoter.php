<?php

namespace OHMedia\ContactBundle\Security\Voter;

use OHMedia\SecurityBundle\Entity\User;
use OHMedia\SecurityBundle\Security\Voter\AbstractEntityVoter;
use OHMedia\SettingsBundle\Entity\Setting;

class SettingVoter extends AbstractEntityVoter
{
    public const CONTACT_FORM = 'contact_form';

    protected function getAttributes(): array
    {
        return [
            self::CONTACT_FORM,
        ];
    }

    protected function getEntityClass(): string
    {
        return Setting::class;
    }

    protected function canContactForm(Setting $setting, User $loggedIn): bool
    {
        return true;
    }
}

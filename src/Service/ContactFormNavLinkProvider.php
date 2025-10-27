<?php

namespace OHMedia\ContactBundle\Service;

use OHMedia\BackendBundle\Service\AbstractSettingsNavLinkProvider;
use OHMedia\BootstrapBundle\Component\Nav\NavLink;
use OHMedia\ContactBundle\Security\Voter\SettingVoter;
use OHMedia\SettingsBundle\Entity\Setting;

class ContactFormNavLinkProvider extends AbstractSettingsNavLinkProvider
{
    public function getNavLink(): NavLink
    {
        return (new NavLink('Contact Form', 'settings_contact_form'))
            ->setIcon('mailbox2-flag');
    }

    public function getVoterAttribute(): string
    {
        return SettingVoter::CONTACT_FORM;
    }

    public function getVoterSubject(): mixed
    {
        return new Setting();
    }
}

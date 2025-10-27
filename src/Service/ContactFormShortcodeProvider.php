<?php

namespace OHMedia\ContactBundle\Service;

use OHMedia\WysiwygBundle\Shortcodes\AbstractShortcodeProvider;
use OHMedia\WysiwygBundle\Shortcodes\Shortcode;

class ContactFormShortcodeProvider extends AbstractShortcodeProvider
{
    public function getTitle(): string
    {
        return 'Contact Form';
    }

    public function buildShortcodes(): void
    {
        $this->addShortcode(new Shortcode(
            'Contact Form',
            'contact_form()'
        ));
    }
}

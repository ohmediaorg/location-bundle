<?php

namespace OHMedia\ContactBundle\Twig;

use OHMedia\ContactBundle\Service\ContactForm;
use OHMedia\WysiwygBundle\Twig\AbstractWysiwygExtension;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Twig\Environment;
use Twig\TwigFunction;

class ContactFormExtension extends AbstractWysiwygExtension
{
    public function __construct(
        private ContactForm $contactForm,
        #[Autowire('%oh_media_antispam.captcha.sitekey%')]
        private string $captchaSitekey
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('contact_form', [$this, 'form'], [
                'is_safe' => ['html'],
                'needs_environment' => true,
            ]),
        ];
    }

    public function form(Environment $twig): string
    {
        $form = $this->contactForm->buildForm();

        if (!$form) {
            return '';
        }

        return $twig->render('@OHMediaContact/contact_form.html.twig', [
            'form' => $form->createView(),
            'success_message' => $this->contactForm->getSuccessMessage(),
            'captcha_sitekey' => $this->captchaSitekey,
        ]);
    }
}

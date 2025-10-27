<?php

namespace OHMedia\ContactBundle\Service;

use OHMedia\AntispamBundle\Form\Type\CaptchaType;
use OHMedia\AntispamBundle\Validator\Constraints\NoForeignCharacters;
use OHMedia\ContactBundle\Repository\LocationRepository;
use OHMedia\SettingsBundle\Service\Settings;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ContactForm
{
    public const DEFAULT_SUCCESS_MESSAGE = 'Thanks for your submission. We will get back to you as soon as we can.';

    private const NAME_LENGTH = 100;
    private const EMAIL_LENGTH = 180;
    private const PHONE_LENGTH = 20;
    private const MESSAGE_LENGTH = 1000;

    private array $locations = [];
    private array $subjects = [];
    private ?string $defaultRecipient = '';

    public function __construct(
        private FormFactoryInterface $formFactory,
        private LocationRepository $locationRepository,
        private Settings $settings
    ) {
    }

    public function buildForm(): ?FormInterface
    {
        $formBuilder = $this->formFactory->createBuilder(FormType::class);

        $this->subjects = [];

        $this->defaultRecipient = $this->settings->get('contact_form_recipient');

        if ($this->defaultRecipient) {
            $this->subjects['General Inquiry'] = 'general';
        }

        $locations = $this->locationRepository->findAllOrdered();

        $this->locations = [];

        foreach ($locations as $location) {
            if (!$location->isContactEligible()) {
                continue;
            }

            $id = $location->getId();

            $this->locations[$id] = $location;

            $this->subjects[$location->getSubject()] = 'location:'.$id;
        }

        if (!$this->subjects) {
            return null;
        }

        if (1 === count($this->subjects)) {
            $formBuilder->add('subject', HiddenType::class, [
                'data' => reset($this->subjects),
            ]);
        } else {
            $formBuilder->add('subject', ChoiceType::class, [
                'choices' => $this->subjects,
            ]);
        }

        $formBuilder->add('name', TextType::class, [
            'attr' => [
                'maxlength' => self::NAME_LENGTH,
            ],
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Please fill out your name.',
                ]),
                new Assert\Length([
                    'max' => self::NAME_LENGTH,
                    'maxMessage' => 'Your name must be {{ limit }} characters or less.',
                ]),
                new Assert\NoSuspiciousCharacters(),
                new NoForeignCharacters(),
            ],
        ]);

        $formBuilder->add('email', EmailType::class, [
            'attr' => [
                'maxlength' => self::EMAIL_LENGTH,
            ],
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Please fill out your email.',
                ]),
                new Assert\Email(
                    null,
                    'Please enter a valid email address.'
                ),
                new Assert\Length([
                    'max' => self::EMAIL_LENGTH,
                    'maxMessage' => 'Your email address must be {{ limit }} characters or less.',
                ]),
                new Assert\NoSuspiciousCharacters(),
                new NoForeignCharacters(),
            ],
        ]);

        $formBuilder->add('phone', TelType::class, [
            'attr' => [
                'maxlength' => self::PHONE_LENGTH,
            ],
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Please fill out your phone number.',
                ]),
                new Assert\Length([
                    'max' => self::PHONE_LENGTH,
                    'maxMessage' => 'Your phone number must be {{ limit }} characters or less.',
                ]),
                new Assert\NoSuspiciousCharacters(),
                new NoForeignCharacters(),
            ],
        ]);

        $formBuilder->add('message', TextareaType::class, [
            'attr' => [
                'maxlength' => self::MESSAGE_LENGTH,
                'rows' => 5,
            ],
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Please enter a message.',
                ]),
                new Assert\Length([
                    'max' => self::MESSAGE_LENGTH,
                    'maxMessage' => 'Please enter a message of {{ limit }} characters or less.',
                ]),
                new Assert\NoSuspiciousCharacters(),
                new NoForeignCharacters(),
            ],
        ]);

        $formBuilder->add('captcha', CaptchaType::class);

        $formBuilder->add('submit', SubmitType::class);

        return $formBuilder->getForm();
    }

    public function getRecipient(string $subject): ?string
    {
        if ('general' === $subject) {
            return $this->defaultRecipient;
        }

        $parts = explode(':', $subject);

        if ('location' === $parts[0] && isset($parts[1])) {
            return isset($this->locations[$parts[1]])
                ? $this->locations[$parts[1]]->getEmail()
                : null;
        }

        return null;
    }

    public function getSubject(string $subject): ?string
    {
        $subject = array_search($subject, $this->subjects);

        return $subject ?: null;
    }

    public function getSuccessMessage(): string
    {
        $successMessage = $this->settings->get('contact_form_message');

        return $successMessage ?: self::DEFAULT_SUCCESS_MESSAGE;
    }
}

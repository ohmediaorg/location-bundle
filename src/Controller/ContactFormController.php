<?php

namespace OHMedia\ContactBundle\Controller;

use OHMedia\ContactBundle\Security\Voter\SettingVoter;
use OHMedia\ContactBundle\Service\ContactForm;
use OHMedia\EmailBundle\Entity\Email;
use OHMedia\EmailBundle\Repository\EmailRepository;
use OHMedia\EmailBundle\Util\EmailAddress;
use OHMedia\SettingsBundle\Entity\Setting;
use OHMedia\SettingsBundle\Service\Settings;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactFormController extends AbstractController
{
    #[Route('/admin/settings/contact-form', name: 'settings_contact_form')]
    public function scripts(Request $request, Settings $settings): Response
    {
        $this->denyAccessUnlessGranted(
            SettingVoter::CONTACT_FORM,
            new Setting()
        );

        $formBuilder = $this->createFormBuilder();

        $formBuilder->add('recipient', EmailType::class, [
            'data' => $settings->get('contact_form_recipient'),
        ]);

        $formBuilder->add('message', TextareaType::class, [
            'required' => false,
            'data' => $settings->get('contact_form_message'),
            'help' => 'The default message is "'.ContactForm::DEFAULT_SUCCESS_MESSAGE.'"',
        ]);

        $formBuilder->add('save', SubmitType::class);

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $formData = $form->getData();

                $settings->set('contact_form_recipient', $formData['recipient']);

                $settings->set('contact_form_message', $formData['message']);

                $this->addFlash('notice', 'Contact form settings updated successfully');

                return $this->redirectToRoute('settings_contact_form');
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        return $this->render('@OHMediaContact/settings/settings_contact_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/contact-form/post', name: 'contact_form_post', methods: ['POST'])]
    public function contactFormPost(
        ContactForm $contactForm,
        EmailRepository $emailRepository,
        Request $request
    ) {
        $form = $contactForm->buildForm();

        if (!$form) {
            return new JsonResponse('Form not found.', 500);
        }

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return new JsonResponse('Form not submitted.', 500);
        }

        if (!$form->isValid()) {
            $formErrorIterator = $form->getErrors(true);

            $errorCount = $formErrorIterator->count();

            $errors = [];

            for ($i = 0; $i < $errorCount; ++$i) {
                $error = $formErrorIterator->offsetGet($i);

                if ($error instanceof FormError) {
                    $errors[] = $error->getMessage();
                } else {
                    $errors[] = (string) $error;
                }
            }

            return new JsonResponse([
                'success' => false,
                'errors' => $errors,
            ]);
        }

        $formData = $form->getData();

        $recipient = $contactForm->getRecipient($formData['subject']);
        $subject = $contactForm->getSubject($formData['subject']);

        if (!$recipient) {
            return new JsonResponse('Unknown recipient.', 500);
        }

        try {
            $to = new EmailAddress($recipient);

            $replyTo = new EmailAddress($formData['email']);

            $email = (new Email())
                ->setSubject('Contact Form: '.$subject)
                ->setTemplate('@OHMediaContact/email/contact_email.html.twig', [
                    'data' => $formData,
                    'subject' => $subject,
                ])
                ->setTo($to)
                ->setReplyTo($replyTo)
            ;

            $emailRepository->save($email, true);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), 500);
        }

        return new JsonResponse(['success' => true]);
    }
}

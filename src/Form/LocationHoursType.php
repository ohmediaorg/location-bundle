<?php

namespace OHMedia\ContactBundle\Form;

use OHMedia\ContactBundle\Entity\LocationHours;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationHoursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('closed', CheckboxType::class, [
            'required' => false,
        ]);

        $builder->add('day', ChoiceType::class, [
            'choices' => LocationHours::getDayChoices(),
        ]);

        $builder->add('open', TimeType::class, [
            'with_seconds' => false,
            'widget' => 'single_text',
        ]);

        $builder->add('close', TimeType::class, [
            'with_seconds' => false,
            'widget' => 'single_text',
        ]);

        $builder->add('next_day_close', CheckboxType::class, [
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LocationHours::class,
        ]);
    }
}

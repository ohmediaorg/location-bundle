<?php

namespace OHMedia\LocationBundle\Form;

use OHMedia\LocationBundle\Entity\Location;
use OHMedia\UtilityBundle\Form\ProvinceType;
use OHMedia\UtilityBundle\Form\StateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $location = $options['data'];

        $builder->add('name');

        $builder->add('address');

        $builder->add('city');

        $builder->add('province');

        $builder->add('provinces', ProvinceType::class, [
            'label' => 'Province',
            'mapped' => false,
            'data' => $location->getProvince(),
        ]);

        $builder->add('states', StateType::class, [
            'label' => 'State',
            'mapped' => false,
            'data' => $location->getProvince(),
        ]);

        $builder->add('country', CountryType::class, [
            'alpha3' => true,
            'preferred_choices' => ['CAN', 'USA'],
            'attr' => [
                'class' => 'nice-select2',
            ],
        ]);

        $builder->add('postal_code', TextType::class, [
            'label' => 'Postal Code',
        ]);

        $builder->add('zip', TextType::class, [
            'label' => 'ZIP',
            'mapped' => false,
            'data' => $location->getPostalCode(),
            'attr' => [
                'maxlength' => 10,
            ],
        ]);

        $builder->add('email', EmailType::class, [
            'required' => false,
        ]);

        $builder->add('phone', TelType::class, [
            'required' => false,
        ]);

        $builder->add('fax', TelType::class, [
            'required' => false,
        ]);

        $builder->add('primary', ChoiceType::class, [
            'label' => 'Is this the primary location?',
            'choices' => [
                'Yes' => true,
                'No' => false,
            ],
            'expanded' => true,
            'row_attr' => [
                'class' => 'fieldset-nostyle mb-3',
            ],
        ]);

        $builder->add('hours', CollectionType::class, [
            'entry_type' => LocationHoursType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
        ]);
    }
}

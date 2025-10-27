<?php

namespace OHMedia\ContactBundle\Form;

use OHMedia\ContactBundle\Entity\Location;
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

        $builder->add('provinces', ChoiceType::class, [
            'label' => 'Province',
            'mapped' => false,
            'data' => $location->getProvince(),
            'attr' => [
                'class' => 'nice-select2',
            ],
            'choices' => [
                'Alberta' => 'AB',
                'British Columbia' => 'BC',
                'Manitoba' => 'MB',
                'New Brunswick' => 'NB',
                'Newfoundland and Labrador' => 'NL',
                'Northwest Territories' => 'NT',
                'Nova Scotia' => 'NS',
                'Nunavut' => 'NU',
                'Ontario' => 'ON',
                'Prince Edward Island' => 'PE',
                'Quebec' => 'QC',
                'Saskatchewan' => 'SK',
                'Yukon' => 'YT',
            ],
        ]);

        $builder->add('states', ChoiceType::class, [
            'label' => 'State',
            'mapped' => false,
            'data' => $location->getProvince(),
            'attr' => [
                'class' => 'nice-select2',
            ],
            'choices' => [
                'Alabama' => 'AL',
                'Alaska' => 'AK',
                'Arizona' => 'AZ',
                'Arkansas' => 'AR',
                'California' => 'CA',
                'Colorado' => 'CO',
                'Connecticut' => 'CT',
                'Delaware' => 'DE',
                'Florida' => 'FL',
                'Georgia' => 'GA',
                'Hawaii' => 'HI',
                'Idaho' => 'ID',
                'Illinois' => 'IL',
                'Indiana' => 'IN',
                'Iowa' => 'IA',
                'Kansas' => 'KS',
                'Kentucky' => 'KY',
                'Louisiana' => 'LA',
                'Maine' => 'ME',
                'Maryland' => 'MD',
                'Massachusetts' => 'MA',
                'Michigan' => 'MI',
                'Minnesota' => 'MN',
                'Mississippi' => 'MS',
                'Missouri' => 'MO',
                'Montana' => 'MT',
                'Nebraska' => 'NE',
                'Nevada' => 'NV',
                'New Hampshire' => 'NH',
                'New Jersey' => 'NJ',
                'New Mexico' => 'NM',
                'New York' => 'NY',
                'North Carolina' => 'NC',
                'North Dakota' => 'ND',
                'Ohio' => 'OH',
                'Oklahoma' => 'OK',
                'Oregon' => 'OR',
                'Pennsylvania' => 'PA',
                'Rhode Island' => 'RI',
                'South Carolina' => 'SC',
                'South Dakota' => 'SD',
                'Tennessee' => 'TN',
                'Texas' => 'TX',
                'Utah' => 'UT',
                'Vermont' => 'VT',
                'Virginia' => 'VA',
                'Washington' => 'WA',
                'West Virginia' => 'WV',
                'Wisconsin' => 'WI',
                'Wyoming' => 'WY',
            ],
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

        $builder->add('contact', ChoiceType::class, [
            'label' => 'Include in contact form?',
            'choices' => [
                'Yes' => true,
                'No' => false,
            ],
            'expanded' => true,
            'row_attr' => [
                'class' => 'fieldset-nostyle mb-3',
            ],
            'help' => 'Email must also be populated.',
        ]);

        $builder->add('subject', TextType::class, [
            'label' => 'Contact Form Subject',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
        ]);
    }
}

<?php

namespace App\Form;

use libphonenumber\PhoneNumberFormat;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SmsFormType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('mobile_number', PhoneNumberType::class, [
            'default_region' => 'GB',
            'format' => PhoneNumberFormat::E164,
            'label' => false,
            'required' => true,
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter a mobile number to send too',
                ]),
            ],
        ])->add('message', TextareaType::class, [
            'label' => false,
            'required' => true,
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter a message to send',
                ]),
                new Length([
                    'max' => 140,
                    'maxMessage' => 'Message can not be longer than {{ limit }} characters',
                ]),
            ],
        ]);
    }
}

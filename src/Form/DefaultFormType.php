<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DefaultFormType extends AbstractType
{
    private const EXCLUDED_FIELDS = ['id', 'createdAt', 'createdBy', 'updatedAt', 'updatedBy', 'deletedAt', 'deletedBy'];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var FieldInterface[] */
        $fields = $options['fields'];

        foreach ($fields as $field) {
            if (!$field->showOnForm || in_array($field->name, self::EXCLUDED_FIELDS, true)) {
                continue;
            }

            $options = ['label' => $field->label] + $field->formTypeOptions;

            $builder->add($field->name, $field->formType, $options);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => null,
                'fields' => [],
            ])
            ->setAllowedTypes('fields', 'array')
        ;
    }
}

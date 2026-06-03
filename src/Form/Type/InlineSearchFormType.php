<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class InlineSearchFormType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => Request::METHOD_GET,
            'csrf_protection' => false,
            'allow_extra_fields' => true,
            'attr' => [
                'class' => 'flex flex-col md:flex-row gap-4 mb-2',
                'data-controller' => 'auto-submit',
                'data-auto-submit-delay-value' => 300,
                'data-action' => 'input->auto-submit#submit change->auto-submit#submit',
            ],
        ]);
    }
}

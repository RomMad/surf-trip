<?php

declare(strict_types=1);

namespace App\Form\Type\Autocomplete;

use App\Controller\Trip\TripAutocompleteController;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

final class TripAutocompleteType extends AbstractType
{
    public function __construct(
        private readonly RouterInterface $router,
    ) {}

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'label' => 'trip.label',
                'choice_label' => 'label',
                'choice_value' => 'id',
                'choice_translation_domain' => false,
                'autocomplete' => true,
                'autocomplete_url' => $this->router->generate(TripAutocompleteController::class),
                'max_results' => 10,
                'min_characters' => 3,
                'preload' => 'focus',
                'attr' => [
                    'placeholder' => 'trip.placeholder',
                ],
                'required' => false,
            ])
        ;
    }

    #[\Override]
    public function getParent(): string
    {
        return ChoiceType::class;
    }
}

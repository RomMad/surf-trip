<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Controller\User\UserSearchController;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

final class OwnersAutocompleteType extends AbstractType
{
    public function __construct(
        private readonly RouterInterface $router,
    ) {}

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'label' => 'owners.label',
                'choice_label' => 'fullName',
                'choice_value' => 'id',
                'choice_translation_domain' => false,
                'multiple' => true,
                'autocomplete' => true,
                'autocomplete_url' => $this->router->generate(UserSearchController::class),
                'max_results' => 10,
                'min_characters' => 3,
                'preload' => false,
                'attr' => [
                    'placeholder' => 'owners.placeholder',
                ],
            ])
        ;
    }

    #[\Override]
    public function getParent(): string
    {
        return ChoiceType::class;
    }
}

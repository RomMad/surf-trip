<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
class UserAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => User::class,
            'placeholder' => 'user.placeholder',
            'choice_label' => 'fullName',
            'multiple' => true,
            'by_reference' => false,
            'filter_query' => function (QueryBuilder $qb, string $query, EntityRepository $repository): void {
                if (!$query) {
                    return;
                }

                $qb->andWhere('entity.firstName LIKE :filter OR entity.lastName LIKE :filter')
                    ->setParameter('filter', '%'.$query.'%')
                ;
            },
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}

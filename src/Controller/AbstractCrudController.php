<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\DefaultFormType;
use App\ReadModel\Field;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Dto\CrudDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(
    path: self::PREFIX_PATH,
    name: self::PREFIX_ROUTE,
)]
abstract class AbstractCrudController extends AbstractController
{
    protected const string PREFIX_PATH = '/crud';
    protected const string PREFIX_ROUTE = 'app.crud.';

    protected const ?string TEMPLATE_INDEX = 'crud/index.html.twig';
    protected const ?string TEMPLATE_NEW = 'crud/new.html.twig';
    protected const ?string TEMPLATE_SHOW = 'crud/show.html.twig';
    protected const ?string TEMPLATE_EDIT = 'crud/edit.html.twig';

    protected const string MESSAGE_CREATE_SUCCESS = 'created_successfully';
    protected const string MESSAGE_UPDATE_SUCCESS = 'updated_successfully';
    protected const string MESSAGE_DELETE_SUCCESS = 'deleted_successfully';

    protected const string MESSAGE_TYPE = 'success';
    protected const string MESSAGE_CREATE_TYPE = self::MESSAGE_TYPE;
    protected const string MESSAGE_UPDATE_TYPE = self::MESSAGE_TYPE;
    protected const string MESSAGE_DELETE_TYPE = self::MESSAGE_TYPE;

    protected const string MESSAGE_DOMAIN = 'crud_messages';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TranslatorInterface $translator,
    ) {}

    abstract public static function getEntityFqcn(): string;

    abstract public function configureCrud(): Crud;

    abstract public static function getVoterFqcn(): ?string;

    public static function getFormTypeFqcn(): string
    {
        return DefaultFormType::class;
    }

    /** @return Field[] */
    abstract public static function configureFields(): iterable;

    protected static function getEntityName(): string
    {
        $entityClass = static::getEntityFqcn();
        $parts = explode('\\', $entityClass);
        $entityName = end($parts);
        $entityName = strtolower($entityName);

        return $entityName;
    }

    #[Route(
        path: '/index',
        name: 'index',
        methods: [Request::METHOD_GET]
    )]
    public function index(Request $request): Response
    {
        $entityClass = static::getEntityFqcn();
        $repository = $this->entityManager->getRepository($entityClass);

        $list = $repository->findBy([], ['id' => 'DESC'], 10);
        $form = null;

        $fields = static::configureFields();
        $headers = [];
        $data = [];

        foreach ($fields as $field) {
            if (!$field->showOnIndex) {
                continue;
            }

            $headers[] = [$this->translator->trans($field->label), 'entity.'.$field->name];
        }

        foreach ($list as $entity) {
            $rowData = [];

            foreach ($fields as $field) {
                if (!$field->showOnIndex) {
                    continue;
                }

                $propertyName = $field->name;
                $value = $entity->{$propertyName};
                $rowData[] = $this->formatValue($value, $field);
            }
            $data[] = $rowData;
        }

        return $this->render('crud/index.html.twig', [
            'pager' => $list,
            'form' => $form,
            'fields' => $fields,
            'crud' => static::getCrudDTO(),
            'headers' => $headers,
            'data' => $data,
            'route' => [
                'new' => static::getRouteNew(),
            ],
        ]);
    }

    #[Route(
        path: '/new',
        name: 'new',
        methods: [Request::METHOD_GET, Request::METHOD_POST]
    )]
    public function new(Request $request): Response
    {
        $entityClass = static::getEntityFqcn();
        $entity = new $entityClass();

        $new = 'NEW';

        if (static::getVoterFqcn() && isset(static::getVoterFqcn()::$new)) {
            $this->denyAccessUnlessGranted($new, $entity);
        }

        $form = $this->createForm(static::getFormTypeFqcn(), $entity, [
            'method' => Request::METHOD_POST,
            'data_class' => $entityClass,
            'fields' => static::configureFields(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($entity);
            $this->entityManager->flush();

            $this->addFlash(
                static::MESSAGE_CREATE_TYPE,
                new TranslatableMessage(static::MESSAGE_CREATE_SUCCESS, domain: static::MESSAGE_DOMAIN)
            );

            return $this->redirectToRoute(
                static::getRouteIndex(),
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render(static::TEMPLATE_NEW, [
            'form' => $form,
            'crud' => static::getCrudDTO(),
            'route' => [
                'index' => static::getRouteIndex(),
            ],
        ]);
    }

    #[Route(
        path: '/{id}',
        name: 'show',
        requirements: [
            'id' => Requirement::POSITIVE_INT,
        ],
        methods: [Request::METHOD_GET]
    )]
    public function show(int $id): Response
    {
        $entity = static::getEntity($id);

        $fields = array_values((array) static::configureFields());
        $data = [];

        foreach ($fields as $field) {
            $propertyName = $field->name;
            $clonedField = clone $field;
            $value = $entity->{$propertyName};

            $data[$propertyName] = [
                'label' => $field->label,
                'value' => $this->formatValue($value, $clonedField),
            ];
        }

        return $this->render(static::TEMPLATE_SHOW, [
            'entity' => $entity,
            'fields' => $fields,
            'data' => $data,
            'crud' => static::getCrudDTO(),
            'route' => [
                'index' => static::getRouteIndex(),
                'edit' => static::getRouteEdit(),
                'delete' => static::getRouteDelete(),
            ],
        ]);
    }

    #[Route(
        path: '/{id}/edit',
        name: 'edit',
        requirements: [
            'id' => Requirement::POSITIVE_INT,
        ],
        methods: [Request::METHOD_GET, Request::METHOD_POST],
    )]
    public function edit(Request $request, int $id): Response
    {
        $entity = static::getEntity($id);

        $this->denyAccessUnlessGranted('EDIT', $entity);

        $form = $this->createForm(static::getFormTypeFqcn(), $entity, [
            'method' => Request::METHOD_POST,
            'data_class' => static::getEntityFqcn(),
            'fields' => static::configureFields(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash(
                static::MESSAGE_UPDATE_TYPE,
                new TranslatableMessage(static::MESSAGE_UPDATE_SUCCESS, domain: static::MESSAGE_DOMAIN)
            );

            return $this->redirectToRoute(
                static::getRouteShow(),
                [
                    'id' => $entity->id,
                ],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render(static::TEMPLATE_EDIT, [
            'entity' => $entity,
            'form' => $form,
            'crud' => static::getCrudDTO(),
            'route' => [
                'index' => static::getRouteIndex(),
                'edit' => static::getRouteEdit(),
                'delete' => static::getRouteDelete(),
            ],
        ]);
    }

    #[Route(
        path: '/{id}/delete',
        name: 'delete',
        requirements: ['id' => Requirement::POSITIVE_INT],
        methods: [Request::METHOD_POST]
    )]
    #[IsCsrfTokenValid(new Expression('"delete" ~ args["id"]'))]
    public function delete(int $id): Response
    {
        $entity = static::getEntity($id);

        $this->denyAccessUnlessGranted('DELETE', $entity);

        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        $this->addFlash(
            static::MESSAGE_DELETE_TYPE,
            new TranslatableMessage(static::MESSAGE_DELETE_SUCCESS, domain: static::MESSAGE_DOMAIN)
        );

        return $this->redirectToRoute(
            static::getRouteIndex(),
            [],
            Response::HTTP_SEE_OTHER
        );
    }

    private function getEntity(int $id)
    {
        $entityClass = static::getEntityFqcn();
        $entity = $this->entityManager->getRepository($entityClass)->find($id);

        if (null === $entity) {
            throw new NotFoundHttpException(
                sprintf('Entity %s with id %d not found', $entityClass, $id)
            );
        }

        return $entity;
    }

    private function getCrudDTO(): CrudDto
    {
        return $this->configureCrud()->getAsDto();
    }

    private function formatValue(mixed $value, Field $field): string
    {
        return match (true) {
            $value instanceof \DateTimeInterface => $value->format($field->format ?? 'd/m/Y'),
            $value instanceof Collection => implode(', ', array_map(fn ($v) => $this->formatValue($v, $field), $value->toArray())),
            is_array($value) => implode(', ', array_map(fn ($v) => $this->formatValue($v, $field), $value)),
            $value instanceof TranslatableInterface => $value->trans($this->translator),
            $value instanceof \Stringable => $value->__toString(),
            null => '',
            default => (string) $value,
        };
    }

    private static function getRouteIndex(): string
    {
        return sprintf('app.crud.%s.index', static::getEntityName());
    }

    private static function getRouteNew(): string
    {
        return sprintf('app.crud.%s.new', static::getEntityName());
    }

    private static function getRouteShow(): string
    {
        return sprintf('app.crud.%s.show', static::getEntityName());
    }

    private static function getRouteEdit(): string
    {
        return sprintf('app.crud.%s.edit', static::getEntityName());
    }

    private static function getRouteDelete(): string
    {
        return sprintf('app.crud.%s.delete', static::getEntityName());
    }
}

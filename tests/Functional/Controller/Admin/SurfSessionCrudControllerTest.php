<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Admin;

use App\Controller\Admin\SurfSessionCrudController;
use App\Factory\SurfSessionFactory;
use App\Repository\SurfSessionRepository;
use App\Tests\Fixtures\SurfSessionStory;
use PHPUnit\Framework\Attributes\Medium;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 *
 * @extends CustomAbstractCrudTestCase<SurfSessionCrudController>
 */
#[Medium]
final class SurfSessionCrudControllerTest extends CustomAbstractCrudTestCase
{
    /**
     * @return class-string<SurfSessionCrudController>
     */
    protected function getControllerFqcn(): string
    {
        return SurfSessionCrudController::class;
    }

    public function testIndexPageIsSuccessful(): void
    {
        $this->client->request(Request::METHOD_GET, $this->generateIndexUrl());

        $this->assertResponseIsSuccessful(verbose: false);
        $this->assertSelectorExists(self::TABLE_SELECTOR);
        $this->assertIndexFullEntityCount(SurfSessionFactory::count());
    }

    public function testNewFormPageIsSuccessful(): void
    {
        /** @var SurfSessionRepository $surfSessionRepository */
        $surfSessionRepository = self::getContainer()->get(SurfSessionRepository::class);
        $formSelector = $this->getEntityFormSelector();
        $newSurfSession = SurfSessionFactory::new()->withoutPersisting()->create();

        $this->client->request(Request::METHOD_GET, $this->generateNewFormUrl());

        $this->assertResponseIsSuccessful(verbose: false);
        $this->assertSelectorExists($formSelector);

        $form = $this->client->getCrawler()->filter($formSelector)->form();

        $this->client->submit($form, [
            'SurfSession[spot]' => $newSurfSession->spot,
            'SurfSession[board]' => $newSurfSession->board,
            'SurfSession[startAt]' => $newSurfSession->startAt->format('Y-m-d\TH:i'),
            'SurfSession[endAt]' => $newSurfSession->endAt->format('Y-m-d\TH:i'),
            'SurfSession[rating]' => (string) $newSurfSession->rating?->value,
            'SurfSession[objective]' => $newSurfSession->objective,
            'SurfSession[comment]' => $newSurfSession->comment,
        ]);

        $this->assertResponseIsSuccessful(verbose: false);

        $lastSurfSession = $surfSessionRepository->findOneBy([], ['id' => 'DESC']);

        $this->assertNotNull($lastSurfSession);
        $this->assertSame($newSurfSession->spot, $lastSurfSession->spot);
    }

    public function testDetailPageIsSuccessful(): void
    {
        $surfSession = SurfSessionFactory::last();

        $this->assertNotNull($surfSession->id);

        $this->client->request(Request::METHOD_GET, $this->generateDetailUrl($surfSession->id));

        $this->assertResponseIsSuccessful(verbose: false);
    }

    public function testEditFormIsSuccessful(): void
    {
        /** @var SurfSessionRepository $surfSessionRepository */
        $surfSessionRepository = self::getContainer()->get(SurfSessionRepository::class);
        $surfSession = SurfSessionFactory::last();
        $editSurfSession = SurfSessionFactory::new()->withoutPersisting()->create();
        $formSelector = $this->getEntityFormSelector();

        $this->assertNotNull($surfSession->id);

        $this->client->request(Request::METHOD_GET, $this->generateEditFormUrl($surfSession->id));

        $this->assertResponseIsSuccessful(verbose: false);
        $this->assertSelectorExists($formSelector);

        $form = $this->client->getCrawler()->filter($formSelector)->form();

        $this->client->submit($form, [
            'SurfSession[spot]' => $editSurfSession->spot,
            'SurfSession[board]' => $editSurfSession->board,
            'SurfSession[startAt]' => $editSurfSession->startAt->format('Y-m-d\TH:i'),
            'SurfSession[endAt]' => $editSurfSession->endAt->format('Y-m-d\TH:i'),
            'SurfSession[rating]' => (string) $editSurfSession->rating?->value,
            'SurfSession[objective]' => $editSurfSession->objective,
            'SurfSession[comment]' => $editSurfSession->comment,
        ]);

        $this->assertResponseIsSuccessful(verbose: false);

        $updatedSurfSession = $surfSessionRepository->find($surfSession->id);

        $this->assertNotNull($updatedSurfSession);
        $this->assertSame($editSurfSession->spot, $updatedSurfSession->spot);
        $this->assertSame($editSurfSession->board, $updatedSurfSession->board);
        $this->assertSame(
            $editSurfSession->startAt->format('Y-m-d\TH:i'),
            $updatedSurfSession->startAt->format('Y-m-d\TH:i')
        );
        $this->assertSame(
            $editSurfSession->endAt->format('Y-m-d\TH:i'),
            $updatedSurfSession->endAt->format('Y-m-d\TH:i')
        );
        $this->assertSame($editSurfSession->rating, $updatedSurfSession->rating);
        $this->assertSame($editSurfSession->objective, $updatedSurfSession->objective);
        $this->assertSame($editSurfSession->comment, $updatedSurfSession->comment);
    }

    public function testSearchIsSuccessful(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            $this->generateIndexUrl(SurfSessionStory::SPOT)
        );

        $this->assertResponseIsSuccessful(verbose: false);
        $this->assertSelectorExists(self::TABLE_SELECTOR);
        $this->assertIndexFullEntityCount(1);
    }
}

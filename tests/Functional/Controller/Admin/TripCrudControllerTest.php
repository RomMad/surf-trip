<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Admin;

use App\Controller\Admin\TripCrudController;
use App\Factory\TripFactory;
use App\Repository\TripRepository;
use App\Tests\Fixtures\TripStory;
use PHPUnit\Framework\Attributes\Medium;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 *
 * @extends CustomAbstractCrudTestCase<TripCrudController>
 */
#[Medium]
final class TripCrudControllerTest extends CustomAbstractCrudTestCase
{
    /**
     * @return class-string<TripCrudController>
     */
    protected function getControllerFqcn(): string
    {
        return TripCrudController::class;
    }

    public function testIndexPageIsSuccessful(): void
    {
        $this->client->request(Request::METHOD_GET, $this->generateIndexUrl());

        $this->assertResponseIsSuccessful(verbose: false);
        $this->assertSelectorExists(self::TABLE_SELECTOR);
        $this->assertIndexFullEntityCount(TripFactory::count());
    }

    public function testNewFormPageIsSuccessful(): void
    {
        $formSelector = $this->getEntityFormSelector();

        $this->client->request(Request::METHOD_GET, $this->generateNewFormUrl());

        $this->assertResponseIsSuccessful(verbose: false);
        $this->assertSelectorExists($formSelector);

        // Impossible to test the owners autocomplete field because it requires a JS environment to work properly.
    }

    public function testDetailPageIsSuccessful(): void
    {
        $trip = TripFactory::last();

        $this->assertNotNull($trip->id);

        $this->client->request(Request::METHOD_GET, $this->generateDetailUrl($trip->id));

        $this->assertResponseIsSuccessful(verbose: false);
    }

    public function testEditFormIsSuccessful(): void
    {
        $trip = TripFactory::last();
        $editTrip = TripFactory::new()->withoutPersisting()->create();
        $formSelector = $this->getEntityFormSelector();

        $this->assertNotNull($trip->id);

        $this->client->request(Request::METHOD_GET, $this->generateEditFormUrl($trip->id));

        $this->assertResponseIsSuccessful(verbose: false);
        $this->assertSelectorExists($formSelector);

        $form = $this->client->getCrawler()->filter($formSelector)->form();

        $this->client->submit($form, [
            'Trip[title]' => $editTrip->title->value,
            'Trip[location][label]' => $editTrip->location->label,
            'Trip[location][comment]' => $editTrip->location->comment,
            'Trip[startAt]' => $editTrip->startAt?->format('Y-m-d\TH:i'),
            'Trip[endAt]' => $editTrip->endAt?->format('Y-m-d\TH:i'),
            'Trip[requiredLevels]' => array_map(fn ($level) => $level->value, $editTrip->requiredLevels),
            'Trip[description]' => $editTrip->description,
            'Trip[owners][autocomplete]' => $trip->owners->map(fn ($owner) => $owner->id)->toArray(), // cannot change owners in this test because the autocomplete field requires a JS environment to work properly.
        ]);

        $this->assertResponseIsSuccessful(verbose: false);

        /** @var TripRepository $tripRepository */
        $tripRepository = self::getContainer()->get(TripRepository::class);

        $updatedTrip = $tripRepository->find($trip->id);

        $this->assertNotNull($updatedTrip);
        $this->assertSame($editTrip->title->value, $updatedTrip->title->value);
        $this->assertSame($editTrip->location->label, $updatedTrip->location->label);
        $this->assertSame($editTrip->location->comment, $updatedTrip->location->comment);
        $this->assertSame(
            $editTrip->startAt?->format('Y-m-d\TH:i'),
            $updatedTrip->startAt?->format('Y-m-d\TH:i')
        );
        $this->assertSame(
            $editTrip->endAt?->format('Y-m-d\TH:i'),
            $updatedTrip->endAt?->format('Y-m-d\TH:i')
        );
        $this->assertSame(
            array_values($editTrip->requiredLevels),
            array_values($updatedTrip->requiredLevels),
        );
        $this->assertSame($editTrip->description, $updatedTrip->description);
    }

    public function testSearchIsSuccessful(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            $this->generateIndexUrl(TripStory::TRIP_TITLE)
        );

        $this->assertResponseIsSuccessful(verbose: false);
        $this->assertSelectorExists(self::TABLE_SELECTOR);
        $this->assertIndexFullEntityCount(1);
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Admin;

use App\Controller\Admin\UserCrudController;
use App\Factory\UserFactory;
use App\Repository\UserRepository;
use App\Tests\Fixtures\UserStory;
use PHPUnit\Framework\Attributes\Medium;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 *
 * @extends CustomAbstractCrudTestCase<UserCrudController>
 */
#[Medium]
final class UserCrudControllerTest extends CustomAbstractCrudTestCase
{
    /**
     * @return class-string<UserCrudController>
     */
    protected function getControllerFqcn(): string
    {
        return UserCrudController::class;
    }

    public function testIndexPageIsSuccessful(): void
    {
        $this->client->request(Request::METHOD_GET, $this->generateIndexUrl());

        $this->assertResponseIsSuccessful(verbose: false);
        $this->assertSelectorExists(self::TABLE_SELECTOR);
        $this->assertIndexFullEntityCount(UserFactory::count());
    }

    public function testNewFormPageIsSuccessful(): void
    {
        /** @var UserRepository $userRepository */
        $userRepository = self::getContainer()->get(UserRepository::class);
        $formSelector = $this->getEntityFormSelector();
        $newUser = UserFactory::new()->withoutPersisting()->create();

        $this->client->request(Request::METHOD_GET, $this->generateNewFormUrl());

        $this->assertResponseIsSuccessful(verbose: false);
        $this->assertSelectorExists($formSelector);

        $form = $this->client->getCrawler()->filter($formSelector)->form();

        $this->client->submit($form, [
            'User[email]' => $newUser->email->value,
            'User[username]' => $newUser->username->value,
            'User[firstName]' => $newUser->firstName->value,
            'User[lastName]' => $newUser->lastName?->value,
            'User[location]' => $newUser->location,
            'User[level]' => $newUser->level?->value,
            'User[instagram]' => $newUser->instagram,
            'User[description]' => $newUser->description,
            'User[locale]' => $newUser->locale->value,
        ]);

        $this->assertResponseIsSuccessful(verbose: false);

        $lastUser = $userRepository->findOneBy([], ['id' => 'DESC']);

        $this->assertNotNull($lastUser);
        $this->assertSame($newUser->email->value, $lastUser->email->value);
        $this->assertSame($newUser->username->value, $lastUser->username->value);
    }

    public function testDetailPageIsSuccessful(): void
    {
        $user = UserFactory::last();

        $this->assertNotNull($user->id);

        $this->client->request(Request::METHOD_GET, $this->generateDetailUrl($user->id));

        $this->assertResponseIsSuccessful(verbose: false);
    }

    public function testEditFormIsSuccessful(): void
    {
        /** @var UserRepository $userRepository */
        $userRepository = self::getContainer()->get(UserRepository::class);
        $user = UserFactory::last();
        $editUser = UserFactory::new()->withoutPersisting()->create();
        $formSelector = $this->getEntityFormSelector();

        $this->assertNotNull($user->id);

        $this->client->request(Request::METHOD_GET, $this->generateEditFormUrl($user->id));

        $this->assertResponseIsSuccessful(verbose: false);
        $this->assertSelectorExists($formSelector);

        $form = $this->client->getCrawler()->filter($formSelector)->form();

        $this->client->submit($form, [
            'User[email]' => $editUser->email->value,
            'User[username]' => $editUser->username->value,
            'User[firstName]' => $editUser->firstName->value,
            'User[lastName]' => $editUser->lastName?->value,
            'User[location]' => $editUser->location,
            'User[level]' => $editUser->level?->value,
            'User[instagram]' => $editUser->instagram,
            'User[description]' => $editUser->description,
            'User[locale]' => $editUser->locale->value,
        ]);

        $this->assertResponseIsSuccessful(verbose: false);

        $updatedUser = $userRepository->find($user->id);

        $this->assertNotNull($updatedUser);
        $this->assertSame($editUser->email->value, $updatedUser->email->value);
        $this->assertSame($editUser->username->value, $updatedUser->username->value);
        $this->assertSame($editUser->firstName->value, $updatedUser->firstName->value);
        $this->assertSame($editUser->lastName?->value, $updatedUser->lastName?->value);
        $this->assertSame($editUser->location, $updatedUser->location);
        $this->assertSame($editUser->level, $updatedUser->level);
        $this->assertSame($editUser->instagram, $updatedUser->instagram);
        $this->assertSame($editUser->description, $updatedUser->description);
        $this->assertSame($editUser->locale, $updatedUser->locale);
    }

    public function testSearchIsSuccessful(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            $this->generateIndexUrl(UserStory::JOHN_FULL_NAME)
        );

        $this->assertResponseIsSuccessful(verbose: false);
        $this->assertSelectorExists(self::TABLE_SELECTOR);
        $this->assertIndexFullEntityCount(1);
    }
}

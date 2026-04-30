<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\User;

use App\Controller\User\SetUserLocaleController;
use App\Entity\ValueObject\Email;
use App\Enum\User\Locale;
use App\Repository\UserRepository;
use App\Tests\CustomWebTestCase;
use App\Tests\Fixtures\UserStory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Medium;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
#[CoversClass(SetUserLocaleController::class)]
#[Medium]
final class SetUserLocaleControllerTest extends CustomWebTestCase
{
    private const string TARGET = '/en/trips';
    private const string PATH = '/en/locale?target='.self::TARGET;

    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->setUpTest(UserStory::class, UserStory::JOHN_EMAIL, followRedirects: false);

        $this->userRepository = $this->getContainer()->get(UserRepository::class);
    }

    public function testLocaleIsSavedForAuthenticatedUserAndRedirectsToTarget(): void
    {
        $this->assertSame(Locale::French->value, UserStory::getJohnUser()->locale->value);

        $this->client->request(Request::METHOD_GET, self::PATH);

        $user = $this->userRepository->findOneByEmail(Email::from(UserStory::JOHN_EMAIL));

        $this->assertResponseRedirects(self::TARGET);
        $this->assertSame(Locale::English->value, $user->locale->value);
    }
}

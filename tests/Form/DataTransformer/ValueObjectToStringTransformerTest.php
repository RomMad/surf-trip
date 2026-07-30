<?php

declare(strict_types=1);

namespace App\Tests\Form\DataTransformer;

use App\Entity\ValueObject\Title;
use App\Form\DataTransformer\ValueObjectToStringTransformer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @internal
 */
#[CoversClass(ValueObjectToStringTransformer::class)]
#[Small]
final class ValueObjectToStringTransformerTest extends TestCase
{
    private const string VALID_TITLE = 'Amazing Surf Spot';

    private ValueObjectToStringTransformer $transformer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transformer = new ValueObjectToStringTransformer(Title::class);
    }

    public function testConstructorWithInvalidClassName(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageIs('The class "stdClass" must implement ValueObjectInterface.');

        new ValueObjectToStringTransformer(\stdClass::class);
    }

    public function testTransformAndReverseTransformRoundTrip(): void
    {
        $originalTitle = new Title(self::VALID_TITLE);

        $transformed = $this->transformer->transform($originalTitle);
        $reversed = $this->transformer->reverseTransform($transformed);

        $this->assertInstanceOf(Title::class, $reversed);
        $this->assertSame($originalTitle->getValue(), $reversed->getValue());
    }

    #[DataProvider('provideInvalidTitle')]
    public function testReverseTransformWithInvalidString(string $invalidTitle): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessageIsOrContains('Invalid value:');

        $this->transformer->reverseTransform($invalidTitle);
    }

    /**
     * @return \Generator<string, array<int, string>>
     */
    public static function provideInvalidTitle(): \Generator
    {
        yield 'empty string' => [''];
        yield 'too short string' => ['abc'];
        yield 'too long string' => [str_repeat('a', 256)];
    }

    #[DataProvider('provideValidTitle')]
    public function testReverseTransformWithVariousValidTitles(string $title): void
    {
        $result = $this->transformer->reverseTransform($title);

        $this->assertInstanceOf(Title::class, $result);
        $this->assertSame($title, $result->getValue());
    }

    /**
     * @return \Generator<string, array<int, string>>
     */
    public static function provideValidTitle(): \Generator
    {
        yield 'minimum length' => ['abcde'];
        yield 'normal title' => [self::VALID_TITLE];
        yield 'maximum length' => [str_repeat('a', 255)];
    }
}

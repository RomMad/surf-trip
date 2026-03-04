<?php

declare(strict_types=1);

namespace App\Form\DataTransformer;

use App\Entity\ValueObject\ValueObjectInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @implements DataTransformerInterface<ValueObjectInterface, string>
 */
final readonly class ValueObjectToStringTransformer implements DataTransformerInterface
{
    public function __construct(
        private string $className
    ) {
        if (!is_a($className, ValueObjectInterface::class, true)) {
            throw new \InvalidArgumentException(sprintf('The class "%s" must implement ValueObjectInterface.', $this->className));
        }
    }

    public function transform(mixed $value): string
    {
        return $value instanceof ValueObjectInterface ? $value->getValue() : '';
    }

    public function reverseTransform(mixed $value): ValueObjectInterface
    {
        try {
            $className = $this->className;

            return new $className($value);
        } catch (\InvalidArgumentException $invalidArgumentException) {
            throw new TransformationFailedException(
                message: sprintf('Invalid value: %s', $value),
                code: 0,
                previous: $invalidArgumentException,
                invalidMessage: $invalidArgumentException->getMessage(),
            );
        }
    }
}

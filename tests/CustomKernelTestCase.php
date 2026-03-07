<?php

declare(strict_types=1);

namespace App\Tests;

use App\Tests\Traits\KernelTestCaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Attribute\ResetDatabase;

/**
 * @internal
 */
#[ResetDatabase]
abstract class CustomKernelTestCase extends KernelTestCase
{
    use KernelTestCaseTrait;
}

<?php

namespace App\Tests\Twig;

use App\Twig\Extension\TypesExtension;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Twig\TwigTest;

final class TypesExtensionTest extends KernelTestCase
{
    #[DataProvider('provideData')]
    public function testBasicIntegration(mixed $input, bool $expectedResult): void
    {
        self::bootKernel();
        /** @var TypesExtension $extension */
        $extension = self::getContainer()->get(TypesExtension::class);

        /** @var TwigTest $test */
        $test = $extension->getTests()[0];

        $result = $test->getCallable()($input);
        self::assertSame($expectedResult, $result);
    }

    public static function provideData(): iterable
    {
        yield [1, true];
        yield ['1', true];
        yield ['0', true];
        yield ['0abc', false];
        yield ['abc', false];
        yield [[], false];
        yield [new \stdClass(), false];
    }
}

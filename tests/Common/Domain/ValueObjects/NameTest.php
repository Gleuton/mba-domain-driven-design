<?php

namespace Tests\Common\Domain\ValueObjects;

use App\Common\Domain\ValueObjects\Name;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class NameTest extends TestCase
{
    public function testShouldCreateNameWithValidValue(): void
    {
        $name = new Name('João Silva');
        $this->assertEquals('João Silva', (string)$name);
    }

    public function testShouldThrowExceptionForEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Name não pode ser vazio');
        new Name('');
    }

    public function testShouldThrowExceptionForNameWithMorThen100Characters(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Name não pode ter mais que 100 caracteres');
        new Name(str_repeat('a', 101));
    }
}

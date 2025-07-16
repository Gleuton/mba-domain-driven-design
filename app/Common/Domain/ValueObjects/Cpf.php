<?php
declare(strict_types=1);

namespace App\Common\Domain\ValueObjects;

class Cpf extends ValueObject
{
    public function __construct(private string $cpf)
    {
        $this->cpf = $this->sanitize($cpf);
        $this->validate();
    }

    private function sanitize(string $value): string
    {
        return preg_replace('/\D/', '', $value);
    }

    private function validate(): void
    {
        $cpf = $this->cpf;

        if (strlen($cpf) !== 11 || !ctype_digit($cpf)) {
            throw new \InvalidArgumentException('CPF deve ter 11 dígitos numéricos');
        }

        if ($this->isSequential($cpf) || !$this->validateDigit(9) || !$this->validateDigit(10)) {
            throw new \InvalidArgumentException('CPF inválido');
        }
    }

    private function isSequential(string $cpf): bool
    {
        return preg_match('/(\d)\1{10}/', $cpf) === 1;
    }

    private function validateDigit(int $position): bool
    {
        if ($position < 9 || $position > 10) {
            throw new \InvalidArgumentException('Posição do dígito deve ser 9 ou 10');
        }

        $sum = 0;
        for ($i = 0; $i < $position; $i++) {
            $sum += (int)$this->cpf[$i] * ($position + 1 - $i);
        }

        $remainder = $sum % 11;
        $digit = ($remainder < 2) ? 0 : 11 - $remainder;

        return (int)$this->cpf[$position] === $digit;
    }

    public function getValue(): string
    {
        return $this->cpf;
    }

    public function formatted(): string
    {
        return vsprintf(
            '%s.%s.%s-%s',
            [
                substr($this->cpf, 0, 3),
                substr($this->cpf, 3, 3),
                substr($this->cpf, 6, 3),
                substr($this->cpf, 9, 2),
            ]
        );
    }

    protected function toArray(): array
    {
        return ['cpf' => $this->formatted()];
    }
}
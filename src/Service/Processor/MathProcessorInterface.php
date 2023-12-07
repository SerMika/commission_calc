<?php

declare(strict_types=1);

namespace App\Service\Processor;

interface MathProcessorInterface
{
    public function setScale(int $scale): void;

    public function div(string $leftOperand, string $rightOperand): string;

    public function mul(string $leftOperand, string $rightOperand): string;

    public function sub(string $leftOperand, string $rightOperand): string;

    public function add(string $leftOperand, string $rightOperand): string;
}

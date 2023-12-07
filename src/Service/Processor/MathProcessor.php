<?php

declare(strict_types=1);

namespace App\Service\Processor;

class MathProcessor implements MathProcessorInterface
{
    private int $scale;

    public function __construct()
    {
        $this->scale = 0;
    }

    public function setScale(int $scale): void
    {
        $this->scale = $scale;
    }

    public function div(string $leftOperand, string $rightOperand): string
    {
        return bcdiv($leftOperand, $rightOperand, $this->scale);
    }

    public function mul(string $leftOperand, string $rightOperand): string
    {
        return bcmul($leftOperand, $rightOperand, $this->scale);
    }

    public function sub(string $leftOperand, string $rightOperand): string
    {
        return bcsub($leftOperand, $rightOperand, $this->scale);
    }

    public function add(string $leftOperand, string $rightOperand): string
    {
        return bcadd($leftOperand, $rightOperand, $this->scale);
    }

    public function calculatePercentage(string $number, string $percentage): string
    {
        return $this->ceil(bcmul(bcdiv($percentage, '100', 5), $number, $this->scale + 1));
    }

    public function ceil(string $number): string
    {
        $rightOperand = $this->scale > 0 ? '0.'.str_repeat('0', $this->scale - 1).'1' : '1';

        if (str_contains($number, '.') && $number[-1] !== '0') {
            return $this->add($number, $rightOperand);
        }

        return $this->add($number, '0');
    }

    public function max(string $first, string $second): string
    {
        if (bccomp($first, $second) > 0) {
            return $first;
        }

        return $second;
    }

    public function nonNegativeSubtraction(string $minuend, string $subtrahend): string
    {
        $difference = $this->sub($minuend, $subtrahend);

        return $this->max($difference, '0');
    }
}

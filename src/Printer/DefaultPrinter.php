<?php

namespace Adetxt\LaravelFillableGenerator\Printer;

use Nette\PhpGenerator\Printer;

class DefaultPrinter extends Printer
{
    public string $indentation = "    ";
    public int $linesBetweenProperties = 1;
    public int $linesBetweenMethods = 1;
}

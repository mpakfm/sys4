<?php

namespace App;

use Mpakfm\Printu;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;
}

Printu::setPath(__DIR__ . '/../var/log');

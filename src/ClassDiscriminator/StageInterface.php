<?php

namespace Er1z\MultiApiPlatform\ClassDiscriminator;


interface StageInterface
{

    public function isClassAvailable(string $class): bool;

}
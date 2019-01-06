<?php
namespace Er1z\MultiApiPlatform\ClassDiscriminator;


class CacheWarmupStage implements StageInterface
{

    private $isActive = false;

    public function isClassAvailable(string $class): bool
    {
        if(!$this->isActive){
            return false;
        }

        return true;
    }

    public function activate(bool $active)
    {
        $this->isActive = $active;
    }

}
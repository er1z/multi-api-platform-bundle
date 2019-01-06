<?php
namespace Er1z\MultiApiPlatform\ClassDiscriminator;


use Er1z\MultiApiPlatform\ClassDiscriminator;

class CliStage implements StageInterface
{
    /**
     * @var ClassDiscriminator
     */
    private $classDiscriminator;

    /**
     * @var string|null
     */
    private $api = null;
    /**
     * @var array
     */
    private $apis;

    public function __construct(
        ClassDiscriminator $classDiscriminator,
        array $apis
    )
    {
        $this->classDiscriminator = $classDiscriminator;
        $this->apis = $apis;
    }


    public function isClassAvailable(string $class): bool
    {
        if(!$this->api){
            return false;
        }

        return $this->classDiscriminator->classBelongsToApi($class, $this->api);
    }

    public function activate(string $api)
    {
        if(!isset($this->apis[$api])){
            throw new \InvalidArgumentException(
                sprintf('API "%s" does not exist', $api)
            );
        }

        $this->api = $api;
    }
}
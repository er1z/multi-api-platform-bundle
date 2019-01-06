<?php


namespace Er1z\MultiApiPlatform;


use Er1z\MultiApiPlatform\ClassDiscriminator\StageInterface;

class ClassDiscriminator
{
    /**
     * @var array
     */
    private $apis;
    /**
     * @var StageInterface[]
     */
    private $stages;

    /**
     * ClassDiscriminator constructor.
     * @param array $apis
     * @param StageInterface[] $stages
     */
    public function __construct(
        array $apis,
        iterable $stages
    )
    {
        $this->apis = $apis;
        $this->stages = $stages;
    }

    public function classBelongsToApi(string $class, string $api){

        $apiData = $this->apis[$api];

        if(!empty($apiData['namespace'])){
            if(substr($class, 0, strlen($apiData['namespace'])) == $apiData['namespace']){
                return true;
            }
        }

        if(!empty($apiData['implements'])){
            if(array_key_exists($apiData['implements'], class_implements($class))){
                return true;
            }
        }

        return false;
    }

    public function isClassAvailable(string $class){

        foreach($this->stages as $stage){
            if($stage->isClassAvailable($class)){
                return true;
            }
        }

        return false;

    }

}

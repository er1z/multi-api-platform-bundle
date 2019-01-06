<?php


namespace Er1z\MultiApiPlatform;


use ApiPlatform\Core\Bridge\Symfony\Routing\ApiLoader;
use Er1z\MultiApiPlatform\ClassDiscriminator\CacheWarmupStage;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\RouteCollection;

class RouteLoader implements \Symfony\Component\Config\Loader\LoaderInterface
{

    /**
     * @var ApiLoader
     */
    private $decorated;
    /**
     * @var array
     */
    private $apis;
    /**
     * @var bool
     */
    private $isDebug;
    /**
     * @var ClassDiscriminator
     */
    private $classDiscriminator;
    /**
     * @var CacheWarmupStage
     */
    private $stage;

    public function __construct(
        ApiLoader $decorated,
        ClassDiscriminator $executionContext,
        array $apis,
        bool $isDebug,
        CacheWarmupStage $stage
    )
    {
        $this->decorated = $decorated;
        $this->apis = $apis;
        $this->isDebug = $isDebug;
        $this->classDiscriminator = $executionContext;
        $this->stage = $stage;
    }

    /**
     * Loads a resource.
     *
     * @param mixed       $resource The resource
     * @param string|null $type     The resource type or null if unknown
     *
     * @return \Symfony\Component\Routing\RouteCollection
     * @throws \Exception If something went wrong
     */
    public function load($resource, $type = null)
    {
        $this->stage->activate(true);

        /**
         * @var $result RouteCollection
         */
        $result = $this->decorated->load($resource, $type);

        $iterator = $result->getIterator();
        foreach($iterator as $r){
            $class = $r->getDefault('_api_resource_class');

            if(!$class){
                continue;
            }

            $conditions = $this->getConditionsByClass($class);

            if(!$conditions){
                continue;
            }

            $r->setCondition($conditions);

        }

        return $result;
    }

    private function getConditionsByClass($class){

        foreach($this->apis as $api=>$data){
            if($this->classDiscriminator->classBelongsToApi($class, $api)){
                if($this->isDebug){
                    return sprintf('%s || request.attributes.get("_multi_api_platform_debug_api") == "%s"', $data['debug_conditions'], $api);
                }
                return $data['conditions'];
            }
        }

        return null;
    }

    /**
     * Returns whether this class supports the given resource.
     *
     * @param mixed       $resource A resource
     * @param string|null $type     The resource type or null if unknown
     *
     * @return bool True if this class supports the given resource, false otherwise
     */
    public function supports($resource, $type = null)
    {
        return $this->decorated->supports($resource, $type);
    }

    /**
     * Gets the loader resolver.
     *
     * @return LoaderResolverInterface A LoaderResolverInterface instance
     */
    public function getResolver()
    {
        return $this->decorated->getResolver();
    }

    /**
     * Sets the loader resolver.
     */
    public function setResolver(LoaderResolverInterface $resolver)
    {
        $this->decorated->setResolver($resolver);
    }
}

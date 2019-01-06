<?php
namespace Er1z\MultiApiPlatform\ApiPlatform;


use ApiPlatform\Core\Metadata\Resource\Factory\ResourceNameCollectionFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\ResourceNameCollection;
use Er1z\MultiApiPlatform\ClassDiscriminator;

class ResourceNameCollectionFactoryDecorator implements ResourceNameCollectionFactoryInterface
{
    /**
     * @var ResourceNameCollectionFactoryInterface
     */
    private $decorated;
    /**
     * @var ClassDiscriminator
     */
    private $executionContext;

    public function __construct(
        ResourceNameCollectionFactoryInterface $decorated,
        ClassDiscriminator $executionContext
    )
    {
        $this->decorated = $decorated;
        $this->executionContext = $executionContext;
    }


    /**
     * Creates the resource name collection.
     */
    public function create(): ResourceNameCollection
    {
        $result = $this->decorated->create();
        $classes = iterator_to_array($result->getIterator());

        $classes = array_filter($classes, [$this->executionContext, 'isClassAvailable']);

        return new ResourceNameCollection($classes);
    }
}
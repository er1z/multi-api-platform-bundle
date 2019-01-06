<?php
namespace Er1z\MultiApiPlatform\EventSubscriber;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class DebugRequestSnifferListener implements EventSubscriberInterface
{
    /**
     * @var array
     */
    private $configuration;

    /**
     * @var string|null
     */
    private $setCookie = null;

    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
    }


    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                'adjustDebugApi', 33
            ],
            KernelEvents::RESPONSE=>[
                'setCookie', 0
            ]
        ];
    }

    private function determineApiName(Request $request){
        $key = $this->configuration['request_param'];

        foreach($this->configuration['request_order'] as $k){
            if($request->$k->has($key)){
                return $request->$k->get($key);
            }
        }

        return null;
    }

    public function adjustDebugApi(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $api = $this->determineApiName($request);

        if(!$api){
            return;
        }

        $request->attributes->set('_multi_api_platform_debug_api', $api);

        if($this->configuration['set_cookie']){
            $this->setCookie = $api;
        }
    }

    public function setCookie(FilterResponseEvent $event)
    {
        if(!$this->setCookie){
            return;
        }

        $event->getResponse()->headers->setCookie(
            new Cookie($this->configuration['request_param'], $this->setCookie)
        );
    }
}
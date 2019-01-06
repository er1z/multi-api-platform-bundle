<?php
/**
 * Created by PhpStorm.
 * User: eRIZ
 * Date: 05.01.2019
 * Time: 12:54
 */

namespace Er1z\MultiApiPlatform\ClassDiscriminator;


use Er1z\MultiApiPlatform\ClassDiscriminator;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RequestContext;

class RequestStage implements StageInterface
{

    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var ClassDiscriminator
     */
    private $executionContext;
    /**
     * @var array
     */
    private $apis;
    /**
     * @var ExpressionLanguage
     */
    private $expressionLanguage;
    /**
     * @var bool
     */
    private $isDebug;

    public function __construct(
        array $apis,
        RequestStack $requestStack,
        ClassDiscriminator $executionContext,
        ExpressionLanguage $expressionLanguage,
        bool $isDebug
    )
    {
        $this->requestStack = $requestStack;
        $this->executionContext = $executionContext;
        $this->apis = $apis;
        $this->expressionLanguage = $expressionLanguage;
        $this->isDebug = $isDebug;
    }

    private function determineApiFromRequest(Request $request){

        if($this->isDebug && $request->attributes->has('_multi_api_platform_debug_api')){
            return $request->attributes->get('_multi_api_platform_debug_api');
        }

        $requestContext = new RequestContext();
        $requestContext->fromRequest($request);

        $result = [];

        foreach($this->apis as $k=>$v){
            if(!$this->isDebug && $this->expressionLanguage->evaluate($v['conditions'], ['context' => $requestContext, 'request' => $request])) {
                return $k;
            }
            if($this->isDebug && $this->expressionLanguage->evaluate($v['debug_conditions'], ['context' => $requestContext, 'request' => $request])) {
                return $k;
            }
        }

        return $result;
    }


    public function isClassAvailable(string $class): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        if(!$request){
            return false;
        }

        $api = $this->determineApiFromRequest($request);
        if(empty($api)){
            return false;
        }

        return $this->executionContext->classBelongsToApi($class, $api);
    }
}
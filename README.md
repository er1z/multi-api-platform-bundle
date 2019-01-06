Multi-API-Platform
--

This Symfony's bundle is a solution to problem for having more than one API in single application. For example, you have
an application's back-end that exposes resources for end-clients and you want to create an entrypoint for internal
microservices.

Installation
-

Issue
```
composer require er1z/multi-api-platform
```

Configuration
-

First, separate your structures. They have to be distinguished via namespace or implemented interface. Suppose we create
two APIs: internal and external.

Usually I rely on DTOs (but it should work also for entities). Create two classes:
- `App\DTO\Internal\MyInternalStruct`
- `App\DTO\External\MyExternalStruct`

Then create `config/packages/multi_api_platform.yml` with following contents:
```
multi_api_platform:
  apis:
    internal:
      namespace: App\DTO\Internal
      #implements: App\My\MyInterface
      conditions: "request.query.has('is_internal')"
      debug_conditions: "request.query.has('is_internal')"
    external:
      namespace: App\DTO\External
      conditions: "true"
      debug_conditions: "true"
```

Either `namespace` or `implements` should be configured.

You must configure `conditions` for each API manually. This is an [expression](https://symfony.com/doc/current/components/expression_language.html)
that restricts API access via HTTP using dynamic [route conditions](https://symfony.com/doc/current/routing/conditions.html). 

Conditions are not pre-defined in order to obligate you exposing particular API carefully. If you want to expose an API
as is, put `"true"` as a value.

`debug_conditions` are evaluated in `dev` environment whereas `conditions` only in production.

Usage
-

Conditions are simple but powerful tool to restrict particular API, for example by origin IP, header, environment
variable and so on. Check the [route conditions documentation](https://symfony.com/doc/current/routing/conditions.html)
how to construct this stuff.

To extend this for your needs you can always create [`kernel.request`](https://symfony.com/doc/current/reference/events.html#kernel-request)
event listener/subscriber and append some attributes to check against with expression. Be aware to configure your
listener with priority higher than `32` because Symfony's router lives there. Executing your listener after would
produce useless results.  

### Swagger dump
`bin/console swagger:api:export` is overrided — requires a single argument with desired API name to produce correct dump.

### Debug mode
If you are in development environment, it's useful to specify some conditions or use special request attribute
to specify an API you want work with. It's enabled by default provided you're on development environment.

Just add `x-api-select` variable to one of [request parameters](https://symfony.com/doc/current/components/http_foundation.html#request)
(except `files`) with specified API name and you're done. It will produce a cookie named with the same name that will
allow to work with Swagger debugger.

Of course, it may be easily tweaked or disabled:

```
multi_api_platform:
  debug_http_listener:
    enabled: true
    request_param: x-api-select
    set_cookie: true
    request_order: ['request', 'query', 'attributes', 'cookies', 'headers', 'server']
```  

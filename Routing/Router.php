<?php

namespace Hoa\RouterBundle\Routing;

use Hoa\Router as HoaRouter;
use Hoa\Router\Http\Http as HttpRouter;
use Hoa\Router\Exception as HoaException;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Routing\Exception as SymfonyException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\RouteCollection;

class Router implements RouterInterface
{
    /**
     * @var HttpRouter $httpRouter
     */
    private $httpRouter;

    /**
     * @var RequestContext $requestContext
     */
    private $requestContext;

    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * @var RouteCollection
     */
    private $collection;

    /**
     * @var mixed
     */
    protected $resource;

    public function __construct(HttpRouter $httpRouter, LoaderInterface $loader, $resource, $logger)
    {
        $this->httpRouter     = $httpRouter;
        $this->loader         = $loader;
        $this->resource       = $resource;
        $this->logger = $logger;
        $this->requestContext = new RequestContext();
    }

    /**
     * @inheritDoc
     */
    public function match($pathinfo)
    {
        $this->loadRoutes();

        try {
            $this->httpRouter->route($pathinfo); // the returned data isn't right
        } catch (HoaException\NotFound $e) {
            throw new SymfonyException\ResourceNotFoundException($e->getMessage(), $e->getCode(), $e);
        }

        $route = $this->httpRouter->getTheRule();

        return array_merge([
            '_route'      => $route[HoaRouter::RULE_ID],
            '_controller' => $route[HoaRouter::RULE_CALL],
        ], $route[HoaRouter::RULE_VARIABLES]);
    }

    /**
     * @inheritDoc
     */
    public function generate($name, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        $this->loadRoutes();

        if (!$this->httpRouter->ruleExists($name)) {
            throw new SymfonyException\RouteNotFoundException(sprintf('Route "%s" does not exist', $name));
        }

        // @fixme
        return $this->httpRouter->unroute($name, $parameters, false, '/app_dev.php'); // handle $referenceType
    }

    /**
     * @inheritDoc
     */
    public function getRouteCollection()
    {
        $this->loadRoutes();

        return $this->collection;
    }

    /**
     * @inheritDoc
     */
    public function setContext(RequestContext $context)
    {
        $this->requestContext = $context;
    }

    /**
     * @inheritDoc
     */
    public function getContext()
    {
        return $this->requestContext;
    }

    private function loadRoutes()
    {
        if ($this->collection !== null) {
            return;
        }

        $resourceType     = $this->httpRouter->getParameters()->getParameter('resource_type');
        $this->collection = $this->loader->load($this->resource, $resourceType);

        foreach ($this->collection as $name => $route) {
            // @todo: this should be done at load-time
            $path = $this->translatePath($route->getPath());
            $route->setPath($path);

            $defaults   = $route->getDefaults();
            $controller = $defaults['_controller'];

            unset($defaults['_controller']);

            $this->httpRouter->addRule(
                $name,
                $route->getMethods() ?: ['GET'],
                $route->getPath(),
                $controller,
                $able = null,
                $defaults
            );
        }
    }

    /**
     * Converts routes patterns from Symfony to Hoa
     *
     * @param string $path The Symfony route pattern.
     *
     * @return string The same pattern but understandable by Hoa\Router.
     */
    private function translatePath($path)
    {
        return preg_replace('`{([^}]+)}`', '(?<$1>\w+)', $path);
    }
}

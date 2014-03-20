<?php
/**
 * User: dongww
 * Date: 14-3-20
 * Time: 下午2:45
 */

namespace DFramework\HttpKernel;

use Symfony\Component\HttpKernel\HttpKernel as baseKernel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\EventListener\ResponseListener;

class HttpKernel implements HttpKernelInterface, TerminableInterface
{
    protected $kernel;

    public function __construct()
    {
        $routes = new RouteCollection();
        $routes->add('index', new Route('/', array(
            '_controller' => 'Controller\\IndexController::indexAction',
        )));

        $context = new RequestContext();

        $matcher = new UrlMatcher($routes, $context);
        $resolver = new ControllerResolver();

        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber(new RouterListener($matcher));
        $dispatcher->addSubscriber(new ResponseListener('UTF-8'));
        $this->kernel = new baseKernel($dispatcher, $resolver);
    }

    /**
     * @param \Symfony\Component\HttpKernel\HttpKernel $kernel
     */
    public function setKernel($kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @return \Symfony\Component\HttpKernel\HttpKernel
     */
    public function getKernel()
    {
        return $this->kernel;
    }

    /**
     * Handles a Request to convert it to a Response.
     *
     * When $catch is true, the implementation must catch all exceptions
     * and do its best to convert them to a Response instance.
     *
     * @param Request $request A Request instance
     * @param integer $type The type of the request
     *                          (one of HttpKernelInterface::MASTER_REQUEST or HttpKernelInterface::SUB_REQUEST)
     * @param Boolean $catch Whether to catch exceptions or not
     *
     * @return Response A Response instance
     *
     * @throws \Exception When an Exception occurs during processing
     *
     * @api
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        $response = $this->getKernel()->handle($request, $type, $catch);
        return $response;
    }

    /**
     * Terminates a request/response cycle.
     *
     * Should be called after sending the response and before shutting down the kernel.
     *
     * @param Request $request A Request instance
     * @param Response $response A Response instance
     *
     * @api
     */
    public function terminate(Request $request, Response $response)
    {
        $this->getKernel()->terminate($request, $response);
    }

    public function run(Request $request = null)
    {
        if (null === $request) {
            $request = Request::createFromGlobals();
        }

        $response = $this->handle($request);
        $response->send();
        $this->terminate($request, $response);
    }
}
 
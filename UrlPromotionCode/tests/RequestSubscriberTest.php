<?php declare(strict_types=1);

namespace UrlPromotionCode\Tests;

use PHPUnit\Framework\TestCase;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use UrlPromotionCode\Subscriber\RequestSubscriber;

class RequestSubscriberTest extends TestCase
{
    private $systemConfigService;
    private $requestSubscriber;

    protected function setUp(): void
    {
        $this->systemConfigService = $this->createMock(SystemConfigService::class);
        $this->requestSubscriber = new RequestSubscriber($this->systemConfigService);
    }

    public function testOnKernelRequestWithPromotionCode(): void
    {
        // Configure system config to return true for active status
        $this->systemConfigService->method('getBool')
            ->with('UrlPromotionCode.config.active')
            ->willReturn(true);

        // Create request with promotion code
        $request = new Request();
        $request->query->set('Promotioncode', 'TEST123');

        // Create request event
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new RequestEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST);

        // Execute the subscriber
        $this->requestSubscriber->onKernelRequest($event);

        // Verify the attribute was set
        $this->assertTrue($request->attributes->has('set_promotion_cookie'));
        $this->assertEquals('TEST123', $request->attributes->get('set_promotion_cookie'));
    }

    public function testOnKernelRequestWithoutPromotionCode(): void
    {
        // Configure system config to return true for active status
        $this->systemConfigService->method('getBool')
            ->with('UrlPromotionCode.config.active')
            ->willReturn(true);

        // Create request without promotion code
        $request = new Request();

        // Create request event
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new RequestEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST);

        // Execute the subscriber
        $this->requestSubscriber->onKernelRequest($event);

        // Verify the attribute was not set
        $this->assertFalse($request->attributes->has('set_promotion_cookie'));
    }

    public function testOnKernelRequestWithInactivePlugin(): void
    {
        // Configure system config to return false for active status
        $this->systemConfigService->method('getBool')
            ->with('UrlPromotionCode.config.active')
            ->willReturn(false);

        // Create request with promotion code
        $request = new Request();
        $request->query->set('Promotioncode', 'TEST123');

        // Create request event
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new RequestEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST);

        // Execute the subscriber
        $this->requestSubscriber->onKernelRequest($event);

        // Verify the attribute was not set because the plugin is inactive
        $this->assertFalse($request->attributes->has('set_promotion_cookie'));
    }

    public function testOnKernelResponse(): void
    {
        // Configure system config to return true for active status
        $this->systemConfigService->method('getBool')
            ->with('UrlPromotionCode.config.active')
            ->willReturn(true);

        // Create request with promotion code attribute
        $request = new Request();
        $request->attributes->set('set_promotion_cookie', 'TEST123');

        // Create response
        $response = new Response();

        // Create response event
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new ResponseEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $response);

        // Execute the subscriber
        $this->requestSubscriber->onKernelResponse($event);

        // Verify a cookie was set
        $cookies = $response->headers->getCookies();
        $this->assertCount(1, $cookies);
        $this->assertEquals('url_promotion_code', $cookies[0]->getName());
        $this->assertEquals('TEST123', $cookies[0]->getValue());
    }
}
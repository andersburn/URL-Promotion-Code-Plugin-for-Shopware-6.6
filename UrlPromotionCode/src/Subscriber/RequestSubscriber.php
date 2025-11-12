<?php declare(strict_types=1);

namespace UrlPromotionCode\Subscriber;

use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Event\StorefrontRenderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestSubscriber implements EventSubscriberInterface
{
    public const COOKIE_NAME = 'url_promotion_code';
    public const QUERY_PARAM_NAME = 'Promotioncode';
    private const COOKIE_LIFETIME = 60 * 60 * 24 * 30; // 30 days

    public function __construct(
        private readonly SystemConfigService $systemConfigService
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 10],
            KernelEvents::RESPONSE => ['onKernelResponse', 10],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest() || !$this->isPluginActive()) {
            return;
        }

        $request = $event->getRequest();
        
        // Store the promotion code from query parameter
        if ($request->query->has(self::QUERY_PARAM_NAME)) {
            $code = $request->query->get(self::QUERY_PARAM_NAME);
            
            if (!empty($code) && is_string($code)) {
                // Save the code to be set as a cookie in the response
                $request->attributes->set('set_promotion_cookie', $code);
            }
        }
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest() || !$this->isPluginActive()) {
            return;
        }

        $request = $event->getRequest();
        $response = $event->getResponse();
        
        // Set the promotion code cookie if requested
        if ($request->attributes->has('set_promotion_cookie')) {
            $code = $request->attributes->get('set_promotion_cookie');
            
            $cookie = new Cookie(
                name: self::COOKIE_NAME,
                value: $code,
                expire: time() + self::COOKIE_LIFETIME,
                path: '/',
                domain: null,
                secure: $request->isSecure(),
                httpOnly: false, // Allow JS to read the cookie
                raw: false,
                sameSite: Cookie::SAMESITE_LAX
            );
            
            $response->headers->setCookie($cookie);
        }
    }

    private function isPluginActive(): bool
    {
        return $this->systemConfigService->getBool('UrlPromotionCode.config.active');
    }
}
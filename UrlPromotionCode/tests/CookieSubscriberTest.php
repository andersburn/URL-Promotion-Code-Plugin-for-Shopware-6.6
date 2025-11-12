<?php declare(strict_types=1);

namespace UrlPromotionCode\Tests;

use PHPUnit\Framework\TestCase;
use Shopware\Storefront\Event\Cookie\CollectCookieEvent;
use UrlPromotionCode\Subscriber\CookieSubscriber;
use UrlPromotionCode\Subscriber\RequestSubscriber;

class CookieSubscriberTest extends TestCase
{
    private $cookieSubscriber;

    protected function setUp(): void
    {
        $this->cookieSubscriber = new CookieSubscriber();
    }

    public function testAddCookieConfiguration(): void
    {
        $cookies = [];
        $collectCookieEvent = new CollectCookieEvent($cookies);

        $this->cookieSubscriber->addCookieConfiguration($collectCookieEvent);

        $cookies = $collectCookieEvent->getCookies();
        
        $this->assertArrayHasKey(RequestSubscriber::COOKIE_NAME, $cookies);
        $this->assertEquals('functional', $cookies[RequestSubscriber::COOKIE_NAME]['group']);
        $this->assertEquals('url_promotion_code.cookie.description', $cookies[RequestSubscriber::COOKIE_NAME]['snippet_description']);
    }
}
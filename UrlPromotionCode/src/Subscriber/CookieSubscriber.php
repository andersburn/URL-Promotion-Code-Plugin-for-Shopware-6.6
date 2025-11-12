<?php declare(strict_types=1);

namespace UrlPromotionCode\Subscriber;

use Shopware\Storefront\Event\Cookie\CollectCookieEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CookieSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            CollectCookieEvent::class => 'addCookieConfiguration',
        ];
    }

    public function addCookieConfiguration(CollectCookieEvent $event): void
    {
        $event->addCookie(
            RequestSubscriber::COOKIE_NAME,
            [
                'snippet_name' => 'url_promotion_code.cookie.name',
                'snippet_description' => 'url_promotion_code.cookie.description',
                'value' => '',
                'expiration' => '30',
                'isRequired' => false,
                'isGroupRequired' => false,
                'group' => 'functional',
            ]
        );
    }
}
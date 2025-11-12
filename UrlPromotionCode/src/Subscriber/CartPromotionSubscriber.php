<?php declare(strict_types=1);

namespace UrlPromotionCode\Subscriber;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartEvent;
use Shopware\Core\Checkout\Cart\Event\CartChangedEvent;
use Shopware\Core\Checkout\Cart\Event\CartLoadedEvent;
use Shopware\Core\Checkout\Cart\Event\LineItemAddedEvent;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\LineItem\LineItemCollection;
use Shopware\Core\Checkout\Promotion\Cart\PromotionItemBuilder;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CartPromotionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly PromotionItemBuilder $promotionItemBuilder,
        private readonly SystemConfigService $systemConfigService,
        private readonly RequestStack $requestStack
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CartLoadedEvent::class => ['onCartLoaded', 999],
            CartChangedEvent::class => ['onCartChanged', 999],
            LineItemAddedEvent::class => ['onLineItemAdded', 999],
        ];
    }

    public function onCartLoaded(CartLoadedEvent $event): void
    {
        if (!$this->isPluginActive() || $this->isCheckoutConfirmPath()) {
            return;
        }

        $this->processCart($event->getCart());
    }

    public function onCartChanged(CartChangedEvent $event): void
    {
        if (!$this->isPluginActive() || $this->isCheckoutConfirmPath()) {
            return;
        }

        $this->processCart($event->getCart());
    }

    public function onLineItemAdded(LineItemAddedEvent $event): void
    {
        if (!$this->isPluginActive() || $this->isCheckoutConfirmPath()) {
            return;
        }

        $this->processCart($event->getCart());
    }

    private function processCart(Cart $cart): void
    {
        // Only process if we have products in the cart
        if ($cart->getLineItems()->count() === 0) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();
        if ($request === null) {
            return;
        }

        // Check if we have a promotion code in cookie
        $promotionCode = $request->cookies->get(RequestSubscriber::COOKIE_NAME);
        if (empty($promotionCode)) {
            return;
        }

        // Check if this promotion code is already in the cart
        if ($this->hasPromotionCode($cart->getLineItems(), $promotionCode)) {
            return;
        }

        // Add promotion to cart
        $lineItem = $this->promotionItemBuilder->buildPlaceholderItem($promotionCode);
        $cart->add($lineItem);
    }

    private function hasPromotionCode(LineItemCollection $lineItems, string $code): bool
    {
        // Check all promotion line items
        foreach ($lineItems->filterType(LineItem::PROMOTION_LINE_ITEM_TYPE) as $lineItem) {
            if ($lineItem->getReferencedId() === $code) {
                return true;
            }
        }

        return false;
    }

    private function isPluginActive(): bool
    {
        return $this->systemConfigService->getBool('UrlPromotionCode.config.active');
    }

    private function isCheckoutConfirmPath(): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request === null) {
            return false;
        }

        $path = $request->getPathInfo();
        return strpos($path, '/checkout/confirm') === 0;
    }
}
<?php declare(strict_types=1);

namespace UrlPromotionCode\Tests;

use PHPUnit\Framework\TestCase;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\Event\CartLoadedEvent;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\LineItem\LineItemCollection;
use Shopware\Core\Checkout\Promotion\Cart\PromotionItemBuilder;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use UrlPromotionCode\Subscriber\CartPromotionSubscriber;
use UrlPromotionCode\Subscriber\RequestSubscriber;

class CartPromotionSubscriberTest extends TestCase
{
    private $promotionItemBuilder;
    private $systemConfigService;
    private $requestStack;
    private $cartPromotionSubscriber;

    protected function setUp(): void
    {
        $this->promotionItemBuilder = $this->createMock(PromotionItemBuilder::class);
        $this->systemConfigService = $this->createMock(SystemConfigService::class);
        $this->requestStack = $this->createMock(RequestStack::class);
        
        $this->cartPromotionSubscriber = new CartPromotionSubscriber(
            $this->promotionItemBuilder,
            $this->systemConfigService,
            $this->requestStack
        );
    }

    public function testOnCartLoadedWithPromotionCode(): void
    {
        // Configure system config to return true for active status
        $this->systemConfigService->method('getBool')
            ->with('UrlPromotionCode.config.active')
            ->willReturn(true);

        // Create request with promotion code cookie
        $request = new Request();
        $request->cookies->set(RequestSubscriber::COOKIE_NAME, 'TEST123');
        
        // Configure request stack to return our request
        $this->requestStack->method('getCurrentRequest')
            ->willReturn($request);

        // Create a cart with some products but no promotion
        $productLineItem = new LineItem('product-1', LineItem::PRODUCT_LINE_ITEM_TYPE);
        $lineItems = new LineItemCollection([$productLineItem]);
        $cart = new Cart('test-cart');
        $cart->setLineItems($lineItems);

        // Create promotion line item and configure builder to return it
        $promotionLineItem = new LineItem('test-promotion', LineItem::PROMOTION_LINE_ITEM_TYPE);
        $promotionLineItem->setReferencedId('TEST123');
        
        $this->promotionItemBuilder->method('buildPlaceholderItem')
            ->with('TEST123')
            ->willReturn($promotionLineItem);

        // Create cart loaded event
        $event = new CartLoadedEvent($cart, 'sales-channel');

        // Execute the subscriber
        $this->cartPromotionSubscriber->onCartLoaded($event);

        // Verify the promotion was added to the cart
        $this->assertTrue($cart->has($promotionLineItem->getId()));
    }

    public function testOnCartLoadedWithExistingPromotionCode(): void
    {
        // Configure system config to return true for active status
        $this->systemConfigService->method('getBool')
            ->with('UrlPromotionCode.config.active')
            ->willReturn(true);

        // Create request with promotion code cookie
        $request = new Request();
        $request->cookies->set(RequestSubscriber::COOKIE_NAME, 'TEST123');
        
        // Configure request stack to return our request
        $this->requestStack->method('getCurrentRequest')
            ->willReturn($request);

        // Create a product line item
        $productLineItem = new LineItem('product-1', LineItem::PRODUCT_LINE_ITEM_TYPE);
        
        // Create a promotion line item with the same code
        $promotionLineItem = new LineItem('test-promotion', LineItem::PROMOTION_LINE_ITEM_TYPE);
        $promotionLineItem->setReferencedId('TEST123');
        
        // Create a cart with the product and promotion
        $lineItems = new LineItemCollection([$productLineItem, $promotionLineItem]);
        $cart = new Cart('test-cart');
        $cart->setLineItems($lineItems);

        // Create cart loaded event
        $event = new CartLoadedEvent($cart, 'sales-channel');

        // Initial line items count
        $initialCount = $cart->getLineItems()->count();

        // Execute the subscriber
        $this->cartPromotionSubscriber->onCartLoaded($event);

        // Verify no additional promotion was added
        $this->assertEquals($initialCount, $cart->getLineItems()->count());
    }

    public function testOnCartLoadedWithEmptyCart(): void
    {
        // Configure system config to return true for active status
        $this->systemConfigService->method('getBool')
            ->with('UrlPromotionCode.config.active')
            ->willReturn(true);

        // Create request with promotion code cookie
        $request = new Request();
        $request->cookies->set(RequestSubscriber::COOKIE_NAME, 'TEST123');
        
        // Configure request stack to return our request
        $this->requestStack->method('getCurrentRequest')
            ->willReturn($request);

        // Create an empty cart
        $cart = new Cart('test-cart');
        $cart->setLineItems(new LineItemCollection());

        // Create cart loaded event
        $event = new CartLoadedEvent($cart, 'sales-channel');

        // Execute the subscriber
        $this->cartPromotionSubscriber->onCartLoaded($event);

        // Verify the cart is still empty - no promotion added to empty cart
        $this->assertEquals(0, $cart->getLineItems()->count());
    }
}
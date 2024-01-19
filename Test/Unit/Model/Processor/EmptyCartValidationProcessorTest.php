<?php
declare(strict_types=1);

namespace Ytec\RuleEligibilityCheck\Test\Unit\Model\Processor;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;
use Magento\SalesRule\Model\Rule;
use Magento\Quote\Model\Quote;
use Magento\Customer\Model\Data\Customer;
use PHPUnit\Framework\TestCase;
use Ytec\RuleEligibilityCheck\Model\Processor\EmptyCartValidationProcessor;

class EmptyCartValidationProcessorTest extends TestCase
{
    /**
     * @var EmptyCartValidationProcessor
     */
    private EmptyCartValidationProcessor $processor;

    /**
     * @var StoreManagerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $storeManagerMock;

    protected function setUp(): void
    {
        $this->storeManagerMock = $this->getMockForAbstractClass(StoreManagerInterface::class);
        $this->processor = new EmptyCartValidationProcessor($this->storeManagerMock);
    }

    /**
     * Test when cart is not empty.
     *
     * @throws NoSuchEntityException
     */
    public function testExecuteWhenCartIsNotEmpty(): void
    {
        $ruleMock = $this->createMock(Rule::class);
        $cartMock = $this->createMock(Quote::class);
        $cartMock->expects($this->once())->method('getItemsCount')->willReturn(1);

        $this->assertTrue($this->processor->execute($ruleMock, $cartMock));
    }

    /**
     * Test when cart is empty.
     *
     * @throws NoSuchEntityException
     */
    public function testExecuteWhenCartIsEmpty(): void
    {
        $ruleMock = $this->createMock(Rule::class);
        $cartMock = $this->createMock(Quote::class);
        $customerMock = $this->createMock(Customer::class);
        $storeMock = $this->createMock(Store::class);

        $cartMock->expects($this->once())->method('getItemsCount')->willReturn(0);
        $cartMock->expects($this->once())->method('getCustomer')->willReturn($customerMock);
        $cartMock->expects($this->once())->method('getStoreId')->willReturn(1);

        $customerMock->expects($this->once())->method('getFirstname')->willReturn('Matheus');

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with(1)
            ->willReturn($storeMock);

        $storeMock->expects($this->once())->method('getCode')->willReturn('default');

        $this->assertFalse($this->processor->execute($ruleMock, $cartMock));
        $this->assertEquals(
            new Phrase(EmptyCartValidationProcessor::THE_CART_FOR_CUSTOMER_IS_EMPTY, ['Matheus', 'default']),
            $this->processor->getFailedMessage()
        );
    }
}

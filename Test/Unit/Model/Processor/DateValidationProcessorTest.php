<?php
declare(strict_types=1);

namespace Ytec\RuleEligibilityCheck\Test\Unit\Model\Processor;

use Magento\SalesRule\Model\Rule;
use Magento\Quote\Api\Data\CartInterface;
use PHPUnit\Framework\TestCase;
use Ytec\RuleEligibilityCheck\Model\Processor\DateValidationProcessor;

class DateValidationProcessorTest extends TestCase
{
    /**
     * @var DateValidationProcessor
     */
    private DateValidationProcessor $processor;

    protected function setUp(): void
    {
        $this->processor = new DateValidationProcessor();
    }

    /**
     * @dataProvider executeDataProvider
     *
     * @param string|null $toDate
     * @param bool $expectedResult
     * @param string|null $expectedMessage
     */
    public function testExecute(?string $toDate, bool $expectedResult, ?string $expectedMessage): void
    {
        $ruleMock = $this->getMockBuilder(Rule::class)
            ->disableOriginalConstructor()
            ->addMethods(['getName'])
            ->onlyMethods(['getToDate'])
            ->getMock();

        $cartMock = $this->createMock(CartInterface::class);

        $ruleMock->expects($this->atLeast(1))
            ->method('getToDate')
            ->willReturn($toDate);

        $ruleMock->expects($this->any())
            ->method('getName')
            ->willReturn('Test Rule');

        $actualResult = $this->processor->execute($ruleMock, $cartMock);

        $this->assertSame($expectedResult, $actualResult);

        if ($expectedMessage) {
            $this->assertEquals($expectedMessage, (string) $this->processor->getFailedMessage());
        }
    }

    public function executeDataProvider(): array
    {
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 days'));
        $tomorrow = date('Y-m-d', strtotime('+1 days'));

        return [
            'Rule already expired' => [$yesterday, false, "The rule 'Test Rule' is already expired, it ended $yesterday."],
            'Rule valid today' => [$today, true, null],
            'Rule valid tomorrow' => [$tomorrow, true, null],
            'No "to date" set' => [null, true, null],
        ];
    }
}

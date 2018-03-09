<?php
declare(strict_types = 1);

use PHPUnit\Framework\TestCase;
use PTS\Tools\DeepArray;
use PTS\Tools\RegExpFactory;
use PTS\TypeCast\PropException;
use PTS\TypeCast\TypeCast;

class BoolTest extends TestCase
{
    /** @var TypeCast */
    protected $service;

    public function setUp()
    {
        $this->service = new TypeCast(new DeepArray, new RegExpFactory);
    }

    /**
     * @param array $data
     * @param array $rules
     * @param bool  $expected
     *
     * @dataProvider dataProvider
     *
     * @throws PropException
     */
    public function testCastBool(array $data, array $rules, bool $expected): void
    {
        $data = $this->service->cast($data, $rules);
        self::assertInternalType('bool', $data['isUser']);
        self::assertSame($expected, $data['isUser']);
    }

    /**
     * @throws PropException
     */
    public function testCastBoolExceptionOnString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Can`t cast to boolean');
        $this->service->cast(['isUser' => 'some string'], ['isUser' => ['bool']]);
    }

    /**
     * @throws PropException
     */
    public function testCastBoolExceptionOnArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Can`t cast to boolean');
        $this->service->cast(['isUser' => []], ['isUser' => ['bool']]);
    }

    public function dataProvider(): array
    {
        return [
            'true' => [
                ['isUser' => true],
                ['isUser' => ['bool']],
                true
            ],
            'string true' => [
                ['isUser' => 'true'],
                ['isUser' => ['bool']],
                true
            ],
            'int 1' => [
                ['isUser' => 1],
                ['isUser' => ['bool']],
                true
            ],
            'string 1' => [
                ['isUser' => '1'],
                ['isUser' => ['bool']],
                true
            ],
            'string on' => [
                ['isUser' => 'on'],
                ['isUser' => ['bool']],
                true
            ],
            'string yes' => [
                ['isUser' => 'yes'],
                ['isUser' => ['bool']],
                true
            ],
            'false' => [
                ['isUser' => false],
                ['isUser' => ['bool']],
                false
            ],
            'string false' => [
                ['isUser' => 'false'],
                ['isUser' => ['bool']],
                false
            ],
            'string off' => [
                ['isUser' => 'off'],
                ['isUser' => ['bool']],
                false
            ],
            'string no' => [
                ['isUser' => 'no'],
                ['isUser' => ['bool']],
                false
            ],
        ];
    }
}

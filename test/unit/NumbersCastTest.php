<?php
declare(strict_types = 1);

use PHPUnit\Framework\TestCase;
use PTS\Tools\DeepArray;
use PTS\Tools\RegExpFactory;
use PTS\TypeCast\PropException;
use PTS\TypeCast\TypeCast;

class NumbersCastTest extends TestCase
{
    /** @var TypeCast */
    protected $service;

    public function setUp()
    {
        $this->service = new TypeCast(new DeepArray, new RegExpFactory);
    }

    /**
     * @throws PropException
     *
     * @dataProvider dataProvider
     */
    public function testToNumbersCast($value, $expect, $casterAlieases)
    {
        $body = ['phone' => $value];

        $data = $this->service->cast($body, [
            'phone' => $casterAlieases,
        ]);

        $this->assertEquals($expect, $data['phone']);
    }


    public function dataProvider(): array
    {
        return [
            ['+7(123) 213-1231', '71232131231', ['numbers']],
            ['+7(123) 213-1231', 71232131231, ['numbers', 'int']],
            [71232131231, 71232131231, ['numbers']],
        ];
    }
}

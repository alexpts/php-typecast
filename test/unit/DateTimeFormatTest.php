<?php
declare(strict_types = 1);

use PHPUnit\Framework\TestCase;
use PTS\Tools\DeepArray;
use PTS\Tools\RegExpFactory;
use PTS\TypeCast\PropException;
use PTS\TypeCast\TypeCast;

class DateTimeFormatTest extends TestCase
{
    /** @var TypeCast */
    protected $service;

    public function setUp()
    {
        $this->service = new TypeCast(new DeepArray, new RegExpFactory);
    }

    /**
     * @throws PropException
     * @dataProvider dataProvider
     */
    public function testToDateTimeFormatCast(string $raw, string $expect, string $format)
    {
        $body = ['date' => $raw];

        $data = $this->service->cast($body, [
            'date' => ['datetime', ['datetimeFormat' => [$format]]],
        ]);

        self::assertInternalType('string', $data['date']);
        self::assertEquals($expect, $data['date']);
    }

    public function dataProvider(): array
    {
        return [
            ['12-08-2017 23:23:23.000000', '12-08-2017', 'd-m-Y'],
            ['02-03-1994 03:23:23', '03-02-1994', 'm-d-Y'],
            ['2016-04-05', '04-2016', 'm-Y'],
        ];
    }
}

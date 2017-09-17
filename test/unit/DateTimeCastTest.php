<?php
declare(strict_types = 1);

use PHPUnit\Framework\TestCase;
use PTS\Tools\DeepArray;
use PTS\TypeCast\PropException;
use PTS\TypeCast\TypeCast;

class DateTimeCastTest extends TestCase
{
    /** @var TypeCast */
    protected $service;

    public function setUp()
    {
        $this->service = new TypeCast(new DeepArray);
    }

    /**
     * @throws PropException
     */
    public function testToDateTimeCast()
    {
        $body = ['date' => '12-08-2017'];

        $data = $this->service->cast($body, [
            'date' => ['datetime'],
        ]);

        self::assertInstanceOf(\DateTime::class, $data['date']);
        self::assertEquals(new \DateTime($body['date']), $data['date']);
    }
}

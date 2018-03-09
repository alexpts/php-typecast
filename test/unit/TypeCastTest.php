<?php
declare(strict_types = 1);

use PHPUnit\Framework\TestCase;
use PTS\Tools\DeepArray;
use PTS\Tools\RegExpFactory;
use PTS\TypeCast\PropException;
use PTS\TypeCast\TypeCast;

class TypeCastTest extends TestCase
{
    /** @var TypeCast */
    protected $service;

    public function setUp()
    {
        $this->service = new TypeCast(new DeepArray, new RegExpFactory);
    }

    public function testGetRegExpFactory()
    {
        $this->assertInstanceOf(RegExpFactory::class, $this->service->getRegExpFactory());
    }

    public function testGetRules()
    {
        $types = $this->service->getTypes();
        self::assertGreaterThan(6, count($types));
    }

    public function testRegisterRule()
    {
        $this->service->registerType('someCast2', function() {
            return true;
        });

        $rules = $this->service->getTypes();

        self::assertArrayHasKey('someCast2', $rules);
    }

    /**
     * @throws PropException
     */
    public function testCastInt()
    {
        $data = ['age' => 24, 'badAge' => '24'];
        $castedData = $this->service->cast($data, [
            'age' => ['int'],
            'badAge' => ['int']
        ]);
        self::assertCount(2, $castedData);

        self::assertInternalType('int', $castedData['badAge']);
        self::assertInternalType('int', $castedData['age']);
    }

    /**
     * @throws PropException
     */
    public function testDeepProperty()
    {
        $body = ['user' => [
            'isUser' => false,
            'isGuest' => 'true',
        ]];
        $data = $this->service->cast($body, [
            'user' => ['array'],
            'user.isUser' => ['bool'],
            'user.isGuest' => ['bool'],
        ]);

        self::assertInternalType('array', $data['user']);
        self::assertInternalType('bool', $data['user']['isGuest']);
        self::assertInternalType('bool', $data['user']['isGuest']);
    }

    /**
     * @throws PropException
     */
    public function testCollectionEach()
    {
        $body = ['members' => [1, '2', '3', 4]];

        $data = $this->service->cast($body, [
            'members' => ['array', ['each' => ['int']]],
        ]);

        self::assertInternalType('array', $data['members']);
        self::assertInternalType('int', $data['members'][0]);
        self::assertInternalType('int', $data['members'][1]);
        self::assertInternalType('int', $data['members'][2]);
        self::assertInternalType('int', $data['members'][3]);
    }

    /**
     * @throws PropException
     */
    public function testBadRule()
    {
        $this->expectException(PropException::class);

        $body = ['members' => [1, '2', '3', 4]];
        $this->service->cast($body, [
            'members' => ['badTypeCast'],
        ]);
    }
}

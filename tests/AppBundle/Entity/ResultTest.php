<?php

namespace Tests\AppBundle\Entity;


use AppBundle\Entity\Result;
use AppBundle\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * Class ResultTest
 *
 * @package Tests\AppBundle\Entity
 */
class ResultTest extends TestCase
{
    /**
     * @var User $user
     */
    protected $user;
    /**
     * @var Result $result
     */
    protected $result;
    protected $resultado;
    const USERNAME = 'uSeR ñ¿?Ñ';
    const POINTS = 2017;
    /**
     * @var \DateTime $_time
     */
    private $_time;

    /**
     * Sets up the fixture.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->user = new User();
        $this->user->setUsername(self::USERNAME);
        $this->resultado = random_int(0, 100000);
        $this->_time = new \DateTime('now');
        $this->result = new Result($this->resultado, $this->user,$this->_time );
    }

    /**
     * Implement testConstructor
     *
     * @covers \AppBundle\Entity\Result::__construct()
     * @covers \AppBundle\Entity\Result::getId()
     * @covers \AppBundle\Entity\Result::getResult()
     * @covers \AppBundle\Entity\Result::getUser()
     * @covers \AppBundle\Entity\Result::getTime()
     *
     * @return void
     */
    public function testConstructor()
    {
        $time = new \DateTime('now');
        $this->result = new Result(0,$this->user,$time);
        self::assertEquals(0, $this->result->getId());
        self::assertEmpty($this->result->getResult());
        self::assertNotEmpty($this->result->getUser());
        self::assertEquals($time, $this->result->getTime());
    }


    /**
     * Implement testUsername().
     *
     * @covers \AppBundle\Entity\Result::setResult
     * @covers \AppBundle\Entity\Result::getResult
     * @return void
     */
    public function testResult()
    {
        static::assertNotEmpty($this->result->getResult());
        $resultado = random_int(0, 1000);
        $this->result->setResult($resultado);
        static::assertEquals($resultado, $this->result->getResult());
    }

    /**
     * Implement testUser().
     *
     * @covers \AppBundle\Entity\Result::setUser()
     * @covers \AppBundle\Entity\Result::getUser()
     * @return void
     */
    public function testUser()
    {
        self::assertNotEmpty($this->result->getUser());
        $this->result->setUser($this->user);
        static::assertEquals($this->user, $this->result->getUser());
    }

    /**
     * Implement testTime().
     *
     * @covers \AppBundle\Entity\Result::setTime
     * @covers \AppBundle\Entity\Result::getTime
     * @return void
     */
    public function testTime()
    {
        static::assertNotEmpty($this->result->getTime());
        $fecha = new \DateTime('now');
        $this->result->setTime($fecha);
        static::assertEquals($fecha, $this->result->getTime());
    }

    /**
     * Implement testTo_String().
     *
     * @covers \AppBundle\Entity\Result::__toString
     * @return void
     */
    public function testTo_String()
    {

        $this->result->setResult(random_int(0, 1000));
        $this->result->setTime($this->_time);
        $attributes = get_object_vars($this->result);
        self::assertEmpty($attributes, $this->result->__toString());
    }

    /**
     * Implement testJson_Serialize().
     *
     * @covers \AppBundle\Entity\Result::jsonSerialize
     * @return void
     */
    public function testJson_Serialize()
    {
        $this->result->setResult('result');
        $cadena = json_encode($this->result->jsonSerialize());
        self::assertJson($cadena);
    }
}
<?php
namespace Aivo\Tests\Exceptions;

use Aivo\BaseException;

class BaseExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
    }

    private function newBaseException()
    {
        return new BaseExceptionStub();
    }

    private function loggerMock()
    {
        return $this->getMockBuilder('\Psr\Log\LoggerInterface')
                    ->disableOriginalConstructor()
                    ->setMethods([
                        'error',
                        'critical',
                        'emergency',
                        'alert',
                        'warning',
                        'notice',
                        'info',
                        'debug',
                        'log'
                    ])
                    ->getMock();
    }

    public function test_extends_exception()
    {
        $e = $this->newBaseException();
        $this->assertInstanceOf('\Exception', $e);
    }

    public function test_implements_LoggerAware()
    {
        $e = $this->newBaseException();
        $this->assertInstanceOf('\Psr\Log\LoggerAwareInterface', $e);
    }

    public function test_has_psr3_levels()
    {

        $this->assertEquals(
            \Psr\Log\LogLevel::EMERGENCY,
                BaseException::EMERGENCY);

        $this->assertEquals(
            \Psr\Log\LogLevel::ALERT,
                BaseException::ALERT);

        $this->assertEquals(
            \Psr\Log\LogLevel::CRITICAL,
                BaseException::CRITICAL);

        $this->assertEquals(
            \Psr\Log\LogLevel::ERROR,
                BaseException::ERROR);

        $this->assertEquals(
            \Psr\Log\LogLevel::WARNING,
                BaseException::WARNING);

        $this->assertEquals(
            \Psr\Log\LogLevel::NOTICE,
                BaseException::NOTICE);

        $this->assertEquals(
            \Psr\Log\LogLevel::INFO,
                BaseException::INFO);

        $this->assertEquals(
            \Psr\Log\LogLevel::DEBUG,
                BaseException::DEBUG);
    }

    public function test_logger_mock()
    {
        $mock = $this->loggerMock();
        $this->assertInstanceOf('\Psr\Log\LoggerInterface', $mock);
    }

    public function test_constructing_with_logger()
    {
        $loggerMock = $this->loggerMock();
        $e = new BaseExceptionStub($loggerMock);

    }

    public function test_constructing_with_invalid_logger()
    {
        $invalidLogger = $this->getMockBuilder('\Exception')
                                ->disableOriginalConstructor()
                                ->getMock();

        try {
            $e = new BaseExceptionStub($invalidLogger);
        }
        catch (\Exception $ex) {
            // PHP 5
            $this->assertTrue(true);
            return;
        }
        catch (\TypeError $ex) {
            // PHP 7
            $this->assertTrue(true);
            return;
        }

        $this->assertTrue(false);
    }

    public function test_message()
    {
        $e = new BaseExceptionStub;

        // Default message and getter
        $this->assertEquals('Unknown error.', $e->getMessage());


        $e->setMessage('This is a test.');
        $this->assertEquals('This is a test.', $e->getMessage());
    }

    public function test_code()
    {
        $e = new BaseExceptionStub;

        // Default code and getter
        $this->assertEquals(0, $e->getCode());


        $e->setCode(549);
        $this->assertEquals(549, $e->getCode());
    }

    public function test_class()
    {
        $e = new BaseExceptionStub;

        // Default class and getter
        $this->assertEquals('Aivo\Tests\Exceptions\BaseExceptionStub',
                                $e->getClass());


        $e->setClass('\DatabaseException');
        $this->assertEquals('\DatabaseException', $e->getClass());
    }

    public function test_level()
    {
        $e = new BaseExceptionStub;

        // Default level and getter
        $this->assertEquals(\Psr\Log\LogLevel::CRITICAL, $e->getLevel());


        $e->setLevel(\Psr\Log\LogLevel::EMERGENCY);
        $this->assertEquals(\Psr\Log\LogLevel::EMERGENCY, $e->getLevel());
    }

    public function test_http_code()
    {
        $e = new BaseExceptionStub;

        // Default http code and getter
        $this->assertEquals(500, $e->getHttpCode());


        $e->setHttpCode(409);
        $this->assertEquals(409, $e->getHttpCode());
    }

    public function test_data()
    {
        $e = new BaseExceptionStub;

        // Default data and getter
        $this->assertEquals([], $e->getData());

        $expected = [
            'ErrorCode' => 3817,
            'some' => [
                'more' => 'mixed',
                'data' => true
            ]
        ];

        $e->setData($expected);
        $this->assertEquals($expected, $e->getData());
    }

    public function test_logger()
    {
        $loggerMock = $this->loggerMock();
        $e = new BaseExceptionStub;

        // Default http code and getter
        $e->setLogger($loggerMock);

        $this->assertEquals($loggerMock, $e->getLogger());
    }

    public function test_set_previous()
    {
        $previous = new \Exception('Previous exception');

        $e = new BaseExceptionStub();
        $e->setPrevious($previous);

        $this->assertInstanceOf('\Exception', $previous);
    }

    public function test_method_chaining()
    {
        $loggerMock = $this->loggerMock();

        $e = new BaseExceptionStub();
        $e->setMessage('Message')
            ->setCode(300)
            ->setClass('DatabaseException')
            ->setLevel(\Psr\Log\LogLevel::DEBUG)
            ->setData(["doesn't" => "matter"])
            ->setLogger($loggerMock);

        $this->assertTrue(true);
    }

    public function test_to_array_without_data()
    {
        $e = new BaseExceptionStub();
        $e->setMessage('Test message')
            ->setCode(300)
            ->setClass('DatabaseException');

        $expected = [
            'message' => 'Test message',
            'code' => 300,
            'class' => 'DatabaseException'
        ];

        $this->assertEquals($expected, $e->__toArray());
    }

    public function test_to_array_with_data()
    {
        $e = new BaseExceptionStub();
        $e->setMessage('Test message')
            ->setCode(300)
            ->setClass('DatabaseException')
            ->setData([
                'data' => true,
                'code' => 250
            ]);

        $expected = [
            'message' => 'Test message',
            'code' => 300,
            'class' => 'DatabaseException',
            'data' => [
                'data' => true,
                'code' => 250
            ]
        ];

        $this->assertEquals($expected, $e->__toArray());

    }

    public function test_json_serialize()
    {
        $e = new BaseExceptionStub();
        $e->setMessage('Test message')
            ->setCode(300)
            ->setClass('DatabaseException')
            ->setData([
                'data' => true,
                'code' => 250
            ]);

        // @todo change this so the order of the attributes doesn't matter
        $expected = json_encode([
            'message' => 'Test message',
            'class' => 'DatabaseException',
            'code' => 300,
            'data' => [
                'data' => true,
                'code' => 250
            ]
        ]);

        $this->assertEquals($expected, json_encode($e));
    }

    public function test_previous_logger()
    {
        $loggerMock = $this->loggerMock();

        $loggerMock->expects($this->once())
                    ->method('error')
                    ->with($this->equalTo('Previous'));

        $previous = new \Exception('Previous');
        $e = new BaseExceptionStub($loggerMock, $previous, \Psr\Log\LogLevel::ERROR);
    }
}

class BaseExceptionStub extends BaseException
{
}
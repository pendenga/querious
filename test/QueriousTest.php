<?php

namespace Pendenga\Querious\Test;

use PDO;
use PDOStatement;
use Pendenga\Querious\QueriousException;
use Pendenga\Querious\Querious;
use Pendenga\Querious\Query;
use PHPUnit\Framework\TestCase;

/**
 * Class QueriousTest
 * @package Pendenga\Querious\Test
 */
class QueriousTest extends TestCase
{
    public function testSetQuery()
    {
        $query = $this->getMockBuilder(Query::class)
            ->disableOriginalConstructor()
            ->getMock();

        $querious = new Querious();
        /* @var Query $query */
        $querious->setQuery($query);
        $this->assertTrue(true);
    }

    /**
     * @expectedException \Pendenga\Querious\QueriousException
     * @expectedExceptionMessage Load a valid query first
     */
    public function testErrorNoQueryLoaded()
    {
        /* @var PDO $pdo */
        $pdo = $this->getMockBuilder(PDO::class)
            ->disableOriginalConstructor()
            ->getMock();

        $querious = new Querious();
        $querious->execute($pdo, ['username' => 'unit tester']);
    }


    /**
     * @expectedException \Pendenga\Querious\QueriousException
     * @expectedExceptionMessage Invalid query parameters. Expected account_id
     */
    public function testErrorParametersInvalid()
    {
        $query = $this->getMockBuilder(Query::class)
            ->disableOriginalConstructor()
            ->setMethods(['parameters'])
            ->getMock();
        $query->method('parameters')
            ->will($this->returnValue(['account_id'])); // mismatch with 'username' below

        $pdo = $this->getMockBuilder(PDO::class)
            ->disableOriginalConstructor()
            ->getMock();

        $querious = new Querious();
        /* @var Query $query */
        $querious->setQuery($query);
        /* @var PDO $pdo */
        $querious->execute($pdo, ['username' => 'unit tester']); // mismatch with 'account_id' above
    }

    /**
     * @expectedException \Pendenga\Querious\QueriousException
     * @expectedExceptionMessage Invalid command: wrong
     */
    public function testErrorCommandInvalid() {
        $query = $this->getMockBuilder(Query::class)
            ->disableOriginalConstructor()
            ->setMethods(['command','parameters'])
            ->getMock();
        $query->method('parameters')
            ->will($this->returnValue(['username']));
        $query->method('command')
            ->will($this->returnValue('wrong'));

        $pdo = $this->getMockBuilder(PDO::class)
            ->disableOriginalConstructor()
            ->getMock();

        $querious = new Querious();
        /* @var Query $query */
        $querious->setQuery($query);
        /* @var PDO $pdo */
        $querious->execute($pdo, ['username'=>'unit tester']);
    }

    /**
     * @throws QueriousException
     */
    public function testPdoPrepare()
    {
        $query = $this->getMockBuilder(Query::class)
            ->disableOriginalConstructor()
            ->setMethods(['command','parameters','statement'])
            ->getMock();
        $query->method('parameters')
            ->will($this->returnValue(['username']));
        $query->method('command')
            ->will($this->returnValue('prepare'));
        $query->method('statement')
            ->will($this->returnValue('SELECT something FROM nothing'));

        $stmt = $this->getMockBuilder(PDOStatement::class)
            ->disableOriginalConstructor()
            ->setMethods(['execute'])
            ->getMock();
        $stmt->expects(self::once())
            ->method('execute')
            ->with(['username'=>'unit tester'])
            ->willReturn($stmt);

        $pdo = $this->getMockBuilder(PDO::class)
            ->disableOriginalConstructor()
            ->setMethods(['prepare'])
            ->getMock();
        $pdo->expects(self::once())
            ->method('prepare')
            ->with('SELECT something FROM nothing')
            ->willReturn($stmt);

        $querious = new Querious();
        /* @var Query $query */
        $querious->setQuery($query);
        /* @var PDO $pdo */
        $querious->execute($pdo, ['username'=>'unit tester']);
        $this->assertTrue(true);
    }
}

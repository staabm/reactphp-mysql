<?php

namespace React\Tests;

use React\MySQL\Connection;

class ConnectionTest extends \PHPUnit_Framework_TestCase {

	private $connectOptions = array(
		'dbname' => 'test',
		'user'   => 'test',
		'passwd' => 'test'
	);
	
	public function testConnectWithInvalidPass() {
		$loop = \React\EventLoop\Factory::create();
		$conn = new Connection($loop, array('passwd' => 'invalidpass') + $this->connectOptions );
		
		$conn->connect(function ($err, $conn) use($loop){
			$this->assertEquals("Access denied for user 'test'@'localhost' (using password: YES)", $err->getMessage());
			$this->assertInstanceOf('React\MySQL\Connection', $conn);
			//$loop->stop();
		});
		$loop->run();
	}
	
	
	public function testConnectWithValidPass() {
		$loop = \React\EventLoop\Factory::create();
		$conn = new Connection($loop, $this->connectOptions );
		
		$conn->connect(function ($err, $conn) use($loop){
			$this->assertEquals(null, $err);
			$this->assertInstanceOf('React\MySQL\Connection', $conn);
		});
		
		$conn->ping(function ($err, $conn) use ($loop){
			$this->assertEquals(null, $err);
			$conn->close(function ($conn){
				$this->assertEquals($conn::STATE_CLOSED, $conn->getState());
			});
			//$loop->stop();
		});
		$loop->run();
	}
}

<?php

/**
 * IntegerTest
 *
 * @package    Congow\Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 * @version
 */

namespace test\Integration\ODM\Mapper\DataType;

use test\PHPUnit\TestCase;
use Congow\Orient\ODM\Manager;
use Congow\Orient\ODM\Mapper;
use Congow\Orient\ODM\Mapper\Hydration\Result;
use Congow\Orient\Query;
use Congow\Orient\Foundation\Types\Rid;
use Congow\Orient\Exception\ODM\OClass\NotFound as UnmappedClass;
use Congow\Orient\Query\Command\Select;
use Congow\Orient\Exception;
use Congow\Orient\Contract\Protocol\Adapter as ProtocolAdapter;
use Congow\Orient\ODM\Mapper\ClassMetadata\Factory as ClassMetadataFactory;
use Congow\Orient\Validator\Rid as RidValidator;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory as MetadataFactory;

class IntegerTest extends TestCase
{

	public function setup()
	{
	    $mapper          = new Mapper(__DIR__ . "/../../../../../proxies");
	    $mapper->setDocumentDirectories(array('./test/Integration/Document' => 'test'));
	    $client          = new \Congow\Orient\Http\Client\Curl(false, TEST_ODB_TIMEOUT);
	    $binding         = new \Congow\Orient\Foundation\Binding($client, TEST_ODB_HOST, TEST_ODB_PORT, TEST_ODB_USER, TEST_ODB_PASSWORD, TEST_ODB_DATABASE);
	    $protocolAdapter = new \Congow\Orient\Foundation\Protocol\Adapter\Http($binding);
	    $this->manager   = new Manager($mapper, $protocolAdapter);
	}

	public function testHydrationOfAnIntegerProperty()
	{
	    $mapper          = new Mapper(__DIR__ . "/../../proxies");

	    $mapper->enableMismatchesTolerance();

	    $mapper->setDocumentDirectories(array('./test/Integration/Document' => 'test'));
	    $client          = new \Congow\Orient\Http\Client\Curl(false, TEST_ODB_TIMEOUT);
	    $binding         = new \Congow\Orient\Foundation\Binding($client, TEST_ODB_HOST, TEST_ODB_PORT, TEST_ODB_USER, TEST_ODB_PASSWORD, TEST_ODB_DATABASE);
	    $protocolAdapter = new \Congow\Orient\Foundation\Protocol\Adapter\Http($binding);
	    $this->manager   = new Manager($mapper, $protocolAdapter);

		$post = $this->manager->find("#30:0");
		$this->assertInternalType('integer', $post->id);
	}

	/**
	 * @expectedException Congow\Orient\Exception\Casting\Mismatch
	 */
	public function testAnExceptionIsRaisedWhenAnIntegerPropertyIsNotAnInteger()
	{
		$post = $this->manager->find("#30:0");
	}

	public function testMismatchedAttributesAreConvertedIfTheMapperToleratesMismatches(){
	    $mapper          = new Mapper(__DIR__ . "/../../proxies");

	    $mapper->enableMismatchesTolerance();

	    $mapper->setDocumentDirectories(array('./test/Integration/Document' => 'test'));
	    $client          = new \Congow\Orient\Http\Client\Curl(false, TEST_ODB_TIMEOUT);
	    $binding         = new \Congow\Orient\Foundation\Binding($client, TEST_ODB_HOST, TEST_ODB_PORT, TEST_ODB_USER, TEST_ODB_PASSWORD, TEST_ODB_DATABASE);
	    $protocolAdapter = new \Congow\Orient\Foundation\Protocol\Adapter\Http($binding);
	    $this->manager   = new Manager($mapper, $protocolAdapter);

		$post = $this->manager->find("#30:0");
		$this->assertInternalType('integer', $post->title);
	}


}
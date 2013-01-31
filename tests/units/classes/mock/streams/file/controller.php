<?php

namespace mageekguy\atoum\tests\units\mock\streams\file;

use
	mageekguy\atoum,
	mageekguy\atoum\mock\streams\file\controller as testedClass
;

require_once __DIR__ . '/../../../../runner.php';

class controller extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\mock\stream\controller');
	}

	public function test__construct()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->string($controller->getContents())->isEmpty()
				->integer($controller->getPointer())->isZero()
				->integer($controller->getMode())->isEqualTo(644)
				->boolean($controller->stream_eof())->isFalse()
				->array($controller->stat())->isNotEmpty()
		;
	}

	public function testContains()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->object($controller->contains('abcdefghijklmnopqrstuvwxyz'))->isIdenticalTo($controller)
				->string($controller->stream_read(1))->isEqualTo('a')
				->boolean($controller->stream_eof())->isFalse()
				->string($controller->stream_read(1))->isEqualTo('b')
				->boolean($controller->stream_eof())->isFalse()
				->string($controller->stream_read(2))->isEqualTo('cd')
				->boolean($controller->stream_eof())->isFalse()
				->string($controller->stream_read(4096))->isEqualTo('efghijklmnopqrstuvwxyz')
				->boolean($controller->stream_eof())->isTrue()
				->string($controller->stream_read(1))->isEmpty()
		;
	}

	public function testIsEmpty()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->and($controller->contains('abcdefghijklmnopqrstuvwxyz'))
			->then
				->object($controller->isEmpty())->isIdenticalTo($controller)
				->string($controller->getContents())->isEmpty()
		;
	}

	public function testExists()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->object($controller->exists())->isIdenticalTo($controller)
				->array($controller->stat())->isNotEmpty()
			->if($controller->notExists())
			->then
				->object($controller->exists())->isIdenticalTo($controller)
				->array($controller->stat())->isNotEmpty()
		;
	}

	public function testNotExists()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->object($controller->notExists())->isIdenticalTo($controller)
				->boolean($controller->stat())->isFalse()
		;
	}

	public function testIsNotReadable()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->object($controller->isNotReadable())->isIdenticalTo($controller)
				->integer($controller->getMode())->isEqualTo(200)
				->object($controller->isNotReadable())->isIdenticalTo($controller)
				->integer($controller->getMode())->isEqualTo(200)
		;
	}

	public function testIsReadable()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->object($controller->isReadable())->isIdenticalTo($controller)
				->integer($controller->getMode())->isEqualTo(644)
				->object($controller->isReadable())->isIdenticalTo($controller)
				->integer($controller->getMode())->isEqualTo(644)
			->if($controller->isNotReadable())
			->then
				->object($controller->isReadable())->isIdenticalTo($controller)
				->integer($controller->getMode())->isEqualTo(644)
				->object($controller->isReadable())->isIdenticalTo($controller)
				->integer($controller->getMode())->isEqualTo(644)
		;
	}

	public function testIsNotWritable()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->object($controller->isNotWritable())->isIdenticalTo($controller)
				->integer($controller->getMode())->isEqualTo(444)
				->object($controller->isNotWritable())->isIdenticalTo($controller)
				->integer($controller->getMode())->isEqualTo(444)
		;
	}

	public function testIsWritable()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->and($controller->isNotWritable())
			->then
				->object($controller->isWritable())->isIdenticalTo($controller)
				->integer($controller->getMode())->isEqualTo(666)
				->object($controller->isWritable())->isIdenticalTo($controller)
				->integer($controller->getMode())->isEqualTo(666)
		;
	}

	public function testIsExecutable()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->object($controller->isExecutable())->isIdenticalTo($controller)
				->integer($controller->getMode())->isEqualTo(755)
				->object($controller->isExecutable())->isIdenticalTo($controller)
				->integer($controller->getMode())->isEqualTo(755)
		;
	}

	public function testIsNotExecutable()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->and($controller->isExecutable())
			->then
				->object($controller->isNotExecutable())->isIdenticalTo($controller)
				->integer($controller->getMode())->isEqualTo(644)
				->object($controller->isNotExecutable())->isIdenticalTo($controller)
				->integer($controller->getMode())->isEqualTo(644)
		;
	}

	public function testOpen()
	{
		$this
			->assert('Use r and r+ mode')
				->if($controller = new testedClass(uniqid()))
				->then
					->boolean($controller->open('z', 0))->isFalse()
					->array($controller->getCalls())->isEmpty()
					->boolean($controller->open('z', STREAM_REPORT_ERRORS))->isFalse()
					->error('Operation timed out', E_USER_WARNING)->exists()
					->boolean($controller->open('r', 0))->isTrue()
					->integer($controller->tell())->isZero()
					->string($controller->read(1))->isEmpty()
					->integer($controller->write('a'))->isZero()
					->boolean($controller->open('r+', 0))->isTrue()
					->integer($controller->tell())->isZero()
					->string($controller->read(1))->isEmpty()
					->integer($controller->write('a'))->isEqualTo(1)
					->boolean($controller->open('r', STREAM_USE_PATH, $path))->isTrue()
					->string($path)->isEqualTo($controller->getPath())
					->boolean($controller->open('r+', STREAM_USE_PATH, $path))->isTrue()
					->string($path)->isEqualTo($controller->getPath())
				->if($controller->setContents('abcdefghijklmnopqrstuvwxyz'))
				->then
					->boolean($controller->open('r', 0))->isTrue()
					->array($controller->getCalls())->isEmpty()
					->integer($controller->tell())->isZero()
					->string($controller->read(1))->isEqualTo('a')
					->integer($controller->write('a'))->isZero()
					->boolean($controller->open('r+', 0))->isTrue()
					->integer($controller->tell())->isZero()
					->string($controller->read(1))->isEqualTo('a')
					->integer($controller->write('a'))->isEqualTo(1)
					->boolean($controller->open('r', STREAM_USE_PATH, $path))->isTrue()
					->string($path)->isEqualTo($controller->getPath())
					->boolean($controller->open('r+', STREAM_USE_PATH, $path))->isTrue()
					->string($path)->isEqualTo($controller->getPath())
				->if($controller->notExists())
				->then
					->boolean($controller->open('r', 0))->isFalse()
					->array($controller->getCalls())->isEmpty()
					->boolean($controller->open('r+', 0))->isFalse()
					->boolean($controller->open('r', STREAM_REPORT_ERRORS))->isFalse()
					->error('No such file or directory', E_USER_WARNING)->exists()
					->boolean($controller->open('r+', STREAM_REPORT_ERRORS))->isFalse()
					->error('No such file or directory', E_USER_WARNING)->exists()
					->boolean($controller->open('r+', 0))->isFalse()
					->boolean($controller->open('r', STREAM_USE_PATH, $path))->isFalse()
					->variable($path)->isNull()
					->boolean($controller->open('r+', STREAM_USE_PATH, $path))->isFalse()
					->variable($path)->isNull()
				->if($controller->exists())
				->and($controller->isNotReadable())
				->then
					->boolean($controller->open('r', 0))->isFalse()
					->array($controller->getCalls())->isEmpty()
					->boolean($controller->open('r+', 0))->isFalse()
					->boolean($controller->open('r', STREAM_REPORT_ERRORS))->isFalse()
					->error('Permission denied', E_USER_WARNING)->exists()
					->boolean($controller->open('r+', STREAM_REPORT_ERRORS))->isFalse()
					->error('Permission denied', E_USER_WARNING)->exists()
					->boolean($controller->open('r', STREAM_USE_PATH, $path))->isFalse()
					->variable($path)->isNull()
					->boolean($controller->open('r+', STREAM_USE_PATH, $path))->isFalse()
					->variable($path)->isNull()
				->if($controller->isReadable())
				->and($controller->isNotWritable())
					->boolean($controller->open('r', 0))->isTrue()
					->array($controller->getCalls())->isEmpty()
					->boolean($controller->open('r+', 0))->isFalse()
					->boolean($controller->open('r+', STREAM_REPORT_ERRORS))->isFalse()
					->error('Permission denied', E_USER_WARNING)->exists()
					->boolean($controller->open('r', STREAM_USE_PATH, $path))->isTrue()
					->string($path)->isEqualTo($controller->getPath())
					->boolean($controller->open('r+', STREAM_USE_PATH, $path))->isFalse()
					->variable($path)->isNull()
			->assert('Use w and w+ mode')
				->if($controller = new testedClass(uniqid()))
				->then
					->boolean($controller->open('w', 0))->isTrue()
					->array($controller->getCalls())->isEmpty()
					->integer($controller->tell())->isZero()
					->string($controller->read(1))->isEmpty()
					->integer($controller->write('a'))->isEqualTo(1)
					->boolean($controller->open('w+', 0))->isTrue()
					->integer($controller->tell())->isZero()
					->string($controller->read(1))->isEmpty()
					->integer($controller->write('a'))->isEqualTo(1)
					->boolean($controller->open('w', STREAM_USE_PATH, $path))->isTrue()
					->string($path)->isEqualTo($controller->getPath())
					->boolean($controller->open('w+', STREAM_USE_PATH, $path))->isTrue()
					->string($path)->isEqualTo($controller->getPath())
				->if($controller->setContents('abcdefghijklmnopqrstuvwxyz'))
				->then
					->boolean($controller->open('w', 0))->isTrue()
					->array($controller->getCalls())->isEmpty()
					->integer($controller->tell())->isZero()
					->string($controller->read(1))->isEmpty()
					->integer($controller->write('a'))->isEqualTo(1)
					->boolean($controller->open('w+', 0))->isTrue()
					->integer($controller->tell())->isZero()
					->string($controller->read(1))->isEmpty()
					->integer($controller->write('a'))->isEqualTo(1)
				->if($controller->isNotWritable())
				->then
					->boolean($controller->open('w', 0))->isFalse()
					->array($controller->getCalls())->isEmpty()
					->boolean($controller->open('w', STREAM_REPORT_ERRORS))->isFalse()
					->error('Permission denied', E_USER_WARNING)->exists()
					->boolean($controller->open('w+', 0))->isFalse()
					->boolean($controller->open('w+', STREAM_REPORT_ERRORS))->isFalse()
					->error('Permission denied', E_USER_WARNING)->exists()
			->assert('Use c and c+ mode')
				->if($controller = new testedClass(uniqid()))
				->then
					->boolean($controller->open('c', 0))->isTrue()
					->array($controller->getCalls())->isEmpty()
					->integer($controller->tell())->isZero()
					->string($controller->read(1))->isEmpty()
					->integer($controller->write('a'))->isEqualTo(1)
					->boolean($controller->open('c+', 0))->isTrue()
					->integer($controller->tell())->isZero()
					->string($controller->read(1))->isEqualTo('a')
					->integer($controller->write('a'))->isEqualTo(1)
					->boolean($controller->open('c', STREAM_USE_PATH, $path))->isTrue()
					->string($path)->isEqualTo($controller->getPath())
					->boolean($controller->open('c+', STREAM_USE_PATH, $path))->isTrue()
					->string($path)->isEqualTo($controller->getPath())
				->if($controller->setContents('abcdefghijklmnopqrstuvwxyz'))
				->then
					->boolean($controller->open('c', 0))->isTrue()
					->array($controller->getCalls())->isEmpty()
					->integer($controller->tell())->isZero()
					->string($controller->read(1))->isEqualTo('a')
					->integer($controller->write('a'))->isEqualTo(1)
					->boolean($controller->open('c+', 0))->isTrue()
					->integer($controller->tell())->isZero()
					->string($controller->read(1))->isEqualTo('a')
					->integer($controller->write('a'))->isEqualTo(1)
				->if($controller->isNotWritable())
				->then
					->boolean($controller->open('c', 0))->isFalse()
					->array($controller->getCalls())->isEmpty()
					->boolean($controller->open('c', STREAM_REPORT_ERRORS))->isFalse()
					->error('Permission denied', E_USER_WARNING)->exists()
					->boolean($controller->open('c+', 0))->isFalse()
					->boolean($controller->open('c+', STREAM_REPORT_ERRORS))->isFalse()
					->error('Permission denied', E_USER_WARNING)->exists()
			->assert('Use a and a+ mode')
				->if($controller = new testedClass(uniqid()))
				->then
					->boolean($controller->open('a', 0))->isTrue()
					->array($controller->getCalls())->isEmpty()
					->integer($controller->tell())->isZero()
					->string($controller->read(1))->isEmpty()
					->integer($controller->write('a'))->isEqualTo(1)
					->boolean($controller->open('a+', 0))->isTrue()
					->integer($controller->tell())->isEqualTo(1)
					->string($controller->read(1))->isEmpty()
					->integer($controller->write('a'))->isEqualTo(1)
				->if($controller->setContents('abcdefghijklmnopqrstuvwxyz'))
				->then
					->boolean($controller->open('a', 0))->isTrue()
					->array($controller->getCalls())->isEmpty()
					->integer($controller->tell())->isEqualTo(26)
					->string($controller->read(1))->isEmpty()
					->integer($controller->write('a'))->isEqualTo(1)
				->if($controller->isNotWritable())
				->then
					->boolean($controller->open('a', 0))->isFalse()
					->array($controller->getCalls())->isEmpty()
					->boolean($controller->open('a+', 0))->isFalse()
				->if($controller = new testedClass(uniqid()))
				->if($controller->isWritable())
				->and($controller->isNotReadable())
				->then
					->boolean($controller->open('a', 0))->isTrue()
					->array($controller->getCalls())->isEmpty()
					->boolean($controller->open('a+', 0))->isFalse()
					->boolean($controller->open('a+', STREAM_REPORT_ERRORS))->isFalse()
					->error('Permission denied', E_USER_WARNING)->exists()
			->assert('Use x and x+ mode')
				->if($controller = new testedClass(uniqid()))
				->then
					->boolean($controller->open('x', 0))->isFalse()
					->array($controller->getCalls())->isEmpty()
					->boolean($controller->open('x', STREAM_REPORT_ERRORS))->isFalse()
					->error('File exists', E_USER_WARNING)->exists()
					->boolean($controller->open('x+', 0))->isFalse()
					->boolean($controller->open('x+', STREAM_REPORT_ERRORS))->isFalse()
					->error('File exists', E_USER_WARNING)->exists()
				->if($controller->notExists())
				->then
					->boolean($controller->open('x', 0))->isTrue()
					->array($controller->getCalls())->isEmpty()
					->integer($controller->tell())->isZero()
					->string($controller->read(1))->isEmpty()
					->integer($controller->write('a'))->isEqualTo(0)
					->boolean($controller->open('x+', 0))->isTrue()
					->integer($controller->tell())->isZero()
					->string($controller->read(1))->isEmpty()
					->integer($controller->write('a'))->isEqualTo(1)
				->if($controller->setContents('abcdefghijklmnopqrstuvwxyz'))
				->then
					->boolean($controller->open('x', 0))->isTrue()
					->array($controller->getCalls())->isEmpty()
					->integer($controller->tell())->isZero()
					->string($controller->read(1))->isEqualTo('a')
					->integer($controller->write('a'))->isEqualTo(0)
					->boolean($controller->open('x+', 0))->isTrue()
					->integer($controller->tell())->isZero()
					->string($controller->read(1))->isEqualTo('a')
					->integer($controller->write('a'))->isEqualTo(1)
				->if($controller->isNotReadable())
				->then
					->boolean($controller->open('x', 0))->isFalse()
					->array($controller->getCalls())->isEmpty()
					->boolean($controller->open('x', STREAM_REPORT_ERRORS))->isFalse()
					->error('Permission denied', E_USER_WARNING)->exists()
					->boolean($controller->open('x+', 0))->isFalse()
					->boolean($controller->open('x+', STREAM_REPORT_ERRORS))->isFalse()
					->error('Permission denied', E_USER_WARNING)->exists()
				->if($controller->isReadable())
				->and($controller->isNotWritable())
				->then
					->boolean($controller->open('x', 0))->isTrue()
					->array($controller->getCalls())->isEmpty()
					->boolean($controller->open('x+', 0))->isFalse()
					->boolean($controller->open('x+', STREAM_REPORT_ERRORS))->isFalse()
					->error('Permission denied', E_USER_WARNING)->exists()
		;
	}

	public function testSeek()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->boolean($controller->seek(0))->isFalse()
				->boolean($controller->seek(1))->isTrue()
			->if($controller->contains('abcdefghijklmnopqrstuvwxyz'))
			->then
				->boolean($controller->seek(0))->isFalse()
				->boolean($controller->seek(1))->isTrue()
				->string($controller->read(1))->isEqualTo('b')
				->boolean($controller->seek(25))->isTrue()
				->string($controller->read(1))->isEqualTo('z')
				->boolean($controller->seek(26))->isFalse()
				->string($controller->read(1))->isEmpty()
				->boolean($controller->seek(0))->isTrue()
				->string($controller->read(1))->isEqualTo('a')
				->boolean($controller->seek(-1, SEEK_END))->isTrue()
				->string($controller->read(1))->isEqualTo('z')
				->boolean($controller->seek(-26, SEEK_END))->isTrue()
				->string($controller->read(1))->isEqualTo('a')
				->boolean($controller->seek(-27, SEEK_END))->isTrue()
				->string($controller->read(1))->isEmpty()
			->if($controller = new testedClass(uniqid()))
			->and($controller->contains('abcdefghijklmnopqrstuvwxyz'))
			->and($controller->read(4096))
			->then
				->boolean($controller->eof())->isTrue()
				->boolean($controller->seek(0))->isTrue()
				->boolean($controller->eof())->isFalse()
		;
	}

	public function testEof()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->boolean($controller->eof())->isFalse()
			->if($controller->contains('abcdefghijklmnopqrstuvwxyz'))
			->then
				->boolean($controller->eof())->isFalse()
			->if($controller->seek(26))
			->then
				->boolean($controller->eof())->isFalse()
			->if($controller->seek(27))
			->then
				->boolean($controller->eof())->isFalse()
			->if($controller->read(1))
			->then
				->boolean($controller->eof())->isTrue()
		;
	}

	public function testTell()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->integer($controller->tell())->isZero()
			->if($controller->seek($offset = rand(1, 4096)))
			->then
				->integer($controller->tell())->isEqualTo($offset)
		;
	}

	public function testRead()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->string($controller->read(1))->isEmpty()
				->boolean($controller->eof())->isTrue()
			->if($controller->contains('abcdefghijklmnopqrstuvwxyz'))
			->then
				->string($controller->read(1))->isEqualTo('a')
				->boolean($controller->eof())->isFalse()
			->if($controller->seek(6))
			->then
				->string($controller->read(1))->isEqualTo('g')
				->string($controller->read(4096))->isEqualTo('hijklmnopqrstuvwxyz')
				->boolean($controller->eof())->isTrue()
				->string($controller->read(1))->isEmpty()
		;
	}

	public function testWrite()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->integer($controller->write('a'))->isZero()
				->integer($controller->tell())->isZero()
			->if($controller->open('r', 0))
			->then
				->integer($controller->write('a'))->isZero()
				->integer($controller->tell())->isZero()
			->if($controller->open('w', 0))
			->then
				->integer($controller->write('a'))->isEqualTo(1)
				->integer($controller->tell())->isEqualTo(1)
				->integer($controller->write('bcdefghijklmnopqrstuvwxyz'))->isEqualTo(25)
				->integer($controller->tell())->isEqualTo(26)
		;
	}

	public function testMetadata()
	{
		if (PHP_VERSION_ID < 50400)
		{
			$this->skip('It\'s not possible to manage stream\'s metadata before PHP 5.4.0');
		}

		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->boolean($controller->metadata(STREAM_META_ACCESS, 755))->isTrue()
				->integer($controller->getMode())->isEqualTo(755)
		;
	}

	public function testStat()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->and($stats = array(
					'dev' => 0,
					'ino' => 0,
					'mode' => 33188,
					'nlink' => 0,
					'uid' => getmyuid(),
					'gid' => getmygid(),
					'rdev' => 0,
					'size' => 0,
					'atime' => 507769200,
					'mtime' => 507769200,
					'ctime' => 507769200,
					'blksize' => 0,
					'blocks' => 0,
				)
			)
			->and($stats[0] = & $stats['dev'])
			->and($stats[1] = & $stats['ino'])
			->and($stats[2] = & $stats['mode'])
			->and($stats[3] = & $stats['nlink'])
			->and($stats[4] = & $stats['uid'])
			->and($stats[5] = & $stats['gid'])
			->and($stats[6] = & $stats['rdev'])
			->and($stats[7] = & $stats['size'])
			->and($stats[8] = & $stats['atime'])
			->and($stats[9] = & $stats['mtime'])
			->and($stats[10] = & $stats['ctime'])
			->and($stats[11] = & $stats['blksize'])
			->and($stats[12] = & $stats['blocks'])
			->then
				->array($controller->stat())->isEqualTo($stats)
			->if($controller->notExists())
			->then
				->boolean($controller->stat())->isFalse()
			->if($controller = new testedClass(uniqid()))
			->and($controller->stream_stat[2] = false)
			->then
				->array($controller->stream_stat())->isNotEmpty()
				->boolean($controller->stream_stat())->isFalse()
				->array($controller->stream_stat())->isNotEmpty()
		;
	}

	public function testUnlink()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->boolean($controller->unlink())->isTrue()
				->boolean($controller->stat())->isFalse()
				->boolean($controller->unlink())->isFalse()
				->boolean($controller->stat())->isFalse()
			->if($controller->exists())
			->then
				->boolean($controller->unlink())->isTrue()
				->boolean($controller->stat())->isFalse()
				->boolean($controller->unlink())->isFalse()
				->boolean($controller->stat())->isFalse()
			->if($controller->exists())
			->and($controller->isNotWritable())
			->then
				->boolean($controller->unlink())->isFalse()
				->array($controller->stat())->isNotEmpty()
			->if($controller->isWritable())
				->boolean($controller->unlink())->isTrue()
				->boolean($controller->stat())->isFalse()
				->boolean($controller->unlink())->isFalse()
				->boolean($controller->stat())->isFalse()
		;
	}

	public function testSetPath()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->boolean($controller->setPath($newPath = uniqid()))->isTrue()
				->string($controller->getPath())->isEqualTo($newPath)
		;
	}

	public function testDuplicate()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->object($duplicatedController = $controller->duplicate())->isEqualTo($controller)
			->if($controller->setPath($path = uniqid()))
			->then
				->string($duplicatedController->getPath())->isEqualTo($path)
			->if($controller->stream_lock())
			->then
				->array($duplicatedController->getCalls())->isEqualTo($controller->getCalls())
			->if($controller->stream_lock = function() {})
			->then
				->array($duplicatedController->getInvokers())->isEqualTo($controller->getInvokers())
			->if($controller->setContents(uniqid()))
			->then
				->string($duplicatedController->getContents())->isEqualTo($controller->getContents())
			->if($controller->isNotReadable())
			->and($controller->isNotWritable())
			->and($controller->isNotExecutable())
			->then
				->integer($duplicatedController->getMode())->isEqualTo($controller->getMode())
			->if($controller->notExists())
			->then
				->boolean($duplicatedController->stat())->isEqualTo($controller->stat())
		;
	}

	public function testInvoke()
	{
		$this
			->if($controller = new testedClass(uniqid()))
			->then
				->boolean($controller->invoke('stream_open', $arguments = array(uniqid(), 'z', 0)))->isFalse()
				->array($controller->getCalls('stream_open'))->isEqualTo(array(
						1 => $arguments
					)
				)
				->boolean($controller->invoke('stream_open', $otherArguments = array(uniqid(), 'r', 0)))->isTrue()
				->array($controller->getCalls('stream_open'))->isEqualTo(array(
						1 => $arguments,
						2 => $otherArguments
					)
				)
			->if($controller->stream_open = $return = uniqid())
			->then
				->string($controller->invoke('stream_open', $anotherArguments = array(uniqid(), 'r', 0)))->isEqualTo($return)
				->array($controller->getCalls('stream_open'))->isEqualTo(array(
						1 => $arguments,
						2 => $otherArguments,
						3 => $anotherArguments
					)
				)
		;
	}
}

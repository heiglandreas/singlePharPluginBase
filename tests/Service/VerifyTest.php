<?php
/**
 * Copyright by the ComposerDistributor-Team
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

declare(strict_types=1);

namespace PharIo\ComposerDistributorTest\Service;

use Exception;
use GnuPG;
use PharIo\ComposerDistributor\KeyDirectory;
use PharIo\ComposerDistributor\Service\Verify;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use SplFileInfo;
use function var_dump;

class VerifyTest extends TestCase
{
	public function setUp() : void
	{
		parent::setUp();
		require_once __DIR__ . '/../GnuPG.php';
	}

	/**
	 * @covers \PharIo\ComposerDistributor\Service\Verify::__construct
	 */
	public function testThatConstructorThrowsWithoutKeys(): void
	{
		$keys = new KeyDirectory(new SplFileInfo(
			__DIR__ . '/_assets/emptyKeyDirectory'
		));

		$gpg = self::getMockBuilder(GnuPG::class)
			->disableOriginalConstructor()
			->getMock();
		$gpg->method('import')->willReturn(['imported' => 0]);

		self::expectException(RuntimeException::class);
		self::expectExceptionMessage('Could not import needed GPG key!');

		new Verify($keys, $gpg);
	}

	/**
	 * @covers \PharIo\ComposerDistributor\Service\Verify::__construct
	 */
	public function testThatConstructorWorksWithSingleKey(): void
	{
		$keys = new KeyDirectory(new SplFileInfo(
			__DIR__ . '/_assets/singleKeyDirectory'
		));

		$gpg = self::getMockBuilder(GnuPG::class)
			->disableOriginalConstructor()
			->getMock();

		$gpg->method('import')->willReturn(['imported' => 1]);

		self::assertInstanceOf(Verify::class, new Verify($keys, $gpg));
	}

	/**
	 * @covers \PharIo\ComposerDistributor\Service\Verify::__construct
	 */
	public function testThatConstructorWorksWithAlreadyImportedKey(): void
	{
		$keys = new KeyDirectory(new SplFileInfo(
			__DIR__ . '/_assets/singleKeyDirectory'
		));

		$gpg = self::createMock(GnuPG::class);

		$gpg->method('import')->willReturn([
			'imported' => 0,
			'fingerprint' => 'a'
		]);

		self::assertInstanceOf(Verify::class, new Verify($keys, $gpg));
	}

	/**
	 * @covers \PharIo\ComposerDistributor\Service\Verify::fileWithSignature
	 */
	public function testThatCorrectVerificationWillNotThrowAnException(): void
	{
		$keys = new KeyDirectory(new SplFileInfo(
			__DIR__ . '/_assets/singleKeyDirectory'
		));

		$gpg = self::createMock(GnuPG::class);
		$gpg->method('import')->will(self::returnValue(['imported' => 1]));
		$gpg->method('verify')->will(self::returnValue([]));

		$verify = new Verify($keys, $gpg);

		try {
			self::assertTrue($verify->fileWithSignature(
				new SplFileInfo(__DIR__ . '/_assets/singleKeyDirectory/junitdiff.key'),
				new SplFileInfo(__DIR__ . '/_assets/singleKeyDirectory/junitdiff.key')
			));
		} catch(Exception $e) {
			self::assertTrue(false);
		}
	}
}

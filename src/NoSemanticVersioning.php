<?php
/**
 * Copyright by the ComposerDistributor-Team
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

declare(strict_types=1);

namespace PharIo\ComposerDistributor;

use RuntimeException;

final class NoSemanticVersioning extends RuntimeException
{
	public static function fromversionString(string $version) : self
	{
		return new self(sprintf('The version string "%s" does not follow semantic versioning', $version));
	}
}

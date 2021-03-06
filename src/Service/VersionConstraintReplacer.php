<?php
/**
 * Copyright by the ComposerDistributor-Team
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

declare(strict_types=1);

namespace PharIo\ComposerDistributor\Service;

use PharIo\ComposerDistributor\PackageVersion;

final class VersionConstraintReplacer
{
	private $versionConstraint;

	public function __construct(PackageVersion $versionConstraint)
	{
		$this->versionConstraint = $versionConstraint;
	}

	public function replace(string $string) : string
	{
		return str_replace([
			'%minor%',
			'%major%',
			'%patch%',
			'%release%',
			'%build%',
			'%version%',
		], [
			$this->versionConstraint->minor(),
			$this->versionConstraint->major(),
			$this->versionConstraint->patch(),
			$this->versionConstraint->preRelease(),
			$this->versionConstraint->build(),
			$this->versionConstraint->fullVersion(),
		], $string);
	}
}

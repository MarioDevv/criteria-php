<?php

declare(strict_types=1);

namespace MarioDevv\Criteria;

use Exception;

final class InvalidCriteria extends Exception
{
	public function __construct()
	{
		parent::__construct('Page size is required when page number is defined');
	}
}

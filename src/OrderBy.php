<?php

declare(strict_types=1);

namespace MarioDevv\Criteria;

final class OrderBy
{
	public function __construct(protected string $value) {}

	public function value(): string
	{
		return $this->value;
	}
}

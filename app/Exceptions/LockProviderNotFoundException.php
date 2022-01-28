<?php
namespace App\Exceptions;

use UnexpectedValueException;
use App\Exceptions\UnexpectedValueExceptionInterface;

final class LockProviderNotFoundException extends UnexpectedValueException implements UnexpectedValueExceptionInterface
{
}

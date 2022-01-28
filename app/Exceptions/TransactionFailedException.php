<?php

namespace App\Exceptions;

use LogicException;

final class TransactionFailedException extends LogicException implements LogicExceptionInterface
{
}

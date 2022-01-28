<?php
namespace App\Exceptions;

use UnderflowException;
use App\Exceptions\UnderflowExceptionInterface;

final class RecordNotFoundException extends UnderflowException implements UnderflowExceptionInterface
{
}

<?php declare(strict_types=1);

namespace Bemit\DynamoDB;

/**
 * Converts from array (or stdClass) to DynamoDB item style
 * ['some-text'] -> [['S' => 'some-text']]
 */
interface ArrayToItemInterface {

    public function arrayToItem($item): array;

    public function parseArrayElement($value): array;
}

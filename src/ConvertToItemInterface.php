<?php declare(strict_types=1);

namespace Bemit\DynamoDB;

/**
 * Converts from array (or stdClass) to DynamoDB item style
 * ['some-text'] -> [['S' => 'some-text']]
 */
interface ConvertToItemInterface {

    /**
     * @param array|\stdClass $item
     * @param array $schema
     * @param bool $ignore_nulls
     * @return array
     * @throws InvalidTypeException
     */
    public function toItem(array|\stdClass $item, array $schema = [], bool $ignore_nulls = false): array;

    /**
     * @param $value
     * @param string|null $type
     * @return array
     * @throws InvalidTypeException
     */
    public function toItemValue($value, ?string $type = null): array;
}

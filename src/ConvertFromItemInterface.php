<?php declare(strict_types=1);

namespace Bemit\DynamoDB;

/**
 * Converts from DynamoDB item style to array (or stdClass)
 * [['S' => 'some-text']] -> ['some-text']
 */
interface ConvertFromItemInterface {

    /**
     * @param array $item
     * @param false $enforce_object
     * @return array|\stdClass
     * @throws InvalidItemTypeException
     */
    public function fromItem(array $item, bool $enforce_object = false): array|\stdClass;

    /**
     * @param $value_def
     * @return float|string|bool|array|\stdClass|null
     * @throws InvalidItemTypeException
     */
    public function fromItemValue($value_def): float|string|bool|array|\stdClass|null;
}

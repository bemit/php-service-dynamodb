<?php declare(strict_types=1);

namespace Bemit\DynamoDB;

/**
 * Converts from DynamoDB item style to array (or stdClass)
 * [['S' => 'some-text']] -> ['some-text']
 */
interface ItemToArrayInterface {
    /**
     * @param array $item
     * @return array|\stdClass
     * @throws InvalidModeException
     */
    public function itemToArray(array $item);

    public function parseItemProp($value_def);
}

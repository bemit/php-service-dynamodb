<?php declare(strict_types=1);

namespace Bemit\DynamoDB;

/**
 * Converts from array (or stdClass) to DynamoDB item style
 * ['some-text'] -> [['S' => 'some-text']]
 */
class ArrayToItem implements ArrayToItemInterface {
    /**
     * @throws \JsonException
     * @throws InvalidTypeException
     */
    public function arrayToItem($item): array {
        $data = [];
        if($item instanceof \stdClass) {
            $item = get_object_vars($item);
        }

        foreach($item as $key => $value) {
            $data[$key] = $this->parseArrayElement($value);
        }

        return $data;
    }

    /**
     * @throws \JsonException
     * @throws InvalidTypeException
     */
    public function parseArrayElement($value): array {
        $is_array = is_array($value);
        $data = [];

        if(is_null($value)) {
            $data['NULL'] = true;
        } else if(is_string($value)) {
            $data['S'] = $value;
        } else if(is_numeric($value)) {
            $data['N'] = (string)$value;
        } else if(is_bool($value)) {
            $data['BOOL'] = (bool)$value;
        } else if($is_array && array_is_list($value)) {
            $data['L'] = $this->arrayToItem($value);
        } else if($value instanceof \stdClass || $is_array) {
            $data['M'] = $this->arrayToItem($value);
        } else {
            throw new InvalidTypeException('parseArrayElement data type not supported: ' . json_encode($value, JSON_THROW_ON_ERROR));
        }

        return $data;
    }
}

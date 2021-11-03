<?php declare(strict_types=1);

namespace Bemit\DynamoDB;

/**
 * Converts from DynamoDB item style to array (or stdClass)
 * [['S' => 'some-text']] -> ['some-text']
 */
class ItemToArray implements ItemToArrayInterface {
    /**
     * @throws \JsonException
     * @throws InvalidModeException
     */
    public function itemToArray(array $item, $enforce_object = false) {
        if($enforce_object) {
            $data = new \stdClass();
        } else {
            $data = [];
        }
        foreach($item as $key => $value_def) {
            $d = $this->parseItemProp($value_def);
            if($enforce_object) {
                $data->$key = $d;
            } else {
                $data[$key] = $d;
            }
        }
        return $data;
    }

    /**
     * @throws InvalidModeException
     * @throws \JsonException
     */
    public function parseItemProp($value_def) {
        if(isset($value_def['NULL'])) {
            return null;
        }
        if(isset($value_def['S'])) {
            return $value_def['S'];
        }
        if(isset($value_def['N'])) {
            return (float)$value_def['N'];
        }
        if(isset($value_def['BOOL'])) {
            return (bool)$value_def['BOOL'];
        }
        if(isset($value_def['M'])) {
            return $this->itemToArray($value_def['M'], true);
        }
        if(isset($value_def['L'])) {
            $data = [];
            foreach($value_def['L'] as $l_item) {
                $data[] = $this->parseItemProp($l_item);
            }
            return $data;
        }
        throw new InvalidModeException('parseItemProp mode not supported: ' . json_encode(array_keys($value_def), JSON_THROW_ON_ERROR));
    }
}

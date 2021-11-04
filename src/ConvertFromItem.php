<?php declare(strict_types=1);

namespace Bemit\DynamoDB;

/**
 * Converts from DynamoDB item style to array (or stdClass)
 * [['S' => 'some-text']] -> ['some-text']
 */
class ConvertFromItem implements ConvertFromItemInterface {
    /**
     * @throws \JsonException
     * @throws InvalidItemTypeException
     */
    public function fromItem(array $item, bool $enforce_object = false): array|\stdClass {
        if($enforce_object) {
            $data = new \stdClass();
        } else {
            $data = [];
        }
        foreach($item as $key => $value_def) {
            $d = $this->fromItemValue($value_def);
            if($enforce_object) {
                $data->$key = $d;
            } else {
                $data[$key] = $d;
            }
        }
        return $data;
    }

    /**
     * @throws InvalidItemTypeException
     * @throws \JsonException
     */
    public function fromItemValue($value_def): float|string|bool|array|\stdClass|null {
        if(isset($value_def['NULL']) && $value_def['NULL']) {
            return null;
        }
        if(isset($value_def['S'])) {
            return (string)$value_def['S'];
        }
        if(isset($value_def['SS'])) {
            return $value_def['SS'];
        }
        if(isset($value_def['NS'])) {
            return array_map(static fn($n) => (float)$n, $value_def['NS']);
        }
        if(isset($value_def['N'])) {
            return (float)$value_def['N'];
        }
        if(isset($value_def['BOOL'])) {
            return (bool)$value_def['BOOL'];
        }
        if(isset($value_def['M'])) {
            return $this->fromItem($value_def['M'], true);
        }
        if(isset($value_def['L'])) {
            $data = [];
            foreach($value_def['L'] as $l_item) {
                $data[] = $this->fromItemValue($l_item);
            }
            return $data;
        }
        throw new InvalidItemTypeException('fromItemValue mode not supported: ' . json_encode(array_keys($value_def), JSON_THROW_ON_ERROR));
    }
}

<?php declare(strict_types=1);

namespace Bemit\DynamoDB;

/**
 * Converts from array (or stdClass) to DynamoDB item style
 * ['some-text'] -> [['S' => 'some-text']]
 */
class ConvertToItem implements ConvertToItemInterface {
    /**
     * @throws \JsonException
     * @throws InvalidTypeException
     */
    public function toItem(array|\stdClass $item, array $schema = [], bool $ignore_nulls = false): array {
        $data = [];
        if($item instanceof \stdClass) {
            $item = get_object_vars($item);
        }

        foreach($item as $key => $value) {
            if($ignore_nulls && is_null($value)) {
                continue;
            }
            $data[$key] = $this->toItemValue($value, $schema[$key] ?? null);
        }

        return $data;
    }

    /**
     * @throws \JsonException
     * @throws InvalidTypeException
     */
    public function toItemValue($value, ?string $type = null): array {
        if($type === null) {
            $type = $this->detectType($value);
        }

        $data = [];
        switch($type) {
            case 'NULL':
                $data[$type] = true;
                break;
            case 'SS':
            case 'S':
                $data[$type] = $value;
                break;
            case 'N':
                $data[$type] = (string)$value;
                break;
            case 'NS':
                $data[$type] = array_map(static fn($v) => (string)$v, $value);
                break;
            case 'BOOL':
                $data[$type] = (bool)$value;
                break;
            case 'L':
            case 'M':
                $data[$type] = $this->toItem($value);
                break;
            case null:
                throw new InvalidTypeException('toItemValue data type not detectable: ' . json_encode($value, JSON_THROW_ON_ERROR));
            default:
                throw new InvalidTypeException('toItemValue data type not supported: ' . $type);
        }

        return $data;
    }

    protected function detectType($value): ?string {
        $is_array = is_array($value);
        $mode = null;
        if(is_null($value)) {
            $mode = 'NULL';
        } else if(is_string($value)) {
            $mode = 'S';
        } else if(is_numeric($value)) {
            $mode = 'N';
        } else if(is_bool($value)) {
            $mode = 'BOOL';
        } else if($is_array && array_is_list($value)) {
            $mode = 'L';
        } else if($is_array || $value instanceof \stdClass) {
            $mode = 'M';
        }
        return $mode;
    }
}

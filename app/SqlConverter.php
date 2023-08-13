<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class SqlConverter
{
    private string $tableName;
    private array $columns;
    private array $mappings = [];
    private array $values = [];
    private array $closures = [];
    private array $casts = [];
    private bool $eloquent = false;

    private Builder|null $query = null;
    private int $rowCount = 0;

    public static function fromBuilder(Builder $query): SqlConverter
    {
        $instance = new SqlConverter();
        $instance->query = $query;
        $instance->eloquent = true;
        $item = $query->clone()->first();
        $instance->tableName = $query->getModel()->getTable();
        if ($item === null)
            return $instance;

        $instance->rowCount = $query->clone()->count();
        $attributes = $item->getAttributes();

        foreach (array_keys($attributes) as $key) {
            $instance->columns[] = $key;
        }

        $instance->cast('created_at', 'datetime');
        $instance->cast('updated_at', 'datetime');

        return $instance;
    }

    private function getHead()
    {
        $result = "TRUNCATE `$this->tableName`;" . PHP_EOL;
        if ($this->rowCount > 0) {
            $result .= "INSERT INTO `$this->tableName` (";

            foreach ($this->columns as $column) {
                $columnName = isset($this->mappings[$column]) ? $this->mappings[$column] : $column;
                $result .= "`$columnName`, ";
            }

            $result = rtrim($result, ", ") . ") VALUES";
        }

        return rtrim($result, ", ");
    }

    /**
     * @param mixed $row
     * @return string
     */
    function getSql(mixed $row): string
    {
        $rowSql = PHP_EOL . "(";
        foreach ($this->columns as $column) {
            if (isset($this->closures[$column])) {
                $value = $this->closures[$column]($row);
            } else {
                if ($this->eloquent) {
                    $value = $this->values[$column] ?? $row->getAttribute($column);
                } else {
                    $value = $this->values[$column] ?? $row->$column;
                }
            }

            if (isset($this->casts[$column])) {
                $type = $this->casts[$column];
                switch ($type) {
                    case "bool" :
                        $value = Str::StartsWith($value, 'T') ? 1 : 0;
                        break;
                    case "date":
                        if ($value)
                            $value = (new Carbon($value))->format('Y-m-d');
                        break;
                    case "datetime":
                        if ($value)
                            $value = (new Carbon($value))->format('Y-m-d H:i:s');
                        break;
                    case "gender":
                        $value = $value->value;
                        break;
                    default:
                        dd($type);
                }
            }


            switch (gettype($value)) {
                case "NULL":
                    $rowSql .= "NULL, ";
                    break;
                case "string":
                    $value = trim($value);
                    $rowSql .= "'" . str_replace("'", "\'", $value) . "', ";
                    break;
                default:
                    $rowSql .= "$value, ";
            }
        }

        return rtrim($rowSql, ", ") . "), ";
    }

    private function getValues(): string
    {
        $result = '';
        foreach ($this->query->get() as $row) {
            $result .= $this->getSql($row);
        }
        return rtrim($result, ", ") . ";" . PHP_EOL;
    }

    private function writeValues($stream): void
    {
        $processed = 0;
        $this->query->chunk(10000, function(Collection $rows) use ($stream, &$processed) {
            $result = '';
            foreach ($rows as $row) {
                $result .= $this->getSql($row);
                $processed++;
                if ($processed == $this->rowCount)
                    $result = rtrim($result, ", ") . ";" . PHP_EOL;
            }
            fwrite($stream, $result);
        });
    }

    private function writeValuesLazy($stream): void
    {
        $processed = 0;
        $this->query->lazy()->each(function(object $row) use ($stream, &$processed) {
            $rowSql = $this->getSql($row);

            $processed++;
            if ($processed == $this->rowCount)
                $rowSql = rtrim($rowSql, ", ") . ";" . PHP_EOL;

            fwrite($stream, $rowSql);
        });
    }

    public function remove(array $columns): SqlConverter
    {
        $this->columns = array_diff($this->columns, $columns);

        return $this;
    }

    public function only(array $columns)
    {
        $this->columns = $columns;

        return $this;
    }

    public function add(string $column, $value): SqlConverter
    {
        $this->columns[] = $column;
        gettype($value) === 'object' ? $this->closures[$column] = $value :  $this->values[$column] = $value;

        return $this;
    }

    public function rename(string $from, string $to): SqlConverter
    {
        $this->mappings[$from] = $to;

        return $this;
    }

    public function cast(string $column, string $type): SqlConverter
    {
        $this->casts[$column] = $type;

        return $this;
    }

    public function tableName(string $tableName): SqlConverter
    {
        $this->tableName = $tableName;

        return $this;
    }

    public function write($stream): void {
        fwrite($stream, $this->getHead());

        if ($this->rowCount > 0)
            $this->writeValues($stream);
    }
}

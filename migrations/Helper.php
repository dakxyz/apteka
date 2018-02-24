<?php

namespace Xyz\Akulov\Apteka\Migrations;

class Helper
{
    public static function sqlAppendSetTimestampAsTrigger(string $table)
    {
        return <<<SQL
CREATE TRIGGER set_timestamp
  BEFORE INSERT OR UPDATE ON "{$table}"
  FOR EACH ROW
EXECUTE PROCEDURE trigger_set_timestamp();
SQL;
    }

    public static function sqlInsertDictionary(string $table, array $dictionary, string $field = 'name')
    {
        $sql = "INSERT INTO \"{$table}\"(\"{$field}\") VALUES ";
        foreach ($dictionary as $item) {
            $sql .= "('$item'),";
        }

        return substr($sql, 0, -1);
    }
}

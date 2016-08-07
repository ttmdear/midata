<?php
namespace Midata\Mysql\DDL;

use Midata\Object\Constraint as MidataConstraint;
use Midata\DDL\Constraint as DDLConstraint;
use Midata\DDL as MidataDDL;
use Midata\Mysql\Constraint as MysqlConstraint;

class Constraint extends DDLConstraint
{
    public function create(MidataConstraint $constraint)
    {
        $assert = $this->service('assert');

        if ($constraint->isForeignKey()) {
            return $this->createForeignKey($constraint);
        }elseif($constraint->isPrimaryKey()){
            return $this->createPrimaryKey($constraint);
        }else{
            $assert->exception("Unsupported type of constraints ".get_class($constraint));
        }
    }

    public function drop(MidataConstraint $constraint)
    {
        $assert = $this->service('assert');

        if ($constraint->isForeignKey()) {
            return $this->dropForeignKey($constraint);
        }elseif($constraint->isPrimaryKey()){
            return $this->dropPrimaryKey($constraint);
        }else{
            $assert->exception("Unsupported type of constraints ".get_class($constraint));
        }
    }

    public function alter(MidataConstraint $constraint)
    {
        $assert = $this->service('assert');

        if ($constraint->isForeignKey()) {
            return $this->alterForeignKey($constraint);
        }elseif($constraint->isPrimaryKey()){
            return $this->alterPrimaryKey($constraint);
        }else{
            $assert->exception("Unsupported type of constraints ".get_class($constraint));
        }
    }

    public function inline(MidataConstraint $constraint)
    {
        $assert = $this->service('assert');

        if ($constraint->isForeignKey()) {
            return $this->inlineForeignKey($constraint);
        }elseif($constraint->isPrimaryKey()){
            return $this->inlinePrimaryKey($constraint);
        }else{
            $assert->exception("Unsupported type of constraints ".get_class($constraint));
        }
    }

    private function createForeignKey(MidataConstraint $constraint)
    {
        $baseTable = $constraint->baseTable();
        $inline = $this->inline($constraint);

        $sql = "ALTER TABLE `$baseTable`\nADD $inline";

        return "$sql;";
    }

    private function dropForeignKey(MidataConstraint $constraint)
    {
        $tableName = $constraint->tableName();
        $name = $constraint->name();

        $adapter = $this->adapter();
        $table = $adapter->table($tableName);
        $indexes = $table->indexes(true);

        $sql = "";

        if (in_array($name, $indexes)) {
            // wraz z kluczem obcym powstal index, musze ten index usunac aby
            // moc znowu utworzyc klucz obcy ( index w mysql tworzy sie
            // automatycznie z kluczem obcym)
            $ddlIndex = $adapter->ddl(MidataDDL::INDEX);
            $index = $table->index($name);
            $sql .= $ddlIndex->drop($index)."\n";
        }

        $sql .= "ALTER TABLE `$tableName`\nDROP FOREIGN KEY `$name`";

        return "$sql;";
    }

    private function inlineForeignKey(MidataConstraint $constraint)
    {
        // CREATE TABLE `books_authors` (
        //   `book_id` int(10) unsigned NOT NULL,
        //   `author_id` int(10) unsigned NOT NULL,
        //   `type_id` int(10) unsigned NOT NULL,
        //   PRIMARY KEY (`book_id`,`author_id`,`type_id`),
        //   KEY `books_authors_author` (`author_id`,`type_id`),
        //   CONSTRAINT `books_authors_author` FOREIGN KEY (`author_id`, `type_id`) REFERENCES `authors` (`author_id`, `type_id`),
        //   CONSTRAINT `books_authors_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`)
        // ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci

        $baseTable = $constraint->baseTable();
        $refTable = $constraint->refTable();
        $columns = $constraint->columns();
        $name = $constraint->name();

        $onUpdate = $constraint->onUpdate();
        $onDelete = $constraint->onDelete();

        if ($onUpdate == MysqlConstraint::RESTRICT) {
            $onUpdate = "";
        }else{
            $onUpdate = "ON UPDATE $onUpdate";
        }

        if ($onDelete == MysqlConstraint::RESTRICT) {
            $onDelete = "";
        }else{
            $onDelete = "ON DELETE $onDelete";
        }

        $tableColumn = "";
        $refTableColumn = "";

        foreach ($columns as $def) {
            $base = $def['base'];
            $ref = $def['ref'];

            $tableColumn .= "`$base`,";
            $refTableColumn .= "`$ref`,";
        }

        $tableColumn = trim($tableColumn, ',');
        $refTableColumn = trim($refTableColumn, ',');

        $sql = "CONSTRAINT `$name` FOREIGN KEY ($tableColumn) REFERENCES `$refTable` ($refTableColumn) $onUpdate $onDelete";

        $sql = trim($sql);

        return $sql;
    }

    private function alterForeignKey(MidataConstraint $constraint)
    {
        $drop = $this->drop($constraint);
        $create = $this->drop($constraint);

        return "$drop;\n$create;";
    }

    private function createPrimaryKey(MidataConstraint $primaryKey)
    {
        $tableName = $primaryKey->tableName();
        $inline = $this->inline($primaryKey);

        $sql = "ALTER TABLE `$tableName`\nADD $inline";

        return "$sql;";
    }

    private function dropPrimaryKey(MidataConstraint $primaryKey)
    {
        $tableName = $primaryKey->tableName();
        $adapter = $this->adapter();

        $sql = "";

        if ($adapter->isMysql()) {
            $table = $adapter->table($tableName);
            $primaryKey = $table->primaryKey();

            if (count($primaryKey) == 1) {
                $column = $table->column($primaryKey[0]);

                if ($column->sequence()) {
                    // prubujemy usunac klucz podstawowy z kolumny ktora
                    // jest oznaczona jako auto_increment, wiec zanim
                    // usuniemy klucz musimy usunac rowniez auto_increment
                    // z kolumny
                    $ddlColumn = MidataDDL::factory($adapter, MidataDDL::COLUMN);
                    $column->sequence(false);
                    $alter = $ddlColumn->alter($column);

                    $sql .= "\n$alter";
                }
            }
        }

        $sql .= "\nALTER TABLE `$tableName` DROP PRIMARY KEY";

        return "$sql;";
    }

    private function inlinePrimaryKey(MidataConstraint $primaryKey)
    {
        $assert = $this->service('assert');

        $columns = $primaryKey->columns();

        if (empty($columns)) {
            $assert->exception("Can't create primary key for constraints without columns");
        }

        $columns = $this->implode($columns);

        $sql = "PRIMARY KEY ($columns)";

        return $sql;
    }

    private function alterPrimaryKey(MidataConstraint $primaryKey)
    {
        $drop = $this->drop($primaryKey);
        $create = $this->create($primaryKey);

        return "$drop\n$create";
    }
}

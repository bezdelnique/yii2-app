<?php
/**
 * Created by PhpStorm.
 * User: heman
 * Date: 26.03.2018
 * Time: 16:35
 */

namespace app\entities;


class EntityBulk
{
    public const C_modeInsertNormal = 0;
    public const C_modeInsertReplace = 1;
    public const C_modeInsertIgnore = 2;
    public const C_modeInsertUpdateOnDuplicate = 3;

    private $_bulkTotalCnt = 0;
    private $_bulkCnt = 0;
    private $_bulkData = [];
    private $_classEntity;
    private $_classEntityColumns;
    // private $_keysName = ['id'];
    // private $_presentsExistsClosure;
    private $_bulkSize = 1000;
    private $_modeInsert = self::C_modeInsertReplace;
    private $_updateOnDuplicateColumns;


    public function __construct(AbstractModel $classEntity)
    {
        $this->_classEntity = $classEntity;
        $this->_classEntityColumns = $this->_classEntity->attributes();
    }


    public function setModeInsertNormal()
    {
        $this->_modeInsert = self::C_modeInsertNormal;
    }


    public function setModeInsertIgnore()
    {
        $this->_modeInsert = self::C_modeInsertIgnore;
    }


    public function setModeInsertReplace()
    {
        $this->_modeInsert = self::C_modeInsertReplace;
    }


    public function setModeInsertUpdateOnDuplicate(array $updateOnDuplicateColumns)
    {
        $this->_modeInsert = self::C_modeInsertUpdateOnDuplicate;
        $this->_updateOnDuplicateColumns = $updateOnDuplicateColumns;
    }


    public function setBulkSize(int $bulkSize)
    {
        $this->_bulkSize = $bulkSize;
    }


    public function addOrBulk(array $data): bool
    {
        $this->add($data);

        // do bulk
        if ($this->isTimeTodoBulk()) {
            $this->_doBulk();
            return true;
        }

        return false;
    }


    public function add(array $data)
    {
        $classEntity = $this->_classEntity;
        $classEntity->attributes = $data;
        if (!$classEntity->validate()) {
            $classEntity::raiseException($classEntity);
        }

        $dataByColumn = [];
        foreach ($this->_classEntityColumns as $column) {
            if (array_key_exists($column, $data)) {
                $dataByColumn[$column] = $data[$column];
            }
        }

        $this->_bulkData[] = $dataByColumn;
        $this->_bulkCnt++;
        $this->_bulkTotalCnt++;

        $threshold = $this->_bulkSize * 2;
        if ($this->_bulkCnt > $threshold) {
            throw new ExceptionModel(sprintf('bulkCnt > _bulkSize. %d > %d You have to use addOrBulk() to add data or run doBulk() manually.', $this->_bulkCnt, $threshold));
        }
    }


    public function doBulk(): bool
    {
        return $this->_doBulk();
    }


    public function doLast(): bool
    {
        return $this->_doBulk();
    }


    public function isTimeTodoBulk(): bool
    {
        return ($this->_bulkCnt >= $this->_bulkSize);
    }


    public function attributes(): array
    {
        return array_keys(reset($bulkData));
    }


    protected function _doBulk(): bool
    {
        if (!empty($this->_bulkData)) {
            $bulkData = $this->_bulkData;
            $className = $this->_classEntity;

            $columns = array_keys(reset($bulkData));
            $sql = \Yii::$app->db->createCommand()->batchInsert(
                $className::tableName(),
                $columns,
                $bulkData)
                ->getRawSql();

            // hack
            if ($this->_modeInsert == self::C_modeInsertReplace) {
                $sql = str_replace('INSERT INTO ', 'REPLACE INTO ', $sql);
            } elseif ($this->_modeInsert == self::C_modeInsertIgnore) {
                $sql = str_replace('INSERT INTO ', 'INSERT IGNORE INTO ', $sql);
            } elseif ($this->_modeInsert == self::C_modeInsertUpdateOnDuplicate) {
                $updatingColumns = [];
                foreach ($this->_updateOnDuplicateColumns as $column) {
                    $updatingColumns[] = sprintf('%s=VALUES(%s)', $column, $column);
                }

                $sql = sprintf('%s ON DUPLICATE KEY UPDATE %s', $sql, join(', ', $updatingColumns));
            }

            \Yii::$app->db->createCommand($sql)
                ->execute();

            $this->_bulkData = [];
            $this->_bulkCnt = 0;

            return true;
        }

        return false;
    }


    public function getTotalCnt(): int
    {
        return $this->_bulkTotalCnt;
    }
}


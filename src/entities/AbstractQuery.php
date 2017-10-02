<?php
/**
 * Created by PhpStorm.
 * User: heman
 * Date: 21.06.17
 * Time: 14:55
 */

namespace bezdelnique\yii2app\entities;


use yii\db\ActiveQuery;

abstract class AbstractQuery extends ActiveQuery
{
    /**
     * AbstractQuery constructor.
     * Setup default SELECT value
     *
     * @param string $modelClass
     * @param array $config
     */
    public function __construct($modelClass, $config = [])
    {
        parent::__construct($modelClass, $config);
        $this->select($modelClass::tableName() . '.*');
    }


    public function orderByRemove()
    {
        $this->orderBy = null;
        return $this;
    }


    public function selectReset()
    {
        $this->select = null;
        return $this;
    }


    /**
     * @return string
     */
    public function getModelTableName()
    {
        return $this->modelClass::tableName();
    }


    public function andWhereId(int $id)
    {
        return $this->_andWhereByField('id', $id);
    }


    public function andWhereIds(array $ids)
    {
        return $this->_andWhereByField('id', $ids);
    }


    public function selectMaxCreatedAt()
    {
        return $this->select('max(' . $this->getModelTableName() . '.createdAt)');
    }


    protected function _andWhereByField($fieldName, $values)
    {
        return $this->andWhere([$this->getModelTableName() . '.' . $fieldName => $values]);
    }


    protected function _selectByField($fieldName)
    {
        return $this->addSelect($this->getModelTableName() . '.' . $fieldName);
    }
}



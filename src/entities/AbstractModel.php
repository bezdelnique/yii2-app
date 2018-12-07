<?php
/**
 * Created by PhpStorm.
 * User: heman
 * Date: 21.06.17
 * Time: 14:54
 */

namespace bezdelnique\yii2app\entities;


use bezdelnique\yii2app\components\IUrlManager;
use yii\helpers\VarDumper;

class AbstractModel extends \yii\db\ActiveRecord
{
    private $_urlManager;


    public function getId(): int
    {
        return $this->id;
    }


    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }


    protected function _getUrlManager(): IUrlManager
    {
        if (is_null($this->_urlManager) == true) {
            throw new ExceptionModel('urlManager must be set for entity with id: ' . $this->getId() . '. Use event attachment on application boostrap stage. Another reason manual Entity creation.');
        }

        return $this->_urlManager;
    }


    public function setUrlManager(IUrlManager $urlManager)
    {
        $this->_urlManager = $urlManager;
    }


    public function saveOrRaiseException($runValidation = true, $attributeNames = null)
    {
        if (parent::save($runValidation, $attributeNames)) {
            return true;
        }

        static::raiseException($this);
        return false;
    }


    /*
    public function saveOrLogWarning($runValidation = true, $attributeNames = null)
    {
        if (parent::save($runValidation, $attributeNames)) {
            return true;
        }

        App::log()->warn(self::getValidationErrorsAsString($this));
        return false;
    }
    */


    public static function getValidationErrorsAsString($model): string
    {
        return str_replace("\n", ' ', VarDumper::export($model->getErrors()));
    }


    /**
     * @param $model AbstractModel
     * @throws ExceptionModel
     */
    public static function raiseException($model)
    {
        throw new ExceptionModel(sprintf('Model validation error occupied: %s.', static::getValidationErrorsAsString($model)));
    }


    public function classNameShort()
    {
        return (new \ReflectionClass($this))->getShortName();
    }
}


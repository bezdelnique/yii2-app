<?php
/**
 * Created by PhpStorm.
 * User: heman
 * Date: 21.06.17
 * Time: 14:54
 */

namespace bezdelnique\yii2app\entities;



use bezdelnique\yii2app\components\IUrlManager;

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
}


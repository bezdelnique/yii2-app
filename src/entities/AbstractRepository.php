<?php
/**
 * Created by PhpStorm.
 * User: heman
 * Date: 21.06.17
 * Time: 14:54
 */

namespace bezdelnique\yii2app\entities;


/**
 * Class AbstractRepository
 * - common Repository methods: getAll(), getById()
 * - cache implementation
 */
abstract class AbstractRepository
{
    const ENTITIES_PER_PAGE = 20;


    /**
     * @return AbstractQuery
     */
    abstract protected function _getModelFinder();


    /**
     * @var AbstractRepositoryCacher
     */
    protected $_cacher = null;



    /***************************************************************
     * cacher
     ***************************************************************/


    public function setCacher(InterfaceRepositoryCacher $cacher)
    {
        $this->_cacher = $cacher;
    }


    /**
     * @return AbstractRepositoryCacher
     */
    public function getCacher()
    {
        if (is_null($this->_cacher) == true) {
            throw new ExceptionRepository('cacher must be set. User setCacher().');
        }

        return $this->_cacher;
    }


    private function isSetCacher(): bool
    {
        return (!is_null($this->_cacher));
    }


    /***************************************************************
     * Общие методы
     ***************************************************************/
    public function getAll($cacher = true)
    {
        if ($cacher == true) {
            $entities = $this->getCacher()->getEntitiesAll();
        } else {
            $entities = $this->_getModelFinder()->all();
        }

        return $entities;
    }


    public function getById(int $id, $cacher = true)
    {
        if ($cacher == true) {
            $entity = $this->getCacher()->getEntityById($id);
        } else {
            $entity = $this->_getModelFinder()->andWhereId($id)->limit(1)->one();
        }

        if (empty($entity) == true) {
            throw new ExceptionRepository('Entity can not be empty. Use hasId() for check.');
        }

        return $entity;
    }


    public function hasId(int $id, $cacher = true)
    {
        if ($cacher == true) {
            $entity = $this->getCacher()->getEntityById($id);
        } else {
            $entity = $this->_getModelFinder()->andWhereId($id)->limit(1)->one();
        }

        if (empty($entity) == true) {
            return false;
        }

        return true;
    }


    public function getByIds(array $ids, $cacher = true)
    {
        if ($cacher == true) {
            $entities = $this->getCacher()->getEntitiesByIds($ids);
        } else {
            $entities = $this->_getModelFinder()->andWhereIds($ids)->all();
        }

        return $entities;
    }


    public function getMaxCreatedAt(): string
    {
        return $this->_getModelFinder()->selectMaxCreatedAt()->scalar();
    }


    public function countAll(): int
    {
        return $this->_getModelFinder()->count();
    }


    static public function getPerPage()
    {
        return self::ENTITIES_PER_PAGE;
    }
}


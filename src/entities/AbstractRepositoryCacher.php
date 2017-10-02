<?php
/**
 * Created by PhpStorm.
 * User: heman
 * Date: 29.06.17
 * Time: 21:49
 */


namespace bezdelnique\yii2app\entities;


class AbstractRepositoryCacher implements InterfaceRepositoryCacher
{
    /**
     * @var AbstractRepository
     */
    protected $_repository = null;
    protected $_fetched = false;


    /**
     * Class properties
     */
    protected $_cacheEntitiesById = [];
    protected $_cache = [];


    public function __construct(AbstractRepository $repository)
    {
        $repository->setCacher($this);
        $this->_repository = $repository;
    }


    protected function _getRepository(): AbstractRepository
    {
        return $this->_repository;
    }


    public function prefetch()
    {
        $this->_prefetch();
    }


    protected function _prefetch()
    {
        if ($this->_fetched == false) {
            $entities = $this->_repository->getAll($cacher = false);

            $this->_cacheEntitiesById = [];
            foreach ($entities as $entity) {
                $this->_cacheEntitiesById[$entity['id']] = $entity;
            }

            $this->_prefetchHook();

            $this->_fetched = true;
        }
    }


    protected function _prefetchHook()
    {
        // foreach ($this->_cacheEntitiesById as $entity) {
        // do something with entity
        // }
    }


    public function getEntitiesByIds(array $ids)
    {
        /**
         * Search ids to request from Db
         */
        $idsForQuery = [];
        foreach ($ids as $id) {
            if (isset($this->_cacheEntitiesById[$id]) == false) {
                $idsForQuery[] = $id;
            }
        }


        /**
         * Db query
         */
        if (false == empty($idsForQuery)) {
            $entitiesFromQuery = $this->_repository->getByIds($idsForQuery, $cacher = false);
            foreach ($entitiesFromQuery as $entity) {
                $this->_cacheEntitiesById[$entity->id] = $entity;
            }
        }


        /**
         * Return data from cache
         */
        $entities = [];
        foreach ($ids as $id) {
            if (isset($this->_cacheEntitiesById[$id]) == false) {
                throw new ExceptionRepository('Entity with id ' . $id . ' does not found in cache. Repository class: ' . get_class($this->_getRepository()) . '. Try to check SQL query and data in source table.');
            }

            $entities[] = $this->_cacheEntitiesById[$id];
        }


        return $entities;
    }


    public function getEntityById(int $id)
    {
        $entities = $this->getEntitiesByIds([$id]);
        if (false == empty($entities[0])) {
            return $entities[0];
        }

        return null;
    }


    public function getEntitiesAll()
    {
        $this->_prefetch();

        return $this->_cacheEntitiesById;
    }


    /****************************************************************************************************************
     * Universal cache
     ****************************************************************************************************************/


    public function set($namespace, $key, $value)
    {
        if (isset($this->_cache[$namespace][$key]) == true) {
            throw new ExceptionRepository('Element already defined in namespace: ' . $namespace . ', key: ' . $key . '. Repository class: ' . get_class($this->_getRepository()) . '.');
        }

        $this->_cache[$namespace][$key] = $value;
    }


    public function get($namespace, $key)
    {
        if (isset($this->_cache[$namespace][$key]) == false) {
            throw new ExceptionRepository('Element does not defined in namespace: ' . $namespace . ', key: ' . $key . '. Repository class: ' . get_class($this->_getRepository()) . '.');
        }

        return $this->_cache[$namespace][$key];
    }


    public function exists($namespace, $key)
    {
        if (isset($this->_cache[$namespace][$key]) == true) {
            return true;
        }

        return false;
    }
}


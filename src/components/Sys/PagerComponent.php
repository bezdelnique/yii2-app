<?php

namespace bezdelnique\yii2app\components\Sys;


use bezdelnique\yii2app\components\AbstractComponent;
use bezdelnique\yii2app\components\ExceptionComponent;


class PagerComponent extends AbstractComponent
{
    protected $_pages = 1;
    protected $_pagesWindow = 3;
    protected $_page = 1;
    protected $_url = null;


    public function __construct($pages, $page, $pagesWindow = 3)
    {
        $this->_pages = $pages;

        if ($page < 1) {
            $page = 1;
        } elseif ($page > $pages) {
            $page = $pages;
        }
        $this->_page = $page;
        $this->_pagesWindow = $pagesWindow;
    }


    public function hasPrevious(): bool
    {
        return ($this->_page > 1) ? true : false;
    }


    public function getPrevious(): int
    {
        if ($this->hasPrevious() == true) {
            return $this->_page - 1;
        }

        return 1;
    }


    public function hasNext(): bool
    {
        return (($this->_page + 1) <= $this->_pages) ? true : false;
    }


    public function getNext(): int
    {
        if ($this->hasNext() == true) {
            return $this->_page + 1;
        }

        return $this->_pages;
    }


    public function getPage(): int
    {
        return $this->_page;
    }


    public function getPages(): int
    {
        return $this->_pages;
    }


    public function getPagesWindowFrom(): int
    {
        $pagesWindowFrom = $this->_page - $this->_pagesWindow;
        if ($pagesWindowFrom < 1) {
            $pagesWindowFrom = 1;
        }

        return $pagesWindowFrom;
    }


    public function getPagesWindowTo(): int
    {
        $pagesWindowTo = $this->_page + $this->_pagesWindow;
        if ($pagesWindowTo > $this->_pages) {
            $pagesWindowTo = $this->_pages;
        }

        return $pagesWindowTo;
    }


    public function setUrl($urlPath, $qs, $pageParam = 'page')
    {
        $urlPath = rtrim(ltrim($urlPath, '/'), '/');

        unset($qs[$pageParam]);
        $q = http_build_query($qs);
        $this->_url = '/' . $urlPath . '?' . (!empty($q) ? $q . '&' : '') . $pageParam . '=';
    }


    public function getUrl(): string
    {
        if (is_null($this->_url) == true) {
            throw new ExceptionComponent('url can not be null. Use setUrl().');
        }

        return $this->_url;
    }
}


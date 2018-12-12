<?php
/**
 * Created by PhpStorm.
 * User: heman
 * Date: 12.12.2018
 * Time: 17:17
 */

namespace bezdelnique\yii2app\helpers\Sys;


use \yii\helpers\ArrayHelper as YiiArrayHelper;


class ArrayHelper
{
    static function toArray(array $objects, array $props): array
    {
        $data = [];
        foreach ($objects as $object) {
            $el = [];
            foreach ($props as $prop) {
                $el[$prop] = $object->{$prop};
            }

            $data[] = $el;
        }

        return $data;
    }


    static function indexBy(array $objects, string $prop): array
    {
        $data = [];
        foreach ($objects as $object) {
            $data[$object->{$prop}] = $object;
        }

        return $data;
    }


    static function parentBy(array $objects, string $prop): array
    {
        $data = [];
        foreach ($objects as $object) {
            $data[$object->{$prop}][] = $object;
        }

        return $data;
    }


    static function map(array $items): array
    {
        $data = [];
        foreach ($items as $item) {
            $data[$item] = $item;
        }

        return $data;
    }


    public static function getColumn($array, $name, $keepKeys = true)
    {
        $result = [];
        if ($keepKeys) {
            foreach ($array as $k => $element) {
                $result[$k] = static::getValue($element, $name);
            }
        } else {
            foreach ($array as $element) {
                $result[] = static::getValue($element, $name);
            }
        }

        return $result;
    }


    public static function getValue($array, $key, $default = null)
    {
        return YiiArrayHelper::getValue($array, $key, $default);
    }
}


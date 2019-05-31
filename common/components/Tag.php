<?php

namespace common\components;

class Tag
{
    public const TAG_TYPE_PIN = 'PIN';
    public const TAG_TYPE_GRAPHIC_CODE = 'GCODE';
    public const TAG_TYPE_NFC = 'NFC';
    public const TAG_TYPE_UHF = 'UHF';
    public const TAG_TYPE_DUMMY = 'DUMMY';


    /**
     * @param $type
     * @param $tagId
     * @return null|string
     */
    public static function getTag($type, $tagId)
    {
        $tagTypes = self::getTagTypes();

        if (in_array($type, $tagTypes)) {
            if ($type != self::TAG_TYPE_DUMMY && !empty($tagId)) {
                return $type . ':' . $tagId;
            } else if ($type == self::TAG_TYPE_DUMMY) {
                return $type . ':' . '';
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * @return array
     */
    private static function getTagTypes()
    {
        return [
            self::TAG_TYPE_PIN,
            self::TAG_TYPE_GRAPHIC_CODE,
            self::TAG_TYPE_NFC,
            self::TAG_TYPE_UHF,
            self::TAG_TYPE_DUMMY,
        ];
    }

    /**
     * @param $tag
     * @return null|string
     */
    public static function getTagId($tag)
    {
        if (preg_match('/([a-z]*):([a-z0-9-]*)/i', $tag, $match)) {
            return $match[2];
        } else {
            return null;
        }
    }

    /**
     * @param $tag
     * @return null|string
     */
    public static function getTagType($tag)
    {
        $tagTypes = self::getTagTypes();

        if (preg_match('/([a-z]*):([a-z0-9-]*)/i', $tag, $match)) {
            $type = $match[1];
            if (in_array($type, $tagTypes)) {
                return $type;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
}
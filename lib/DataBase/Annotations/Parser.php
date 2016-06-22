<?php

namespace DataBase\Annotations;
use DataBase\Annotations\Annotation;

class Parser{

    const REGEX_METADATA_TAG_LINE = '/^([ ]*\*[\s]?@).*/';
    const REGEX_METADATA_KEY_VALUE = '/(@[A-Za-z][A-Za-z\d]+)((\s)[A-Za-z\d_-]+)?/';

    protected static function getTags($input){

        $tagsData = array();
        $lines = array();
        $lines = preg_grep(self::REGEX_METADATA_TAG_LINE, explode("\n", $input));

        foreach ($lines as $line) {
            $tagMatched = array();
            preg_match(self::REGEX_METADATA_KEY_VALUE, $line, $tagMatched);
            $tagMatched = preg_split('/[\s]/', $tagMatched[0]);
            $tagsData[strtolower(explode('@', $tagMatched[0])[1])] = isset($tagMatched[1]) ? $tagMatched[1] : true;
        }

        return $tagsData;
    }

    public static function getAnnotations(\Reflector $reflection){
        $docComment = $reflection->getDocComment();
        if($docComment === false) return;
        return new Annotation(self::getTags($docComment));
    }

    public static function getAnnotationsArray($reflections){
        if(!is_array($reflections)) throw new \Exception("O parâmetro deve ser um array.", 1);

        $annotations = array();
        foreach ($reflections as $reflection) {
            if($reflection instanceof \Reflector)
                $annotations[$reflection->name] = self::getAnnotations($reflection);
        }

        return $annotations;
    }
}

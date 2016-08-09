<?php
namespace Application;

class TemplateConfig {

    protected $title;
    protected $layoutPath;
    protected $noTemplate;
    protected $charset;
    protected $contentName;

    public function __construct($config){
        if(isset($config['defaultTemplate'])) $this->setLayoutPath($config['defaultTemplate']);
        if(isset($config['charset'])) $this->setCharset($config['charset']);
        if(isset($config['contentName'])) $this->setContentName($config['contentName']);
        if(isset($config['noTemplate'])) $this->setNoTemplate($config['noTemplate']);
        if(isset($config['title'])) $this->setTitle($config['title']);
    }

    public function getTitle(){
        return $this->title;
    }

    public function setTitle($title){
        $this->title = $title;
        return $this;
    }

    public function getLayoutPath(){
        return $this->layoutPath;
    }

    public function setLayoutPath($layoutPath){
        $this->layoutPath = $layoutPath;
        return $this;
    }

    public function getNoTemplate(){
        return $this->noTemplate;
    }

    public function setNoTemplate($noTemplate){
        $this->noTemplate = $noTemplate;
        return $this;
    }

    public function getCharset(){
        return $this->charset;
    }

    public function setCharset($charset){
        $this->charset = $charset;
        return $this;
    }

    public function getContentName(){
        return $this->contentName;
    }

    public function setContentName($contentName){
        $this->contentName = $contentName;
        return $this;
    }
}

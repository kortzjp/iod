<?php

Class Template {

    function __construct($str) {
        $this->template = $str;
    }

    function render($dict) {
        $this->set_dict($dict);
        return str_replace(array_keys($dict), array_values($dict), $this->template);
    }

    function render_regex($dict = array(), $id = '') {
        $regex = "#<!--$id-->([\s\S]*)<!--$id-->#";
        preg_match($regex, $this->template, $matches);

        $strorig = $this->template;
        $this->template = $matches[0];
        $render = '';
          foreach ($dict as $possible_obj) {
          $render .= $this->render($possible_obj);
          }

          $render = str_replace("<!--$id-->", '', $render);

          return str_replace($this->template, $render, $strorig);
        
    }

    private function sanear_diccionario(&$dict) {
        settype($dict, 'array');
        $dict2 = $dict;
        foreach ($dict2 as $key => $value) {
            if (is_object($value) or is_array($value)) {
                unset($dict[$key]);
            }
        }
    }

    private function set_dict(&$dict) {
        $this->sanear_diccionario($dict);
        $dict2 = $dict;
        foreach ($dict2 as $key => $value) {
            $dict["{{$key}}"] = $value;
            unset($dict[$key]);
        }
    }

    function show($titulo, $contenido) {
        $this->template = file_get_contents(STATIC_DIR . 'html/template.html');
        $dict = array('title' => $titulo, 'body' => $contenido);
        return $this->render($dict);
    }

}

<?php

/*
Copyright 2015 Weldon Sams

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

   http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/

/**
 * WsTmpl is a simple templating engine.
 *
 * Create a template file such as the following and name it for example my.tmpl.
 *
 * <html><head><title>;:site_title:;</title></head><body>;:my_body:;</body></html>
 *
 * The example above has two variables: ;:site_title:; and ;:my_body:;
 *
 * Add them to an array,
 *
 * $tmpl = array("site_title"=>"This is my site title", "my_body"=>"This is the body");
 *
 * Create an instances of WsTmpl,
 *
 * $t = new WsTmpl();
 *
 * Set the template and data array,
 *
 * $t->setFile("path/to/my.tmpl");
 * $t->setData($tmpl);
 *
 * To compile your template,
 *
 * $html = $t->compile();
 */
class WsTmpl {
    private $file;
    private $data;
    private $stripComments = true;
    protected $html;

    public function __construct($file = null, $data = null) {
        $this->setFile($file);
        $this->setData($data);
    }

    public function setFile($file) {
        $this->file = $file;
    }

    public function getFile() {
        return $this->file;
    }

    public function setData($data) {
        $this->data = $data;
    }

    public function getData() {
        return $this->data;
    }

    public function addData($data) {
        foreach ($data as $k=>$v) {
            $this->data[$k] = $v;
        }
    }

    public function setStripComments($stripComments) {
        $this->stripComments = $stripComments;
    }

    public function getStripComments() {
        return $this->stripComments;
    }

    public function getHtml() {
        return $this->html;
    }

    public function compile() {
        $f = file($this->file);

        foreach ($f as $k => $line) {
            $f[$k] = rtrim($f[$k], "\n");

            foreach ($this->data as $var => $val) {
                if (strpos($f[$k], ";:{$var}:;")) {
                    $f[$k] = str_replace(";:{$var}:;", $val, $f[$k]);
                } else {
                    $f[$k] = $f[$k];
                }
            }
                    
            // Set variables not entered to nothing so they're not shown in the HTML.
            if (preg_match("/;:.*:;/", $f[$k])) {
                $f[$k] = preg_replace("/;:.*?:;/", "", $f[$k]);
            }
        }

        $this->html = implode("\n", $f);

        return $this->html;
    }
}

<?php

namespace Pachel\EasyFrameWork;

class Draw extends Prefab
{
    private static array $vars;

    private array $contents;

    public static function template(string $template)
    {
        $caller = debug_backtrace()[1];
        $ui = Base::instance()->env("APP.views");
        if (!is_file($ui . $template)) {
            throw new \Exception("Template not found: " . $ui . $template);
        }
        $route = Routing::matchroute();
        if ($route["object"][0] != $caller["class"] || $route["object"][1] != $caller["function"]) {
            throw new \Exception("Permission denied!");
        }
        self::$vars["template"] = $ui . $template;
    }

    public function generate()
    {
        if(!isset(self::$vars["template"]) || empty(self::$vars["template"])){
            return;
        }
        $path = self::$vars["template"];
        $content = file_get_contents($path);
        $this->replace_variables($content);
        $this->run_content($content);
        $this->cut_template($content);

        $haslayout = $this->haslayout($content, $layout);

        $this->show($haslayout, $layout);

    }

    public function show($haslayout, $layout)
    {
        if ($haslayout) {
            $layoutcontent = file_get_contents(Base::instance()->env("APP.VIEWS") . $layout);
            $this->replace_variables($layoutcontent);
            $this->run_content($layoutcontent);
            foreach ($this->contents as $content) {
                $layoutcontent = preg_replace("/\{\{\\$".$content["name"]."\}\}/i",$content["content"],$layoutcontent);
            }
            echo $layoutcontent;
        } else {
            foreach ($this->contents as $content) {
                echo $content["content"];
            }
        }
    }

    private function run_content(&$content)
    {
        $vars = Base::instance()->env(null);
        foreach ($vars as $name => $value) {
            $$name = $value;
        }
        unset($vars);
        ob_start();
        eval("?>" . $content . "<?php");
        $content = ob_get_clean();
    }

    private function replace_variables(&$content)
    {
        if (preg_match_all("/\{\{([^\$\}]+)\}\}/", $content, $preg)) {
            foreach ($preg[1] as $index => $varname) {
                $content = str_replace($preg[0][$index], Base::instance()->env($varname), $content);
            }
        }
    }

    private function haslayout($content, &$layout = null)
    {
        if (preg_match("/<!\-\-layout:(.+)\-\->/i", $content, $preg)) {
            $layout = $preg[1];
            if (!is_file(Base::instance()->env("APP.VIEWS") . $layout)) {
                throw new \Exception("Layout not exists: " . Base::instance()->env("APP.VIEWS") . $layout);
            }
            return true;
        }
        return false;
    }

    private function cut_template(&$content)
    {
        if (preg_match_all("~@([a-z_0-9\-]+):(.+)(:[a-z]+@)~misU", $content, $preg, PREG_PATTERN_ORDER)) {
            foreach ($preg[1] as $index => $item) {
                $name = strtoupper($item);
                $this->contents[] = [
                    "name" => $name,
                    "content" => $preg[2][$index]
                ];
            }
        }
    }
}


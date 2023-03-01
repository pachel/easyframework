<?php

namespace Pachel\EasyFrameWork;

use Pachel\EasyFrameWork\Messages;
use PHPUnit\Framework\Warning;

class Draw extends Prefab
{
    private static array $vars;

    private array $contents;

    private $layout;
    public function view(string $view){
        $viewpath = Base::instance()->env("APP.views") . $view;
        if (!is_file($viewpath)) {
            throw new \Exception(Messages::DRAW_TEMPLATE_NOT_FOUND);
        }
        $content = file_get_contents($viewpath);
        $this->set_layout($content);

        self::$vars["template"] = $viewpath;
    }
    private function set_layout($content):void{
        if (preg_match("/<!\-\-layout:(.+)\-\->/i", $content, $preg)) {
            if (!is_file(Base::instance()->env("APP.VIEWS") . $preg[1])) {
                throw new \Exception("Layout not exists: " . Base::instance()->env("APP.VIEWS") . $preg[1]);
            }
            $this->layout = Base::instance()->env("APP.VIEWS").$preg[1];
        }
    }
    private function get_view_info(string $viewpath){

    }
    public static function template(string $template)
    {
        $ui = Base::instance()->env("APP.views");
        if (!is_file($ui . $template)) {
            throw new \Exception(Messages::DRAW_TEMPLATE_NOT_FOUND);
        }
        //$route = Routing::matchroute();
        self::$vars["template"] = $ui . $template;
      //  self::set_layoutscript($template);
    }
    private static function check_layoutscript(string $template){
        $path = self::$vars["template"];
        $content = file_get_contents($path);
        if(self::haslayout($content,$layout)){

        }

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

      //  $haslayout = $this->haslayout($content, $layout);
        $this->set_layout($content);
        $this->show();

    }

    public function show()
    {
        if (!empty($this->layout)) {
            $layoutcontent = file_get_contents($this->layout);
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
        extract($vars);


            ob_start();
            eval("?>" . $content . "<?php");
            $content = ob_get_clean();
        //$this->contents[] = $content;



    }

    private function replace_variables(&$content)
    {
        if (preg_match_all("/\{\{([^\$\}]+)\}\}/", $content, $preg)) {
            foreach ($preg[1] as $index => $varname) {
                $variable = Base::instance()->env($varname);
                $content = str_replace($preg[0][$index], $variable, $content);
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


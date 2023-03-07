<?php

namespace Pachel\EasyFrameWork;

final class View
{

    private partObjects $parts;

    public function __construct(Routes &$routes)
    {
        $this->parts = new partObjects();
        /**
         * @var Routes $templates ;
         */
        $templates = $routes->find("template")->notequal("")->get();

        if (count($templates)==0) {
            $directs = $routes->find("direct")->notequal("")->get();
            if (!empty($directs)) {
                $this->generate_direct_content($directs);
            }

            return;
        }

        $this->set_content($templates[0]->template, $templates[0]->layout);
    }

    /**
     * @param Route[] $directs
     * @return void
     */
    private function generate_direct_content(&$directs)
    {
        foreach ($directs as $direct) {
            if ($direct->direct == "json") {
                $data = [
                    "content" => json_encode($direct->return,JSON_PRETTY_PRINT),
                    "part_name" => "NOTPART",
                    "content_type" => "Content-Type: application/json; charset=utf-8"
                ];
                $this->parts->push($data);
            }
        }
        //print_r($directs);
    }

    public function show()
    {
        /**
         * @var partObjectsItem $one
         * @var partObjectsItem $part
         */

        /**
         * Ha nincs feldolgozandó template, akkor nem megyünk tovább
         */

        if ($this->parts->count() == 0) {
            return false;
        }
        $one = $this->parts->find("part_name")->equal("NOTPART")->get();
        $two = $this->parts->find("part_name")->notequal("NOTPART")->get();
        //$content = $one[0]["content"];
        $one = $one[0];
        //print_r($this->parts);
        $this->parts->reset();
        foreach ($two as $part) {
            $one->content = preg_replace("/\{\{\\$" . $part->part_name . "\}\}/i", (is_null($part->content)?"null":$part->content), (is_null($one->content)?"null":$one->content));
        }
        $this->content_with_header($one);
        return true;
    }

    /**
     * @param partObjectsItem $content
     * @return void
     */
    private function content_with_header(&$content){
        if(is_null($content->content_type)){
            header('Content-Type:text/html; charset=UTF-8');
        }
        else{
            header($content->content_type);
            header("Content-Disposition:inline;filename=generated_".time().".json");
        }
        echo $content->content;
    }

    private function set_content($template, $layout = null)
    {

        $content = file_get_contents($template);
        $this->replace_variables($content);
        $this->run_content($content);
        $this->cut_content($content, $template, $layout);

        if (!empty($layout)) {
            $this->set_content($layout);
        }
    }

    private function cut_content(&$content, $template, $layout = null)
    {

        if (preg_match_all("~@([a-z_0-9\-]+):(.+)(:[a-z]+@)~misU", $content, $preg, PREG_PATTERN_ORDER)) {
            foreach ($preg[1] as $index => $item) {
                $part = new partObjectsItem();
                $part->part_name = strtoupper($item);
                $part->content = $preg[2][$index];
                $part->layout = $layout;
                $part->template = $template;
                $part->content_type = "Content-Type: text/html; charset=UTF-8";
                $this->parts->push($part);
            }
        } else {
            $part = new partObjectsItem();
            $part->part_name = "NOTPART";
            $part->content_type = "Content-Type: text/html; charset=UTF-8";
            $part->content = $content;
            $part->layout = $layout;
            $part->template = $template;
            $this->parts->push($part);
        }
        //print_r($this->parts);
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

    private function run_content(&$content)
    {
        $vars = Base::instance()->env(null);
        extract($vars);

        /**
         * A lezáratlan php tegek lezárása
         */
        if(preg_match_all("/(<\?)|(\?>)/misU",$content,$preg)){
            if(count($preg[0])%2 != 0){
                $content.="?>";
            }
        }
        //TODO:code tagek beépítése
        eval("?>".$content."<?php");
        $content = ob_get_clean();
        ob_start();
    }
}

final class partObjects extends ListObject
{
    protected $class = partObjectsItem::class;
}

/**
 * @property string $part_name;
 * @property string $template;
 * @property string $layout;
 * @property string $content;
 * @property string $content_type;
 */
final class partObjectsItem extends ListObjectItem
{

}
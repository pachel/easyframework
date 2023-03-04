<?php
namespace Pachel\EasyFrameWork;

final class View{

    private partObjects $parts;
    public function __construct(Routes &$routes)
    {
        /**
         * @var Routes $templates;
         */
        $templates = $routes->find("template")->notequal("")->get();
        if(empty($templates)){
            return;
        }
        $this->parts = new partObjects();
        $this->set_content($templates[0]->template,$templates[0]->layout);
    }
    public function show(){
        /**
         * @var partObjectsItem $one
         * @var partObjectsItem $part
         */
        if(empty($this->parts)){
            return null;
        }
        $one = $this->parts->find("part_name")->equal("NOTPART")->get();
        $two = $this->parts->find("part_name")->notequal("NOTPART")->get();
        $content = $one[0]["content"];
        unset($one);
        $this->parts->reset();
        foreach ($two AS $part){
            $content = preg_replace("/\{\{\\$".$part->part_name."\}\}/i",$part->content,$content);
        }
        return $content;
    }
    private function set_content($template,$layout = null){

        $content = file_get_contents($template);
        $this->replace_variables($content);
        $this->run_content($content);
        $this->cut_content($content,$template,$layout);

        if(!empty($layout)){
            $this->set_content($layout);
        }
    }
    private function cut_content(&$content,$template,$layout = null)
    {

        if (preg_match_all("~@([a-z_0-9\-]+):(.+)(:[a-z]+@)~misU", $content, $preg, PREG_PATTERN_ORDER)) {
            foreach ($preg[1] as $index => $item) {
                $part = new partObjectsItem();
                $part->part_name = strtoupper($item);
                $part->content = $preg[2][$index];
                $part->layout = $layout;
                $part->template = $template;
                $this->parts->push($part);
            }
        }
        else{
            $part = new partObjectsItem();
            $part->part_name = "NOTPART";
            $part->content = $content;
            $part->layout = $layout;
            $part->template = $template;
            $this->parts->push($part);
        }
        //print_r($this->parts);
    }
    private function replace_variables(&$content){
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
        ob_start();
        eval("?>" . $content . "<?php");
        $content = ob_get_clean();
     }
}
final class partObjects extends ListObject{
    protected $class = partObjectsItem::class;
}

/**
 * @property string $part_name;
 * @property string $template;
 * @property string $layout;
 * @property string $content;
 */
final class partObjectsItem extends ListObjectItem
{

}
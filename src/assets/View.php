<?php

namespace Pachel\EasyFrameWork;

use JetBrains\PhpStorm\Deprecated;

final class View
{

    private partObjects $parts;
    protected bool $auth = false;

    public function __construct(Routes &$routes)
    {
        $this->parts = new partObjects();
        /**
         * @var Routes $templates ;
         */
        $templates = $routes->find("template")->notequal("")->object();
        if ($templates->count() == 0) {
            $directs = $routes->find("direct")->notequal("")->get();
            if (count($directs)>0) {
                $this->generate_direct_content($directs);
            }
            return;
        }
        $this->checkViews($templates);
       //$this->set_content($templates[0]->template, $templates[0]->layout);
    }

    /**
     * @param Route[] $template
     * @return array
     */
    protected function checkViews(&$templates)
    {
        foreach ($templates AS $template) {

            if(Auth::instance()->is_authorised($template)){
                Base::instance()->run_content($template);
            }
            else{
                continue;
            }
            //$this->auth = true;*/
            $content = file_get_contents($template->template);
            //A layout hivatkozást ki kell venni a tartalomból, mert már nem kell
            $content = preg_replace("/<!\-\-.*\[layout:.+\].*\-\->/i", "", $content);
            //Változókat bele kell tenni
            //Lefuttatjuk a templatet, hogy a betöltött tartalmak ne kavarjanak be
            $this->run_content($content, $template->template,$template);

            /**
             * HA be betölteni való tartalom, akkor azt ide betöltjük!
             */
            $this->hasFileToLoad($content);
            $this->replace_variables($content);
            /**
             * Ha nincs layout akkor nincs mit tenni
             */
            if ($template->layout == "") {
                $part = new partObjectsItem();
                $part->part_name = "NOTPART";
                $part->content = $content;
                $part->layout = "";
                $part->template = $template->template;
                $this->parts->push($part);
            } /**
             * HA van layout akkor fel kell szabdalni a cuccot
             */
            else {
                /**
                 * Szétszedjük a tartalmat
                 */
                $this->cut_content($content, $template->template, $template->layout, $template->name);
                /**
                 * Innentől a layouttal dolgozunk
                 */
                $content = file_get_contents($template->layout);
                $this->replace_variables($content);
                $this->hasFileToLoad($content);
                $this->run_content($content, $template->layout,$template);
                $part = new partObjectsItem();
                $part->part_name = "NOTPART";
                $part->content = $content;
                $part->layout = "";
                $part->template = $template->layout;
                $this->parts->push($part);
            }
        }
    }
    private function hasFileToLoad(&$content){
        $viewsDir = Base::instance()->env("app.views");
        if(preg_match_all("/<!\-\-.*\[load:([a-z0-9_\-\/\.]+)\].*\-\->/i",$content,$preg)){
            foreach ($preg[1] AS $index => $value){
                if(!file_exists($viewsDir.$value)){
                    $content = str_replace($preg[0][$index],"<!--ERROR - file is not exists: ".$value."-->",$content);
                    continue;
                }
                $load_content = file_get_contents($viewsDir.$value);
                $this->run_content($load_content,$value);
                $name = preg_replace("/(.+)\.[^\.]+$/","$1",basename($value));
                $content = str_replace($preg[0][$index],"<!--".$name."-->\n".$load_content."\n<!--end of ".$name."-->\n",$content);
            }
        }
    }
    /**
     * @param Route[] $directs
     * @return void
     */
    private function generate_direct_content(&$directs)
    {
        foreach ($directs as $direct) {
            if(!Auth::instance()->is_authorised($direct)){
                $this->auth = false;
                continue;
            }
            $this->auth = true;

            if ($direct->direct == "json") {
                $data = [
                    "content" => json_encode($direct->return, JSON_PRETTY_PRINT),
                    "part_name" => "NOTPART",
                    "content_type" => "Content-Type: application/json; charset=utf-8"
                ];
                $this->parts->push($data);
            }
        }
        //print_r($directs);
    }
    public function show2()
    {
        /**
         * Ha nincs part és van templatet tartalmazó route, akkor bizony nincs hozzáférésünk az oldalhoz
         */
        if ($this->parts->count() == 0 && Base::instance()->routes->find("template")->notequal("")->count() == 0) {
            return false;
        }
        if ($this->parts->count() == 0) {
            Base::instance()->send_error(403);
        }

        $one = $this->parts->find("part_name")->equal("NOTPART")->get();
        $two = $this->parts->find("part_name")->notequal("NOTPART")->get();
        //$content = $one[0]["content"];
        $one = $one[0];
        $this->parts->reset();
        foreach ($two as $part) {
//            echo $part->part_name."\n";
            $one->content = preg_replace("/\{\{\\$" . $part->part_name . "\}\}/i", (is_null($part->content) ? "null" : $part->content), (is_null($one->content) ? "null" : $one->content));
            $one->content = preg_replace("/<!\-\-.*\[content:" . $part->part_name . "\].*\-\->/i", "<!--".$part->part_name."-->".(is_null($part->content) ? "null" : $part->content)."\n<!--end of ".$part->part_name."-->", (is_null($one->content) ? "null" : $one->content));
        }

        $this->content_with_header($one);

        return true;
    }
    #[Deprecated]
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
        $this->parts->reset();
        foreach ($two as $part) {
//            echo $part->part_name."\n";
            $one->content = preg_replace("/\{\{\\$" . $part->part_name . "\}\}/i", (is_null($part->content) ? "null" : $part->content), (is_null($one->content) ? "null" : $one->content));
            $one->content = preg_replace("/<!\-\-.*\[content:" . $part->part_name . "\].*\-\->/i", "<!--".$part->part_name."-->".(is_null($part->content) ? "null" : $part->content)."\n<!--end of ".$part->part_name."-->", (is_null($one->content) ? "null" : $one->content));
        }

        $this->content_with_header($one);
        return true;
    }

    /**
     * @param partObjectsItem $content
     * @return void
     */
    private function content_with_header(&$content)
    {
        if (is_null($content->content_type)) {
            header('Content-Type:text/html; charset=UTF-8');
        } else {
            header($content->content_type);
            header("Content-Disposition:inline;filename=generated_" . time() . ".json");
        }
        echo $content->content;
        if(defined("START_EFW")) {
            $timelog = Base::instance()->env("app.temp") . "timelog.log";
            $line = date("Y-m-d H:i:s") . " " . round(microtime(true) - START_EFW, 3) . " " . Base::instance()->env("server.request_uri") . "\n";
            file_put_contents($timelog, $line, FILE_APPEND);
        }
    }

    #[Deprecated]
    private function set_content($template, $layout = null)
    {

        $content = file_get_contents($template);
        $this->replace_variables($content);
        $this->run_content($content, $template);
        $this->cut_content($content, $template, $layout);

        if (!empty($layout)) {
            $this->set_content($layout);
        }
    }

    private function cut_content(&$content, $template, $layout = null,$name = null)
    {

        if(!is_null($name)){
            $part = new partObjectsItem();
            $part->part_name = strtoupper($name);
            $part->content = $content;
            $part->layout = $layout;
            $part->template = $template;
            $part->content_type = "Content-Type: text/html; charset=UTF-8";
            $this->parts->push($part);
        }
        elseif (preg_match_all("~@([a-z_0-9\-]+):(.+)(:[a-z]+@)~misU", $content, $preg, PREG_PATTERN_ORDER)) {
            foreach ($preg[1] as $index => $item) {
                $part = new partObjectsItem();
                $part->part_name = strtoupper($item);
                $part->content = $preg[2][$index];
                $part->layout = $layout;
                $part->template = $template;
                $part->content_type = "Content-Type: text/html; charset=UTF-8";
                $this->parts->push($part);
            }
        }elseif (preg_match_all("/<!\-\-\[name:(.+)\]\-\->(.+)/misU",$content,$preg)){
            $splitted = "";
            for ($index = count($preg[0])-1;$index>=0;$index--){
                $part = new partObjectsItem();
                $part->part_name = strtoupper($preg[1][$index]);
                $part->template = $template;
                $part->layout = $layout;
                $part->content_type = "Content-Type: text/html; charset=UTF-8";
                $splitted = explode($preg[0][$index],(is_array($splitted)?$splitted[0]:$content));
                $part->content = $splitted[1].($index==count($preg[0])-1?"\n":"");
                $this->parts->push($part);
            }

        }
        else {
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
    public static function run_template(string $template):string{
        $template = Base::instance()->env("app.views").$template;
        if(!file_exists($template)){
            throw new \Exception("A template nem létezik: ".$template);
        }
        $routes = new Routes();
        $View = new View($routes);
        $content = file_get_contents($template);
        $View->run_content($content);
        return $content;
    }
    private function run_content(&$content, $template = null,Route $route = null)
    {




        $vars = Base::instance()->env(null);
        extract($vars);
        /**
         * A lezáratlan php tegek lezárása
         */
        if (preg_match_all("/(<\?)|(\?>)/misU", $content, $preg)) {
            if (count($preg[0]) % 2 != 0) {
                $content .= "?>";
            }
        }
        error_reporting(E_ERROR);
        if (preg_match("/.+?\.php/i", $template)) {
            eval("?>" . $content . "<?php");
            $content = ob_get_clean();
        } else {

        }
        error_reporting(E_ALL);
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
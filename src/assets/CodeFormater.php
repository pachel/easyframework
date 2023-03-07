<?php

namespace Pachel\EasyFrameWork;

class CodeFormater
{
    protected const styles = [
        "variables" => "cf-variable",
        "comment" => "cf-comment",
        "comment_b" => "cf-comment-line",
        "constances" => "cf-const",
        "functions" => "cf-functions",
        "types" => "cf-type",
        "texts" => "cf-texts",
    ];
    protected const types = ["function","array","string","int","bool","double","object","const","protected","private","public","class","train","extentds","abstract","final","try","catch","foreach","for","while","__construct"];

    public function php(string $code):string
    {
        /*
        $code = str_replace("\"","#",$code);
        $code = preg_replace("/([ \t])(public|function)([ \t])/i","$1<span class=\"".self::styles["types"]."\">$2</span>$3",$code);
        $code = preg_replace("/(\\$[a-z]+?)/misU","<span class=\"".self::styles["variables"]."\">$1</span>",$code);
        $code = preg_replace("/([a-z0-9_]+?)\(/misU","<span class=\"".self::styles["functions"]."\">$1</span>(",$code);
        $code = preg_replace("/([\'#].+[\'#])/misU","<span class=\"".self::styles["texts"]."\">$1</span>",$code);
        $code = preg_replace("/(\/\*.+\*\/)/misU","<span class=\"".self::styles["comment"]."\">$1</span>",$code);
        $code = preg_replace("/(\/\/.+\n)/misU","<span class=\"".self::styles["comment_b"]."\">$1</span>",$code);
        $code = str_replace("#","\"",$code);
*/
        //$code = htmlentities($code);

        return str_replace('<br />',"",highlight_string($code,true));
    }
}
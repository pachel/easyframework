<?php
namespace Pachel\EasyFrameWork\Interfaces;
abstract class Route{
    public string $path;
    public string $method;
    public array $object;
}
<?php

namespace App\Helpers;

use App\Enums\Models\ModelStatusEnum;

class HTMLHelper
{
    public static function link(string $url, string $mask = "Ir al enlace", string $target = "_blank"): string
    {
        return "<a href='{$url}' target='{$target}'>{$mask}</a>";
    }
    public static function generarButton(array $props): string
    {
        $title = $props["title"] ?? "";
        $element = $props["element"] ?? "button";
        $href = $props["href"] ?? "";
        $id = $props["id"] ?? "";
        $class = $props["class"] ?? "primary";
        $text = $props["text"] ?? "Boton";
        $type = $props["type"] ?? "button";
        $data = $props["data"] ?? [];
        $str_data = "";
        foreach ($data as $key => $value) {
            $str_data .= "data-{$key}='{$value}' ";
        }
        return "<{$element} href='{$href}' id='{$id}' type='{$type}' data-container='body' data-bs-placement='top' data-bs-original-title='{$title}' data-bs-toggle='tooltip' {$str_data} class='btn {$class}'>{$text}</{$element}>";
    }
    public static function badge(mixed $enum, string $color = "primary")
    {
        return "<span class='badge rounded-pill badge-{$color}'>{$enum->name}</span>";
    }
}

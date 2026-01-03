<?php
namespace App\Server\AIDeveloper\Domain;

use App\Server\AIDeveloper\Domain\IPrompt;

class Prompt implements IPrompt
{
    private string $html;
    private string $css;
    private string $js;
    private string $sugerencia;

    public function __construct(
        string $html,
        string $css,
        string $js,
        string $sugerencia
    ) {
        $this->html = $html;
        $this->css = $css;
        $this->js = $js;
        $this->sugerencia = $sugerencia;
    }

    public function getHTML(): string
    {
        return $this->html;
    }

    public function getCSS(): string
    {
        return $this->css;
    }

    public function getJS(): string
    {
        return $this->js;
    }

    public function getSugerencia(): string
    {
        return $this->sugerencia;
    }
}

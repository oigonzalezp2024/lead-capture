<?php
namespace App\Server\AIDeveloper\Infrastructure\Utils;

use App\Server\AIDeveloper\Domain\IPrompt;
use App\Server\AIDeveloper\Domain\IPromptConstructor;

class PromptConstructorTS implements IPromptConstructor
{
    protected IPrompt $prompt;
    protected string $output = "";

    public function __construct(IPrompt $prompt) 
    {
        $this->prompt = $prompt;
    }

    public function process(): void
    {
        $html = (string) $this->prompt->getHTML();
        $css = (string) $this->prompt->getCSS();
        $js = (string) $this->prompt->getJS();
        $sugerencia = (string) $this->prompt->getSugerencia();

        $prompt = "Actúa como un experto en UI/UX. 
                CONTEXTO: Se proporciona una estructura HTML, CSS
                HTML: '$html'. 
                CSS: '$css'. 
                TYPESCRIPT Actual: '$js'. 
                
                TAREA: Modifica el TYPESCRIPT según esta sugerencia: '$sugerencia'. 
                REGLA CRÍTICA: Devuelve ÚNICAMENTE el código TYPESCRIPT resultante. No incluyas explicaciones, ni bloques de código markdown (```typescript ... ```).";

        $this->output = (string) $prompt;
    }

    public function output()
    {
        return $this->output;
    }
}

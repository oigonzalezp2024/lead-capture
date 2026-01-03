<?php
namespace App\Server\AIDeveloper\Infrastructure\Utils;

use App\Server\AIDeveloper\Domain\IPrompt;
use App\Server\AIDeveloper\Domain\IPromptConstructor;

class PromptConstructor implements IPromptConstructor
{
    private IPrompt $prompt;
    private string $output;

    public function __construct(
        IPrompt $prompt
    ) {
        $this->prompt = $prompt;
    }

    public function process(): void
    {
        $html = (string) $this->prompt->getHTML();
        $css = (string) $this->prompt->getCSS();
        $js = (string) $this->prompt->getJS();
        $sugerencia = (string) $this->prompt->getSugerencia();

        $prompt = "Actúa como un experto en UI/UX. 
                CONTEXTO: Se proporciona un HTML que contiene una 'Encuesta Adaptativa para captar clientes de desarrollo de software'. Esta estructura es SAGRADA y no debe verse afectada funcionalmente.
                HTML: '$html'. 
                CSS ACTUAL: '$css'. 
                JS: '$js'. 
                
                TAREA: Modifica el CSS según esta sugerencia: '$sugerencia'. 
                REGLA CRÍTICA: Devuelve ÚNICAMENTE el código CSS resultante. No incluyas explicaciones, ni bloques de código markdown (```css ... ```).";

        $this->output = (string) $prompt;
    }

    public function output()
    {
        return $this->output;
    }
}

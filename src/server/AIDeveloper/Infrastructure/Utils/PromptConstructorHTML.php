<?php
namespace App\Server\AIDeveloper\Infrastructure\Utils;

use App\Server\AIDeveloper\Domain\IPrompt;

class PromptConstructorHTML extends PromptConstructor 
{
    public function __construct(IPrompt $prompt) 
    {
        parent::__construct($prompt); 
    }

    public function process(): void 
    {
        $html = (string) $this->prompt->getHTML();
        $sugerencia = (string) $this->prompt->getSugerencia();

        $prompt = "Actúa como un experto en UI/UX.
                   TAREA: Modifica la estructura HTML según esta sugerencia: '$sugerencia'.
                   CONTEXTO HTML: '$html'.
                   REGLA: Devuelve solo el código HTML sin markdown.";

        $this->output = $prompt;
    }
}

<?php
namespace App\Server\AIDeveloper\Infrastructure\Utils;

use App\Server\AIDeveloper\Domain\IOutputClean;

class OutputClean implements IOutputClean
{
    private array $input;
    private string $output;

    public function __construct(
        array $input = []
    ) {
        $this->input = $input;
    }

    function input(array $input)
    {
        $this->input = $input;
    }

    function process(): void
    {
        $input = $this->input;
        $textoFinal = $input['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (!$textoFinal) {
            $this->output = "";
            return;
        }

        $textoFinal = preg_replace('/^```[a-z]*\s*|\s*```$/i', '', $textoFinal);
        $this->output = trim($textoFinal);
    }

    function output(): string
    {
        return $this->output;
    }
}

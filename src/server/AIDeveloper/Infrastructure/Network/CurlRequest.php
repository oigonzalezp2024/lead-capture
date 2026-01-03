<?php
namespace App\Server\AIDeveloper\Infrastructure\Network;

use App\Server\AIDeveloper\Domain\ICurlRequest;
use App\Server\AIDeveloper\Exceptions\AiGenerationException;

class CurlRequest implements ICurlRequest
{
    private string $url;
    private array $data;
    private array|string $output = [];

    public function __construct(string $url = "", array $data = [])
    {
        $this->url = $url;
        $this->data = $data;
    }

    public function setUrl(string $url)
    {
        $this->url = $url;
    }

    public function setData(array $data)
    {
        $this->data = $data;
    }

    function process(): void
    {
        $url = $this->url;
        $data = $this->data;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new AiGenerationException("Error de conexión con la API: " . $error);
        }
        $result = json_decode($response, true);
        if (isset($result['error'])) {
            throw new AiGenerationException("API Error: " . ($result['error']['message'] ?? 'Desconocido'));
        }
        curl_close($ch);
        $this->output = $result;
    }

    function output(): array|string
    {
        return $this->output;
    }
}

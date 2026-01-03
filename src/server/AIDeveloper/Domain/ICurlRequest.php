<?php
namespace App\Server\AIDeveloper\Domain;

interface ICurlRequest
{
    public function setUrl(string $url);
    public function setData(array $data);
    public function process(): void;
    public function output(): array|string;
}

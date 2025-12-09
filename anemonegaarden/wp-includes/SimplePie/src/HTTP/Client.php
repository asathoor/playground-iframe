<?php
 declare(strict_types=1); namespace SimplePie\HTTP; interface Client { public const METHOD_GET = 'GET'; public function request(string $method, string $url, array $headers = []): Response; } 
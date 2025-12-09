<?php
 declare(strict_types=1); namespace SimplePie\Cache; interface NameFilter { public function filter(string $name): string; } 
<?php
 declare(strict_types=1); namespace SimplePie; use Exception as NativeException; class Exception extends NativeException { } class_alias('SimplePie\Exception', 'SimplePie_Exception'); 
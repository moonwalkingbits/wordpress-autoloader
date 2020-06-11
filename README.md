# Autoloader

A class loader for the WordPress coding standards.

When taking the object oriented approach to developing in PHP, autoloading can
get rid of all the include/require statements in your code. Instead of manually
including the file before you use the class, or having to check if the class
is available you just use it. If it is not yet included it will be automatically
included by the autoloader.

The only class file you need to include is the autoloader. After you've set it
up it will take care of the rest of the includes.

```php
<?php

require_once '/path/to/class-autoloader.php';

$autoloader = new Moonwalking_Bits\Autoloader();

// Add as many namespace mappings as needed.
$autoloader->add_namespace_mapping( 'Namespace', '/path/to/classes' );

// Register the autoloader with the spl provided autoload queue.
spl_autoload_register( array( $autoloader, 'load_class' ) );
```

After this setup any class in the `Namespace` namespace that resides in the
registered namespace directory can be used directly without any include/require
statements.

## Nested namespaces

The autoloader will resolve any classes with nested namespace as long as the
directory structure follows the namespace path.

**Example:**

```php
<?php

$autoloader->add_namespace_mapping( 'Namespace', '/path/to/classes' );
```

```php
<?php // /path/to/classes/nested/class-test-class.php

namespace Namespace\Nested;

class Test_Class {}
```

Given the above example the class `Namespace\Nested\Test_Class` will be loaded
correctly.

## Classes without namespace

To load classes that do not use any namespace just register the mapping with an
empty namespace.

**Example:**

```php
<?php

$autoloader->add_namespace_mappint( '', '/path/to/classes' );
```

## License

Autoloader is released under the [GPL](license) license.

[license](https://www.gnu.org/licenses/)

# Custom User Table Plugin Testing

This directory contains unit tests for the Custom User Table WordPress plugin using PHPUnit and Brain Monkey.

## Overview

These tests demonstrate how to test WordPress plugins without a full WordPress installation. 
Brain Monkey is used to mock WordPress functions and hooks, allowing us to test our code in isolation.

## Requirements

- PHP 7.2 or higher
- Composer

## Setup

1. Install dependencies:
   ```
   composer install
   ```

2. Run the tests:
   ```
   ./vendor/bin/phpunit
   ```

   Or run a specific test file:
   ```
   ./vendor/bin/phpunit tests/unit/TestHooks.php
   ```

## Test Structure

The test suite includes:

- `TestHooks.php`: Demonstrates testing WordPress hooks using Brain Monkey
- `TestPluginFunctionality.php`: Demonstrates testing plugin functionality in isolation
- `bootstrap.php`: Sets up the test environment

## Notes on Implementation

This test suite focuses on testing WordPress hooks and functions in isolation without directly loading the plugin's actual implementation classes. We use the following approach:

1. **Mock Testing**: The tests directly demonstrate concepts rather than testing the actual plugin classes
2. **Isolation**: Each test runs in isolation, making tests more reliable
3. **No WordPress**: Tests run without a WordPress instance

This approach was chosen due to challenges in testing plugin classes that use WordPress core functions directly. For a more comprehensive test suite that tests the actual plugin classes, you would need to:

1. Create mock implementations or wrappers for your main plugin classes
2. Or use WP_UnitTestCase with a real WordPress instance (requires more setup)

## Key Testing Concepts

### Testing WordPress Hooks

Brain Monkey allows testing if hooks are properly added and executed:

```php
// Test if an action is added
Actions\expectAdded('init')->once();

// Test if an action is executed
Actions\expectDone('init')->once();

// Test if a filter is added
Filters\expectAdded('query_vars')->once();

// Test if a filter is applied
Filters\expectApplied('query_vars')->once()->andReturn(['test_var']);
```

### Mocking WordPress Functions

WordPress functions can be mocked:

```php
// Make a function return a specific value
Functions\when('get_option')->justReturn('mocked value');

// Expect a function to be called with specific arguments
Functions\expect('wp_enqueue_script')
    ->once()
    ->with('script-handle', Mockery::type('string'), [], Mockery::any(), true);
```

### Benefits of Using Brain Monkey

1. **Fast**: Tests run much faster as they don't need a WordPress instance
2. **Isolated**: Each test runs in isolation, making them more reliable
3. **No Database**: No need for a test database
4. **Simple Setup**: Easier to set up compared to WP_UnitTestCase

## Resources

- [Brain Monkey Documentation](https://giuseppe-mazzapica.gitbook.io/brain-monkey/)
- [WordPress Stack Exchange - Testing hooks callback](https://wordpress.stackexchange.com/questions/164121/testing-hooks-callback/164138#164138) 
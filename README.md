# Tinkerwell Drivers

Tinkerwell is an application that lets you run code within your favorite PHP application - either locally or via SSH.

![](https://tinkerwell.app/screenshots/screenshot_tinker_locally.png)

When you open your project with Tinkerwell, one of the available drivers will be loaded and bootstrap your application to prepare it for code execution within Tinkerwell. 
This repository holds all available drivers for Tinkerwell. So far Tinkerwell supports bootstrapping Laravel and Wordpress applications out of the box. 
If your framework does not have a specific driver yet, Tinkerwell will at least try and load your projects autoload file.

## Installation

Install the Tinkerwell application from [https://tinkerwell.app](https://tinkerwell.app).

### Documentation

#### Anatomy of a driver

A Tinkerwell driver is a simple class that will be called when your project gets opened in the Tinkerwell application.  
Here is how a basic driver for Wordpress looks like:

```php
class MyCustomDriver extends TinkerwellDriver
{
    /**
     * Determine if the driver can be used with the selected project path.
     * You most likely want to check the existence of project / framework specific files.
     *
     * @param string $projectPath
     * @return bool
     */
    public function canBootstrap($projectPath)
    {
        return file_exists($projectPath . '/wp-load.php');
    }
    
    /**
     * Bootstrap the application so that any executed can access the application in your desired state.
     * 
     * @param string $projectPath
     */
    public function bootstrap($projectPath)
    {
        require $projectPath . '/wp-load.php';
    }
}
```

The `canBootstrap` and `bootstrap` methods are the important pieces of every driver. 
They determine if the driver can be used for the given project path and how the application should get bootstrapped and prepared, 
so that the application is in the correct state when any code gets executed.

#### Providing variables

When a Tinkerwell driver gets loaded, you can tell Tinkerwell to automatically set variables with specific content, so that these variables are immediately available within the Tinkerwell application.

To define these variables, add a `getAvailableVariables` to your driver. This method should return an array of all variables and their values:

```php
public function getAvailableVariables()
{
    return [
        '__blog' => get_bloginfo()
    ];
}
```

This method gets executed after the Tinkerwell drivers `bootstrap` method was called, so that you have access to any classes that got bootstrapped along with the driver.

#### Context menu

Once your application is loaded, Tinkerwell allows you to load custom context menu items that are defined in your driver. Here is an example from a Laravel application:

![](https://tinkerwell.app/screenshots/context_menu.png)

To define context menu items, add a `contextMenu` method to your driver. It should return an array of available context menu entries.

```php
use Tinkerwell\ContextMenu\Label;
use Tinkerwell\ContextMenu\Submenu;
use Tinkerwell\ContextMenu\SetCode;
use Tinkerwell\ContextMenu\OpenURL;

public function contextMenu()
{
    return [
        Label::create('Detected Laravel v' . app()->version()),
        
        Submenu::create('Snippets', [
            SetCode::create('Perform Query', '\DB::table("example")->get();'),
        ]);

        OpenURL::create('Documentation', 'https://tinkerwell.app'),
    ];
}
```

#### Available context menu items:

##### Label

A simple label that displays textual information in the context menu:

```php
Label::create('This is the label text');
```

##### Link

The `OpenURL` class can be used to open a specific URL in the browser, once the user clicks on it.

```php
OpenURL::create('Documentation', 'https://tinkerwell.app');
```

##### Code Snippets

The `SetCode` class can be used to automatically pre-fill the Tinkerwell code area with specific code snippets. The code will not automatically be executed. 

```php
SetCode::create('Hello World', 'echo "Hello world";');
```

##### Submenu

Last but not least you can group multiple context items into a `Submenu`.

```php
Submenu::create('This is a submenu', [
    Label::create('Entry 1'),
    Label::create('Entry 2'),
    Label::create('Entry 3')
]);
```

#### Project specific drivers

You can specify that a project should use a custom Tinkerwell driver, even if it is a project that is already supported by one of the available drivers. 

In the project directory that should contain the custom driver, create a `.tinkerwell` directory.
In there you can create your custom Tinkerwell driver class and name it, for example, `CustomTinkerwellDriver`. Now when you open this project directory within Tinkerwell, your custom driver will be used instead of any built-in drivers.

## Credits

- [Marcel Pociot](https://github.com/beyondcode)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
# Timeline Framework

Timeline is a micro PHP framework. Designed for small website project, it's easy to use and require nearly zero configuration. It use a singleton design similar to CodeIgniter 3 framework.

**This project isn't maintained since 2016. Please do not use for production.**


### Requirements

* PHP 5.3+
* PDO for database


### Installation

* Clone git repository

* Retrieve git submodule `git submodule init` and `git submodule update`

* Open `apps/configs/settings.php` and change the `$config['secretKey']`


### Features

* MVC PHP Micro-framework
* Basic routing system
* Support custom library and helper
* Localization
* Hooks
* Widget
* Authentification
* Form verification library
* Raw output
* View loader
* Configuration library
* Session library
* Cookie library
* Upload library
* PDO support
* Twig support


### Usage

#### Folder hierarchy

⋅⋅* apps/ : Contains files and folders of the application
⋅⋅⋅⋅* apps/configs : Configuration files
⋅⋅⋅⋅* apps/hooks : Hooks files
⋅⋅⋅⋅* apps/langs : Localization files
⋅⋅⋅⋅* apps/models : Model files
⋅⋅⋅⋅* apps/modules : Controller files
⋅⋅⋅⋅* apps/views : View files
⋅⋅⋅⋅* apps/widget : Widget files
⋅⋅* system/ : Contains files and folders of framework
⋅⋅⋅⋅* system/core : Core files
⋅⋅⋅⋅* system/helpers : Helper files
⋅⋅⋅⋅* system/libraries : Library files
⋅⋅⋅⋅* system/vendors : External library (like Twig)
⋅⋅* public/ : Contains static files that can be accessed publicaly (css, images, js etc...)

#### Controller

Controllers are inside `apps/modules` folder.

```php
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Foo extends Controller {

    public function index() {
        echo 'Hello world';
    }

}
```

#### Routing

Routing work with GET parameters "a" and "m". "a" is stand for action and is the controller name in lowercase. "m" is stand for method and is the controller method in lowercase.

Example : an URL http://localhost/?a=foo&m=bar call the method bar of controller Foo

If parameter "m" isn't define it call `index` method. If parameter "a" isn't define it call the default controller.

The default controller and method can be configured in `apps/configs/settings.php`

#### Models

Models are inside `apps/models` folder.

```php
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bar_model extends Model {

    public function sum($a, $b) {
        return $a + $b;
    }

}
```

Using your model inside your controller :
```php
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Foo extends Controller {

    public function index() {
        $this->load->model('bar_model', 'bar');
        echo $bar->sum(2, 3);
    }

}
```

#### Model with PDO

First you need to load PDO library in your controller by adding the line `$this->load->library('pdodb')`.

Then in your model :
```php
class Bar_model extends Model {

    public function get_users() {
        $pdo =& $this->pdodb->loadPDO();
        $query = $pdo->query('SELECT * FROM users');
        return $query->fetchAll(PDO::FETCH_CLASS);
    }

}
```

#### Views

Views are in `apps/views` folder. Views are mostly PHP that contains HTML.

Loading a view inside a controller :
```php
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Foo extends Controller {

    public function index() {
        $this->load->library('view');
        $this->view->load('view.php', array('foo'=>'Hello world'));
    }

}
```

#### Helper

Helper are in `system/helpers` folder. Helper are small function that can be used inside a view.

```php
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('lang')) {

    function lang($line) {
        $TL =& getInstance(); // Get framework instance
        if ( ! $TL->load->isLoaded('lang'))
            $TL->load->library('lang');
        return $TL->lang->line($line);
    }

}
```

For using helper, you need to load it inside a controller with `$this->load->helper('lang')`.

#### Configuration

Config files are in `application/configs` folder.

```php
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Foo extends Controller {

    public function index() {
        $this->config->load('date', 'timeformat'); // Load file apps/configs/date.php as timeformat
        echo 'Date' . date($this->config->get('day', 'timeformat'));
        echo 'Language is ' . $this->config->get('localization'); // Part of default configuration
        
        $this->config->set('localization', 'fr');
        echo 'Language is ' . $this->config->get('localization'); // Display fr
    }

}
```

#### Libraries

Libraries are in `system/libraries` folder. To load a library use `$this->load->library('foo');` inside a controller method.

After that you can use your library method in controller / model like that : `$this->foo->method()`.

#### Form verification

Timeline offer a library for easily verifying form. 

* First load library inside your controller method `$this->load->library('vform')`
* Then add form rules with `$this->vform->addRules($field, $label='', $rules='', $prep='')`
* Check if your form is valid with `$this->vform->run($method='POST)`

Example :
```php
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Foo extends Controller {

    public function index() {
        $this->load->library('vform');

        $this->vform->addRules('email', 'e-mail', 'required|maxLength[124]|email');
        $this->vform->addRules('age', 'age', 'required|isInt|gt[12]');

        if ($this->vform->run()) {
            $email = $this->vform->getPostData('email', FALSE);
            $email = $this->vform->getPostData('age');

            // Do something ...
        }
        else {
            // Do other thing ...
        }
    }

}
```

##### Rules

Rule | Usage
--- | --- 
required | Field must exist in data
notEmpty | Field can not be empty
matches[str] | Match with the other field in parameter
minLength | Minimal value length
maxLength | Maximal value length
exactLength | Exact value length
isNumeric | Field must be numerical value
isInt | Field must be integer value
gt[int] | Greater than
lt[int] | Lower than
alpha | Field contains only letter
alphaNum | Field contains only letter and number
alphaExt | Field contains letter, number and - space and _
email | Field must be an email
check[str] | Field must match to regular expression in parameter

##### Error

You can check if your form has error with method `$this->vform->hasError()`. To display error, use `$this->vform->getError()`. 
Inside a view, vform library automatically load a helper that help you to retrieve form error, you can use `hasErrorForm()` and `getErrorForm()`.

Markdown | Less | Pretty
--- | --- | ---
*Still* | `renders` | **nicely**
1 | 2 | 3

#### Undocumented features

* Twig templating
* Widget system
* Auth system
* Localization system

### License

MIT License
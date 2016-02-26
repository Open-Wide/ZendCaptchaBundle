ZendCaptchaBundle
=====================
The ZendCaptchaBundle adds support for a captcha form type for the Symfony form component.
Compatibility with Symfony
=====================
If you are using SYmfony >= 2.8
Installation
============
### Step 1: Download the ZendCaptchaBundle
***Using Composer***

``` bash
    composer require openwide/zend-captcha-bundle
```
or add bundle in composer.json
``` 
  // ...
  "require": {
        // ...
        "openwide/zend-captcha-bundle": "dev-master"
        // ...
    },
   // ...
```
### Step 2: Enable the bundle

```php
<?php
// app/appKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Openwide\Bundle\ZendCaptchaBundle\OpenwideZendCaptchaBundle(),
    );
}
```
Usage
=====
You can use the "zend captcha" type in your forms this way:

```php
<?php
    use Openwide\Bundle\ZendCaptchaBundle\Form\Type\ZendCaptchaType;
    // ...
    $builder // ...
    ->add('captcha', ZendCaptchaType::class)
```

Options
=======
* **img_dir** allow you to specify the directory for storing CAPTCHA images. The default is /web/captcha
* **font** allow you to specify the font you will use. The default is arial.ttf
* **img_url** allow you to specify the relative path to a CAPTCHA image to use for HTML markup. The default is “/captcha”.
* **suffix** allow you to specify the filename suffix for the CAPTCHA image. The default is “.png”. 
* **width** allow you to specify the width in pixels of the generated CAPTCHA image. The default is 200px.
* **height** allow you to specify the height in pixels of the generated CAPTCHA image. The default is 50px.
* **font_size** allow you to specify the font size in pixels for generating the CAPTCHA. The default is 24px.
* **dot_noise_level** and **line_noise_level**  allow you to control how much “noise” in the form of random dots and lines the image would contain. The default is 100 dots and 5 lines. The noise is added twice - before and after the image distortion transformation.
* **word_len**  allow you to specify the length of the generated “word” in characters. The default is 8.
* **expiration** allow you to specify a maximum lifetime the CAPTCHA image may reside on the filesystem. Garbage collection is run periodically each time the CAPTCHA object is invoked, deleting all images that have expired. Expiration values should be specified in seconds. The default is 600.
* **gc_freq** allow you to specify how frequently garbage collection should run. Garbage collection will run every 1/gcFreq calls. The default is 100.
* **bypass_code** code that will always validate the captcha (default=null)

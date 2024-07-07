# PHP OOP

![PHP Logo](https://upload.wikimedia.org/wikipedia/commons/thumb/3/31/Webysther_20160423_-_Elephpant.svg/2560px-Webysther_20160423_-_Elephpant.svg.png)
A simple PHP OOP (Object-Oriented Programming) project.

## Requirements

This project requires the following:

- PHP 7.4 or higher
- Composer
- Node 18 or higher
- Valet or Herd (optional)

## Installation

To install this project, you'll need [Composer](https://getcomposer.org/). Follow these steps:

1. Open your terminal.
2. Run the following command to create a new project:
    ```sh
    composer create-project gollumeo/php-oop my-project
    ```
3. You'll be prompted into picking your CSS flavor. 
4. Navigate into your project directory:
    ```sh
    cd my-project
    ```

## Development Server

To run the development server, you have several options depending on your environment.

### Using PHP Built-in Server

If you are not using Valet or Herd, you can use PHP's built-in server:

```sh
php -S localhost:8000 -t public/
```

### Using Valet/Herd

#### Using Valet

If you are using Valet, you can run the following command:

```sh
valet link
```

Then, open your browser and visit `http://my-project.test`.

#### Using Herd

If you are using Herd, you can run the following command:

```sh
herd link
```

Then, open your browser and visit `http://my-project.test`.

## Contributing

Feel free to contribute to this project! You can also comment on the issues or create new ones foe next features or any issue to be solved!

## Coming Soon(tm)

[*] Actual doc on how to use/set up new data flow:
    [*] setting up the model properties;
    [*] handling requests and responses within the controller
    [*] sending data from the controller to the view

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

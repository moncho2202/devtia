Instalación
-----------

1. Clonar el proyecto

    ```bash
    $ git clone https://github.com/moncho2202/devtia.git
    $ cd devtia
    ```
    
2. Instalar vendors

    ```bash
    $ composer install
    ```
    
3. Generar base de datos

    ```bash
    $ php bin/console doctrine:migration:migrate
    ```
    
4. Cargar datos de prueba

    ```bash
    $ php bin/console doctrine:fixtures:load
    ```
    
5. Arrancar el servidor

    ```bash
    $ php bin/console server:run
    ```
    
    Acceder a http://127.0.0.1:8000/books
    
6. Ejecutar comando para contar el numero de libros

    ```bash
    $ php bin/console books:count
    ```
    
7. Ejecutar test

    ```bash
    $ ./bin/phpunit
    ```

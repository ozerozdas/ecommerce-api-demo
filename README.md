# E-Commerce API Demo

Please add "*visite.local*" to your host file.<br>
You must copy .env.example file as .env

And run to build images:
    
    docker-compose up -d --build

You must open docker console:

    docker exec -it ecommerce-api-demo_php_1 /bin/bash

Now you can create a database named "*laravel*" locally and run:

    php artisan migrate

To import example data to db, please run this command:

    php artisan db:seed

Default Basic Auth (You can change in env file):

    Username: admin
    Password: admin

I work with Insomnia and insomnia json file is in the website folder. You can use this file for import.

Example endpoints are in the below

### Order Listing (GET)
``` 
    http://visite.local/api/orders
```
### Save Order (POST)
``` 
    http://visite.local/api/orders
```
### Delete Order (DEL)
``` 
    http://visite.local/api/orders
```
### Discount Calculator (GET)
``` 
    http://visite.local/api/discount/{orderId}
```
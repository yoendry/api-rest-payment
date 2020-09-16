# API REST Payment Management
# v1.0

Api Rest that allows to register payments from different companies.

# Installation instructions:

1.Open a terminal.

2.Run the commands below:

    git clone https://github.com/yoendry/api-rest-payment.git
    
    cd api-rest-payment

Configure your database connection information.It is configured in the api-rest-payment/app/config/parameters.yml file:

parameters:

    database_host:     <your_host>    
    database_name:     <your_database_name>    
    database_user:     <your_user>
    database_password: <your_password>

Later execute :

    php bin/console doctrine:database:create
    
    php bin/console doctrine:schema:update --force
    
    php bin/console doctrine:query:sql "$(< insert_data.sql)"

    php bin/console server:run

# Site url

    http://localhost:8000

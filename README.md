
#### To run the app follow the steps below:
1. clone repo from branch "development"
2. ```cd advox_task```
3. ```docker-compose up -d```
4. ```docker-compose exec phpfpm env COMPOSER_MEMORY_LIMIT=-1 bash```
4a. IN CONTAINER SHELL (You will need your Magento-marketplace authorization data. The username is the public key, the username is the private key from Magento Marketplace panel):
```
>>> composer instal -vvv --prefer-source --no-interaction --no-dev -o
>>> exit
```
5. ```sudo chmod -R 777 mysql/```
6. ```docker-compose exec phpfpm env COMPOSER_MEMORY_LIMIT=-1 bash```
6a. IN CONTAINER SHELL run 5 commands:
```
    >>> bin/magento setup:config:set --backend-frontname=admin --db-host=mysql:3315 --db-name=mydatabase --db-user=myuser --db-password=test123
	>>> bin/magento setup:install	
	>>> bin/magento admin:user:create	(You will be asked to enter your admin details. Enter them and remember.)
	>>> sh clear.sh
	>>> exit
```

###### The application should be launched at: http://localhost:8088
###### Mysql container is working on http://localhost:3315
###### If u want to go to container run: ```docker-compose exec mysql bash```
###### and: ```mysql -u myuser -p```  later in container shell.


#### App description

A new Cats section has been created in the tab:
```
	Stores -> Configuration -> Catalog
```
Here you can add text that will appear on the images.

The task doesn't specify when the replacement of images should take place (should it be related to some event).
That's why I decided to add a CLI command that supports image replacement.
Replacing images for all products can be invoked with the following command:
```
	bin/magento cats:addCats
```
Adding a cat image for a specific product can be triggered with the SKU flag:
```
	bin/magento cats:addCats --sku="abc123"
```
The task also does not specify that the repeated invocation of the command should add or replace the image. So I chose to add next.

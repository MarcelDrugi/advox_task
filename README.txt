To run the app follow the steps below:

1. clone repo from branch "development"
2. cd piotr-mazur-starting-project
3. docker-compose up -d
4. docker-compose exec phpfpm bash
5. IN CONTAINER SHELL (you will need your Magento-marketplace authorization data):
	>>> composer instal -vvv
	>>> exit;
6. sudo chmod -R 777 mysql/
7. docker-compose exec phpfpm bash
8. IN CONTAINER SHELL run 5 commands:
	>>> bin/magento setup:config:set --backend-frontname=admin --db-host=mysql:3315 --db-name=mydatabase --db-user=myuser --db-password=test123
	>>> bin/magento setup:install	
	>>> bin/magento admin:user:create	(You will be asked to enter your admin details. Enter them and remember.)
	>>> sh clear.sh
	>>> exit

9. The application should be launched at: http://localhost:8088


Mysql container is working on http://localhost:3315
If u want to go to container run: docker-compose exec mysql bash
and: "mysql -u myuser -p"  later in container shell

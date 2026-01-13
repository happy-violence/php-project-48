install:
	composer install

validate:
	composer validate

lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin
	composer exec --verbose phpcs -- src tests

test:
	composer exec --verbose phpunit tests
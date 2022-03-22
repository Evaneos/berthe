.PHONY: run-tests
run-tests:
	docker run --rm -ti -v $(PWD):/app --workdir /app php:7.4-cli vendor/bin/phpunit test/

.PHONY: install
install:
	docker run -ti --rm \
        --env COMPOSER_HOME=/tmp/composer \
        --volume ${HOME}/.config/composer:/tmp/composer \
        --volume ${PWD}:/app \
        --user $(id -u):$(id -g) \
        --workdir /app \
        composer:1 composer install

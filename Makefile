.PHONY: helpers
helpers:
	./vendor/bin/sail artisan ide-helper:generate
	./vendor/bin/sail artisan ide-helper:models -F helpers/modelsHelpers.php -M
	./vendor/bin/sail artisan ide-helper:meta

phpstan:
	./vendor/bin/sail php  ./vendor/bin/phpstan analyse


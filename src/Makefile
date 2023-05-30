\.PHONY: info install update composer-install ci composer-update composer-init cu composer-autoload ca

info:
	@echo "LenderKit Application Utility (PHP-FPM Container)"
	@echo " "
	@echo "Usage:"
	@echo "	make command"
	@echo " "
	@echo "Available commands:"
	@echo "	install 			Install project after first time download"
	@echo "	update 				Update project after code update"
	@echo "	composer-install|ci		Run composer install w/ env options"
	@echo "	composer-update|cu		Run composer update w/ env options"
	@echo "	composer-autoload|ca		Run composer dump autoload w/ env options"

COMPOSER_OPTS =

MAYBE_SUDO = sudo
ifneq "$(NOSUDO)" ""
	MAYBE_SUDO =
endif

# INSTALL AFTER CLONE
install: composer-install

update: composer-install

# Composer helpers
ci: composer-install
composer-install: composer-init
	@echo 'Install composer...'
	$(COMPOSER_OPTS) composer install

cu: composer-update
composer-update: composer-init
	@echo 'Update composer...'
	$(COMPOSER_OPTS) composer update

ca: composer-autoload
composer-autoload: composer-init
	$(COMPOSER_OPTS) composer dump-autoload

composer-init:
	@echo ''

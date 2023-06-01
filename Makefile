.PHONY: info init build run test-run install update stop xdebug-init chown php-bash chl chl-first


info:
	@echo "LenderKit Server Configuration/Launcher"
	@echo " "
	@echo "Usage:"
	@echo "	make command [V=1]"
	@echo " "
	@echo "Options:"
	@echo "	V=1 			Vagrant mode, use it in Vagrant to prevent permission errors"
	@echo "	NONE_INTERACTIVE=1	Use it in CI to prevent errors with docker-compose exec\run"
	@echo " "
	@echo "Available commands:"
	@echo "	init	 		Init configurations"
	@echo "	build	 		Build images"
	@echo "	test-run		Docker run in place"
	@echo "	run|up	 		Docker run as daemon"
	@echo "	stop|down			Docker stop"
	@echo "	install	 		Run install process (php and nodejs)"
	@echo "	update	 		Run install process (php and nodejs)"
	@echo "	chown	 		Return back correct file owner for files created inside a container"
	@echo "	php-bash		Open php-fpm container bash"

CURDIR_BASENAME = $(notdir ${CURDIR})

MAYBE_SUDO =
ifneq "$(V)" ""
	MAYBE_SUDO = sudo
endif

DOCKER_T_FLAG =
ifeq "$(NON_INTERACTIVE)" "1"
    DOCKER_T_FLAG = -T
endif

DOCKER_COMPOSE_EXEC = docker-compose exec ${DOCKER_T_FLAG} --privileged --index=1
DOCKER_COMPOSE_EXEC_WWW = ${DOCKER_COMPOSE_EXEC} -w /var/www/html
DOCKER_COMPOSE_RUN = docker-compose run ${DOCKER_T_FLAG} -w /var/www/html

CONV_CHL_IMAGE := justcoded/php-conventional-changelog:latest
CONV_CHL_DR := docker run -it --rm --volume "$$PWD":/codebase ${CONV_CHL_IMAGE} bash
CONV_CHL_CMD := conventional-changelog --config changelog.php
############################################
# Make Targets
############################################

init:
	@if [ ! -f '.env' ]; then \
		echo 'Copying .env file...'; \
		${MAYBE_SUDO} cp .env.example .env; \
	fi;
	@if [ ! -f 'src/.env' ]; then \
		echo 'Copying src/.env file...'; \
		${MAYBE_SUDO} cp src/.env.example src/.env; \
	fi;
	@if [ ! -f 'docker-compose.yml' ]; then \
		echo 'Copying docker-compose.yml file...'; \
		${MAYBE_SUDO} cp docker-compose.example.yml docker-compose.yml; \
	fi;
	@if [ ! -f 'runtime/bash/.bash_history' ]; then \
		echo 'Creating runtime/bash/.bash_history file...'; \
		${MAYBE_SUDO} mkdir -pv runtime/bash && ${MAYBE_SUDO} touch runtime/bash/.bash_history; \
    fi;
	@echo ''
	@echo 'NOTE: Please check your configuration in ".env" before run.'
	@echo 'NOTE: Please check your configuration in "docker-compose.yml" before run.'
	@echo ''

install: build run
	${DOCKER_COMPOSE_EXEC_WWW} app bash -c "make install"

update: stop run
	${DOCKER_COMPOSE_EXEC_WWW} app bash -c "make update"

build:
	docker-compose build

up: run
run: xdebug-init
	docker-compose up --force-recreate -d

test-run: xdebug-init
	docker-compose up --force-recreate

xdebug-init:
	@if [ $$USER = 'vagrant' ]; then \
		export XDEBUG_REMOTE_HOST=`/sbin/ip route|awk '/default/ { print $$3 }'` \
			&& echo "Set XDEBUG_REMOTE_HOST to $${XDEBUG_REMOTE_HOST} in .env" \
			&& sudo sed -i "s/XDEBUG_REMOTE_HOST=.*/XDEBUG_REMOTE_HOST=$${XDEBUG_REMOTE_HOST}/g" .env; \
	fi

test:
	@echo 'dummy test'

down: stop
stop:
	docker-compose down

chown:
	sudo chown -R $$(whoami) .env docker-compose.* build/ src/

php-bash:
	${DOCKER_COMPOSE_EXEC_WWW} app bash

##
# @command chl 	Generate changelog based on conventional commits
##
chl:
	${CONV_CHL_DR} \
		-c "${CONV_CHL_CMD}"

##
# @command chl-first 	Generate changelog based on conventional commits, first version
##
chl-first:
	${CONV_CHL_DR} \
		-c "${CONV_CHL_CMD} --first-release"


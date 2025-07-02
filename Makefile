DOCKER_COMPOSE = docker compose
USER_UID = $(shell id -u)
USER_GID = $(shell id -g)

up:
	USER_UID=$(USER_UID) USER_GID=$(USER_GID) $(DOCKER_COMPOSE) up --build -d

down:
	$(DOCKER_COMPOSE) down --remove-orphans

php:
	$(DOCKER_COMPOSE) exec php bash

node:
	$(DOCKER_COMPOSE) run node bash

.PHONY: up down build bash

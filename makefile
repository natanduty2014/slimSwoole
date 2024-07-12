.PHONY: directory

RED = \033[0;31m
GREEN = \033[0;32m
NC = \033[0m # No Color

directory:
	@echo "Listing directory contents..."
	@if ls -a -l; then \
		printf "$(GREEN)Directory listed successfully.$(NC)\n"; \
		if docker ps; then \
			printf "$(GREEN)Docker daemon is running. Executing dockerup...$(NC)\n"; \
			$(MAKE) dockerup; \
		else \
			printf "$(RED)Docker daemon is not running. Exiting...$(NC)\n"; \
			exit 1; \
		fi; \
	else \
		printf "$(RED)Failed to list directory contents. Exiting...$(NC)\n"; \
		exit 1; \
	fi

dockerup:
	@echo "Docker compose up..."
	@if docker-compose up -d; then \
		echo "$(GREEN)docker exec ok$(NC)\n"; \
		$(MAKE) testunitphp; \
	else \
		printf "$(RED)erro docker compose file$(NC)\n"; \
		exit 1; \
	fi

testunitphp:
	@echo "php unit test..."
	@if docker compose exec app composer test:debug app/src/Tests/Unit; then \
		printf "$(GREEN)php unit test ok$(NC)\n"; \
		$(MAKE) curl; \
	else \
		printf "$(RED)erro php unit test$(NC)\n"; \
		exit 1; \
	fi


curl:
	@echo "curl..."
	@status_code=$$(curl -s -o /dev/null -w "%{http_code}" http://localhost:9502/teste); \
	if [ $$status_code -eq 200 ]; then \
		printf "$(GREEN)curl ok$(NC)\n"; \
	else \
		printf "$(RED)erro curl: HTTP status $$status_code$(NC)\n"; \
		exit 1; \
	fi
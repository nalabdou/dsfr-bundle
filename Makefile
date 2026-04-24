.DEFAULT_GOAL := help
.PHONY: help install update ci cs cs-fix stan rector rector-fix \
        test test-unit test-integration coverage security audit \
        clean clear-cache matrix debug-components validate-license \
        generate-enums docs release

# ── Colors ────────────────────────────────────────────────────────────────────
RESET   := \033[0m
BOLD    := \033[1m
GREEN   := \033[32m
YELLOW  := \033[33m
BLUE    := \033[34m
CYAN    := \033[36m
RED     := \033[31m
GRAY    := \033[90m

# ── Binaries ──────────────────────────────────────────────────────────────────
PHP         := php
COMPOSER    := composer
PHPUNIT     := vendor/bin/phpunit
PHPSTAN     := vendor/bin/phpstan
CS_FIXER    := vendor/bin/php-cs-fixer
RECTOR      := vendor/bin/rector

# ── Config ────────────────────────────────────────────────────────────────────
SYMFONY_VERSION   ?= 7.1.*
PHP_VERSION       ?= $(shell php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
COVERAGE_MIN      ?= 80
COVERAGE_DIR      := coverage
DEPRECATIONS_HELPER := max[direct]=0

# ══════════════════════════════════════════════════════════════════════════════
# HELP
# ══════════════════════════════════════════════════════════════════════════════

help: ## Show this help
	@printf "\n$(BOLD)$(CYAN)nalabdou/dsfr-bundle$(RESET)\n"
	@printf "$(GRAY)PHP $(PHP_VERSION) · Symfony $(SYMFONY_VERSION)$(RESET)\n\n"
	@printf "$(BOLD)Usage:$(RESET) make $(CYAN)<target>$(RESET)\n\n"
	@awk 'BEGIN {FS = ":.*##"} \
		/^[a-zA-Z_-]+:.*##/ { \
			printf "  $(CYAN)%-22s$(RESET) %s\n", $$1, $$2 \
		} \
		/^##/ { \
			printf "\n$(BOLD)%s$(RESET)\n", substr($$0, 4) \
		}' $(MAKEFILE_LIST)
	@printf "\n"

# ══════════════════════════════════════════════════════════════════════════════
## Dependencies
# ══════════════════════════════════════════════════════════════════════════════

install: ## Install dependencies
	@printf "$(BOLD)$(BLUE)Installing dependencies...$(RESET)\n"
	@SYMFONY_REQUIRE=$(SYMFONY_VERSION) $(COMPOSER) install \
		--no-interaction \
		--prefer-dist \
		--optimize-autoloader
	@printf "$(GREEN)✓ Dependencies installed$(RESET)\n"

update: ## Update dependencies
	@printf "$(BOLD)$(BLUE)Updating dependencies...$(RESET)\n"
	@SYMFONY_REQUIRE=$(SYMFONY_VERSION) $(COMPOSER) update \
		--no-interaction \
		--prefer-dist \
		--optimize-autoloader
	@printf "$(GREEN)✓ Dependencies updated$(RESET)\n"

update-lowest: ## Update to lowest compatible versions
	@printf "$(BOLD)$(BLUE)Installing lowest dependencies...$(RESET)\n"
	@SYMFONY_REQUIRE=$(SYMFONY_VERSION) $(COMPOSER) update \
		--no-interaction \
		--prefer-lowest \
		--prefer-stable
	@printf "$(GREEN)✓ Lowest dependencies installed$(RESET)\n"

# ══════════════════════════════════════════════════════════════════════════════
## Code Quality
# ══════════════════════════════════════════════════════════════════════════════

cs: ## Check code style (dry-run)
	@printf "$(BOLD)$(BLUE)Checking code style...$(RESET)\n"
	@$(PHP) $(CS_FIXER) fix \
		--dry-run \
		--diff \
		--no-interaction \
		--ansi
	@printf "$(GREEN)✓ Code style OK$(RESET)\n"

cs-fix: ## Fix code style
	@printf "$(BOLD)$(BLUE)Fixing code style...$(RESET)\n"
	@$(PHP) $(CS_FIXER) fix \
		--no-interaction \
		--ansi
	@printf "$(GREEN)✓ Code style fixed$(RESET)\n"

stan: ## Run PHPStan (level 9)
	@printf "$(BOLD)$(BLUE)Running PHPStan level 9...$(RESET)\n"
	@$(PHP) $(PHPSTAN) analyse \
		--no-progress \
		--ansi
	@printf "$(GREEN)✓ PHPStan OK$(RESET)\n"

stan-generate-baseline: ## Generate PHPStan baseline
	@printf "$(BOLD)$(YELLOW)Generating PHPStan baseline...$(RESET)\n"
	@$(PHP) $(PHPSTAN) analyse \
		--generate-baseline \
		--no-progress
	@printf "$(YELLOW)⚠ Baseline generated — commit phpstan-baseline.neon$(RESET)\n"

rector: ## Run Rector (dry-run)
	@printf "$(BOLD)$(BLUE)Running Rector (dry-run)...$(RESET)\n"
	@$(PHP) $(RECTOR) process \
		--dry-run \
		--no-progress-bar \
		--ansi
	@printf "$(GREEN)✓ Rector OK$(RESET)\n"

rector-fix: ## Apply Rector transformations
	@printf "$(BOLD)$(YELLOW)Applying Rector...$(RESET)\n"
	@$(PHP) $(RECTOR) process \
		--no-progress-bar \
		--ansi
	@printf "$(GREEN)✓ Rector applied$(RESET)\n"

# ══════════════════════════════════════════════════════════════════════════════
## Tests
# ══════════════════════════════════════════════════════════════════════════════

test: ## Run all tests (no coverage)
	@printf "$(BOLD)$(BLUE)Running test suite...$(RESET)\n"
	@SYMFONY_DEPRECATIONS_HELPER=$(DEPRECATIONS_HELPER) \
	$(PHP) $(PHPUNIT) \
		--testdox \
		--no-coverage \
		--colors=always
	@printf "$(GREEN)✓ Tests passed$(RESET)\n"

test-unit: ## Run unit tests only
	@printf "$(BOLD)$(BLUE)Running unit tests...$(RESET)\n"
	@SYMFONY_DEPRECATIONS_HELPER=$(DEPRECATIONS_HELPER) \
	$(PHP) $(PHPUNIT) \
		--testsuite=unit \
		--testdox \
		--no-coverage \
		--colors=always
	@printf "$(GREEN)✓ Unit tests passed$(RESET)\n"

test-integration: ## Run integration tests only
	@printf "$(BOLD)$(BLUE)Running integration tests...$(RESET)\n"
	@SYMFONY_DEPRECATIONS_HELPER=$(DEPRECATIONS_HELPER) \
	$(PHP) $(PHPUNIT) \
		--testsuite=integration \
		--testdox \
		--no-coverage \
		--colors=always
	@printf "$(GREEN)✓ Integration tests passed$(RESET)\n"

test-filter: ## Run tests matching a filter — usage: make test-filter F=TagTest
	@printf "$(BOLD)$(BLUE)Running tests matching: $(F)...$(RESET)\n"
	@SYMFONY_DEPRECATIONS_HELPER=$(DEPRECATIONS_HELPER) \
	$(PHP) $(PHPUNIT) \
		--filter=$(F) \
		--testdox \
		--no-coverage \
		--colors=always

coverage: ## Run tests with PCOV coverage report
	@printf "$(BOLD)$(BLUE)Running tests with coverage (PCOV)...$(RESET)\n"
	@mkdir -p $(COVERAGE_DIR)
	@XDEBUG_MODE=off \
	SYMFONY_DEPRECATIONS_HELPER=$(DEPRECATIONS_HELPER) \
	$(PHP) $(PHPUNIT) \
		--coverage-html=$(COVERAGE_DIR)/html \
		--coverage-clover=$(COVERAGE_DIR)/clover.xml \
		--coverage-xml=$(COVERAGE_DIR)/coverage-xml \
		--log-junit=$(COVERAGE_DIR)/junit.xml \
		--colors=always
	@printf "$(GREEN)✓ Coverage report generated$(RESET)\n"
	@printf "  $(GRAY)→ HTML : $(COVERAGE_DIR)/html/index.html$(RESET)\n"
	@printf "  $(GRAY)→ Clover: $(COVERAGE_DIR)/clover.xml$(RESET)\n"
	@$(MAKE) coverage-check

coverage-check: ## Check coverage meets minimum threshold
	@printf "$(BOLD)$(BLUE)Checking coverage threshold ($(COVERAGE_MIN)%%)...$(RESET)\n"
	@$(PHP) -r " \
		\$$xml = simplexml_load_file('$(COVERAGE_DIR)/clover.xml'); \
		\$$metrics = \$$xml->project->metrics; \
		\$$covered = (int)\$$metrics['coveredstatements']; \
		\$$total = (int)\$$metrics['statements']; \
		if (\$$total === 0) { echo 'No statements found.' . PHP_EOL; exit(0); } \
		\$$pct = round((\$$covered / \$$total) * 100, 2); \
		echo 'Coverage: ' . \$$pct . '%% (' . \$$covered . '/' . \$$total . ')' . PHP_EOL; \
		if (\$$pct < $(COVERAGE_MIN)) { \
			echo 'FAIL: Below minimum $(COVERAGE_MIN)%%' . PHP_EOL; \
			exit(1); \
		} \
		echo 'OK: Meets minimum $(COVERAGE_MIN)%%' . PHP_EOL; \
	"

coverage-open: coverage ## Open coverage report in browser
	@printf "$(BOLD)$(BLUE)Opening coverage report...$(RESET)\n"
	@open $(COVERAGE_DIR)/html/index.html 2>/dev/null \
		|| xdg-open $(COVERAGE_DIR)/html/index.html 2>/dev/null \
		|| printf "$(YELLOW)Open manually: $(COVERAGE_DIR)/html/index.html$(RESET)\n"

# ══════════════════════════════════════════════════════════════════════════════
## Security
# ══════════════════════════════════════════════════════════════════════════════

security: ## Run composer security audit
	@printf "$(BOLD)$(BLUE)Running security audit...$(RESET)\n"
	@$(COMPOSER) audit --no-interaction --format=plain
	@printf "$(GREEN)✓ No known vulnerabilities$(RESET)\n"

audit: ## Run full security + abandoned packages check
	@$(MAKE) security
	@printf "$(BOLD)$(BLUE)Checking for abandoned packages...$(RESET)\n"
	@$(COMPOSER) show --format=json | $(PHP) -r " \
		\$$data = json_decode(file_get_contents('php://stdin'), true); \
		\$$abandoned = array_filter(\$$data['installed'] ?? [], fn(\$$p) => \$$p['abandoned'] ?? false); \
		if (!empty(\$$abandoned)) { \
			echo 'Abandoned packages:' . PHP_EOL; \
			foreach (\$$abandoned as \$$p) { echo '  - ' . \$$p['name'] . PHP_EOL; } \
		} else { \
			echo 'No abandoned packages.' . PHP_EOL; \
		} \
	"
	@printf "$(GREEN)✓ Audit complete$(RESET)\n"

# ══════════════════════════════════════════════════════════════════════════════
## Full CI (mirrors GitHub Actions)
# ══════════════════════════════════════════════════════════════════════════════

ci: cs stan rector test security ## Run full CI pipeline locally
	@printf "\n$(BOLD)$(GREEN)✓ CI pipeline passed$(RESET)\n"

ci-fix: cs-fix rector-fix test ## Fix then test
	@printf "\n$(BOLD)$(GREEN)✓ Fixed and tested$(RESET)\n"

# ══════════════════════════════════════════════════════════════════════════════
## Symfony Debug
# ══════════════════════════════════════════════════════════════════════════════

debug-components: ## List all registered DSFR components
	@printf "$(BOLD)$(BLUE)Registered DSFR components:$(RESET)\n"
	@$(PHP) bin/console debug:twig-component --filter=Dsfr 2>/dev/null \
		|| printf "$(YELLOW)Command requires a Symfony app context$(RESET)\n"

debug-container: ## Debug DI container for DSFR services
	@$(PHP) bin/console debug:container --tag=dsfr.component 2>/dev/null \
		|| printf "$(YELLOW)Command requires a Symfony app context$(RESET)\n"

# ══════════════════════════════════════════════════════════════════════════════
## License
# ══════════════════════════════════════════════════════════════════════════════

validate-license: ## Validate a license file — usage: make validate-license FILE=dsfr_bundle.lic
	@printf "$(BOLD)$(BLUE)Validating license: $(FILE)...$(RESET)\n"
	@$(PHP) bin/console dsfr:license:validate $(FILE) 2>/dev/null \
		|| $(PHP) -r "echo 'Run from a Symfony app with the bundle installed.' . PHP_EOL;"

# ══════════════════════════════════════════════════════════════════════════════
## Code Generation
# ══════════════════════════════════════════════════════════════════════════════

generate-enums: ## Verify PHP enums are in sync with DSFR CSS classes
	@printf "$(BOLD)$(BLUE)Checking enum/CSS sync...$(RESET)\n"
	@$(PHP) bin/generate-enums 2>/dev/null \
		|| printf "$(YELLOW)Script not yet available$(RESET)\n"

# ══════════════════════════════════════════════════════════════════════════════
## Documentation
# ══════════════════════════════════════════════════════════════════════════════

docs: ## Generate component documentation (VitePress)
	@printf "$(BOLD)$(BLUE)Generating docs...$(RESET)\n"
	@$(PHP) bin/generate-docs 2>/dev/null \
		|| printf "$(YELLOW)Docs generator not yet available$(RESET)\n"
	@cd docs && npm run build 2>/dev/null \
		|| printf "$(YELLOW)VitePress not yet configured$(RESET)\n"

docs-dev: ## Start VitePress dev server
	@printf "$(BOLD)$(BLUE)Starting docs dev server...$(RESET)\n"
	@cd docs && npm run dev

# ══════════════════════════════════════════════════════════════════════════════
## Maintenance
# ══════════════════════════════════════════════════════════════════════════════

clean: ## Remove generated files (coverage, cache)
	@printf "$(BOLD)$(YELLOW)Cleaning generated files...$(RESET)\n"
	@rm -rf \
		$(COVERAGE_DIR) \
		.phpunit.result.cache \
		.phpunit.cache \
		.php-cs-fixer.cache \
		var/cache \
		var/log
	@printf "$(GREEN)✓ Cleaned$(RESET)\n"

validate-composer: ## Validate composer.json
	@printf "$(BOLD)$(BLUE)Validating composer.json...$(RESET)\n"
	@$(COMPOSER) validate --strict --no-check-publish
	@printf "$(GREEN)✓ composer.json valid$(RESET)\n"

check-strict-types: ## Check all PHP files have declare(strict_types=1)
	@printf "$(BOLD)$(BLUE)Checking strict_types declarations...$(RESET)\n"
	@MISSING=$$(find src tests -type f -name "*.php" \
		| xargs grep -rL "declare(strict_types=1)" 2>/dev/null || true); \
	if [ -n "$$MISSING" ]; then \
		printf "$(RED)Missing declare(strict_types=1):$(RESET)\n"; \
		echo "$$MISSING" | while read f; do printf "  $(RED)✗ $$f$(RESET)\n"; done; \
		exit 1; \
	fi
	@printf "$(GREEN)✓ All files have declare(strict_types=1)$(RESET)\n"

check-gitattributes: ## Verify export-ignore entries in .gitattributes
	@printf "$(BOLD)$(BLUE)Checking .gitattributes...$(RESET)\n"
	@ENTRIES=".github tests .php-cs-fixer.php phpstan.neon rector.php phpunit.xml.dist"; \
	MISSING=""; \
	for name in $$ENTRIES; do \
		if ! grep -qE "^/?[[:space:]]*$${name}[[:space:]]+export-ignore" .gitattributes 2>/dev/null; then \
			MISSING="$$MISSING $$name"; \
		fi; \
	done; \
	if [ -n "$$MISSING" ]; then \
		printf "$(RED)Missing export-ignore entries:$(RESET)\n"; \
		for m in $$MISSING; do printf "  $(RED)✗ $$m export-ignore$(RESET)\n"; done; \
		exit 1; \
	fi
	@printf "$(GREEN)✓ .gitattributes OK$(RESET)\n"

# ══════════════════════════════════════════════════════════════════════════════
## Release
# ══════════════════════════════════════════════════════════════════════════════

release: ## Tag and push a new release — usage: make release V=0.1.0
ifndef V
	@printf "$(RED)Usage: make release V=0.1.0$(RESET)\n"
	@exit 1
endif
	@printf "$(BOLD)$(BLUE)Releasing v$(V)...$(RESET)\n"
	@$(MAKE) ci
	@git diff --quiet || (printf "$(RED)Working directory not clean$(RESET)\n" && exit 1)
	@grep -q "## [$(V)]" CHANGELOG.md || (printf "$(RED)CHANGELOG.md has no entry for v$(V)$(RESET)\n" && exit 1)
	@git tag -a "v$(V)" -m "Release v$(V)"
	@git push origin "v$(V)"
	@printf "$(GREEN)✓ Tagged v$(V) — CI will create the GitHub Release$(RESET)\n"

# ══════════════════════════════════════════════════════════════════════════════
## Matrix
# ══════════════════════════════════════════════════════════════════════════════

matrix: ## Run CI matrix locally via act
	@which act > /dev/null 2>&1 || (printf "$(RED)Install act: brew install act$(RESET)\n" && exit 1)
	@act push \
		--matrix php:8.3 \
		--matrix symfony:7.1.*

matrix-full: ## Run full CI matrix locally via act
	@which act > /dev/null 2>&1 || (printf "$(RED)Install act: brew install act$(RESET)\n" && exit 1)
	@act push

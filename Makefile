.PHONY: setup bench bench-store compare report-md report-csv clean help

setup: ## Generate subjects from config + install deps
	php bin/setup

bench: ## Run all benchmarks with comparison report
	vendor/bin/phpbench run --report=compare

bench-store: ## Run + store results as baseline
	vendor/bin/phpbench run --tag=latest --report=compare

compare: ## Run + compare against stored baseline
	vendor/bin/phpbench run --ref=latest --report=compare

report-md: ## Generate Markdown report
	@mkdir -p reports
	@echo "# Benchmark Results" > reports/results.md
	@echo "" >> reports/results.md
	@echo '```' >> reports/results.md
	vendor/bin/phpbench run --report=compare --progress=none 2>/dev/null >> reports/results.md
	@echo '```' >> reports/results.md
	@echo "" >> reports/results.md
	@echo "_Generated: $$(date -u '+%Y-%m-%d %H:%M UTC')_" >> reports/results.md
	@echo "Markdown written to reports/results.md"

report-csv: ## Export as CSV
	@mkdir -p reports
	vendor/bin/phpbench run --report=compare --output=delimited

clean: ## Remove subjects, stored results, reports
	rm -rf subjects/ .phpbench/storage/ reports/*.md reports/*.csv reports/*.html

help: ## Show available targets
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-14s\033[0m %s\n", $$1, $$2}'

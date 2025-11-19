# Semgrep Custom Rules

## Run all rules:
semgrep --config semgrep-rules/ src/

## Run only XSS:
semgrep --config semgrep-rules/xss-reflected.yml src/

## Run only Upload rule:
semgrep --config semgrep-rules/insecure-upload.yml src/

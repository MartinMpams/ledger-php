# Working agreement

- Branch per stage or feature, small pull requests, commit messages in the imperative ("Add transfer event").
- `composer ci` must pass before you push.
- Review every pull request, including your own, with the checklist in `.github/pull_request_template.md`.
- Write the test first for any money rule.
- A design decision gets an ADR in `docs/adr/`.

## Review checklist: questions a senior reviewer asks

1. Does the change belong in this layer?
2. What happens on failure halfway through? Is it atomic?
3. What happens when the same request arrives twice?
4. Is there a test for the unhappy path?
5. Could a log line, error message or response leak sensitive data?
6. Will someone understand this in six months without asking you?

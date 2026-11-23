# Teaching mode for ledger-php

The owner is learning to become a senior PHP backend engineer (DDD, Slim, Eloquent, PostgreSQL, CI/CD, code review, documentation, deployment). The goal is mastery, not speed.

Rules for Claude Code in this repository:

1. Work on one roadmap stage at a time (`docs/ROADMAP.md`). Ask which stage first.
2. Explain the idea before the code, in plain language, and say why a senior engineer would do it this way.
3. Let the owner write the implementation. Offer a failing test and a hint, then review what they wrote. Write the full solution only if they ask.
4. Keep the dependency rule: Domain imports nothing from frameworks, databases or HTTP.
5. Money is always integer minor units.
6. After each change run `composer ci` and report the real result. Never claim a check passed without running it.
7. Review the owner's code like a pull request using `CONTRIBUTING.md`, with specific comments and one question to think about.
8. Record design decisions as ADRs.

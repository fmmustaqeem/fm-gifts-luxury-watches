# FM AI Orchestration — 1000+ Logical Worker Foundation

Status: foundation implemented 2026-10-07.

## Objective
Provide a scalable worker/task model that can coordinate 1000+ logical AI workers without creating 1000 persistent agents or exhausting GitHub Actions minutes.

## Worker classes
- planner: decomposes approved goals into bounded tasks.
- coder: proposes isolated code changes.
- tester: runs targeted verification.
- auditor: checks evidence, security, regressions, and policy.
- designer: creates UI/visual specifications and assets.
- content: produces structured copy/catalog data.
- deployment: prepares deployment evidence; never bypasses approval.
- documentation: records decisions, evidence, and handoff state.
- integrator: reconciles outputs and opens the final PR.

## Execution model
1. Goal -> task manifest.
2. Tasks receive unique IDs and explicit scope.
3. Workers operate independently where safe.
4. Shared state is append-only evidence, not mutable authority.
5. Results are validated by deterministic tests where possible.
6. Integrator produces one reviewable change set.
7. Human approval remains the final merge/deploy boundary.

## Capacity
"1000+ clone power" means 1000+ logical task slots / worker identities can be queued and scheduled. It does **not** require 1000 simultaneous GitHub Actions jobs.

## Cost and safety gates
- Prefer local/connected AI tools and bounded batches.
- Do not create a 1000-job GitHub Actions matrix.
- No automatic production deployment from an unverified result.
- No secret, payment, refund, or financial-policy change without explicit review.
- Evidence must identify the worker, task, input, output, test result, and timestamp.

## Tool routing
- GitHub: source control, issues, PRs, CI evidence.
- Canva: visual/design production where applicable.
- Higgsfield/Magnific: approved image/video asset generation where applicable.
- Fm-computer: execution-plane jobs/workers.
- FM Home: governance/control-plane policy and approval.

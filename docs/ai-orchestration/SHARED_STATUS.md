# FM AI System — Shared Plan & Live Status

Last updated: 2026-10-07 UTC

## Project
FM Gifts & Luxury Watches

## Shared architecture
- FM Home = governance/control plane: policy, approval, audit, shared status.
- Fm-computer = execution plane: bounded workers, queue, dependencies, retries and evidence.
- This repository = project-specific execution surface.
- Capacity target = 10,000+ logical queued worker tasks; actual concurrency remains bounded.
- Worker output is evidence, not authority.

## Plan
1. Foundation — worker classes and repository registration.
2. Governance — Task Envelope v1, policy and approval classes.
3. Execution — bounded queue, dependencies, retries and evidence.
4. Cross-repo routing — scoped tasks through the central envelope.
5. Intelligence — planner/coder/tester/auditor/designer/content/deployment/documentation/integrator.
6. Verification — deterministic tests, CI and security evidence.
7. Production readiness — prepare only; human approval remains final.
8. Shared status — keep plan, completed work, current work and next work visible here.

## Current status
- Worker foundation: IMPLEMENTED / MERGED.
- FM Home governance: IMPLEMENTED.
- Fm-computer worker runtime: IMPLEMENTED / CI VERIFIED.
- Task Envelope v1 runtime validation: IMPLEMENTED / CI VERIFIED.
- Cross-repo routing: NEXT.
- Automatic self-merge: DISABLED.
- Automatic production deployment: DISABLED.
- Financial, secret, destructive and production actions: HUMAN APPROVAL REQUIRED.

## Work log
- 2026-10-07 — 1000+ logical worker foundation established across FM project repositories.
- 2026-10-07 — FM Home governance and 10,000+ logical capacity policy implemented.
- 2026-10-07 — Fm-computer bounded worker runtime implemented and CI verified.
- 2026-10-07 — Task Envelope v1 runtime validation implemented and CI verified.
- 2026-10-07 — shared repository status documentation standardized.

## Next
- Route repository-scoped tasks through Task Envelope v1.
- Record task/evidence IDs and PR/CI references.
- Add cross-repo health/status reporting.
- Keep project-specific details here; shared policy stays in FM Home.

## Evidence rule
A status is complete only with a verifiable commit, PR, CI/test result or other auditable evidence.

## Status legend
IMPLEMENTED / MERGED = on main.
CI VERIFIED = automated verification succeeded.
IN PROGRESS = implementation exists but gate is incomplete.
NEXT = planned, not implemented.
BLOCKED = stopped by dependency or required approval.

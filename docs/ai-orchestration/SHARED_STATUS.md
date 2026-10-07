# FM AI System — Shared Plan & Live Status

Last updated: 2026-10-07 UTC

## Project
FM Gifts & Luxury Watches

## Shared system architecture
- **FM Home** = governance/control plane: policy, approvals, audit and shared status.
- **Fm-computer** = execution plane: bounded workers, task queue, evidence and runtime verification.
- **This repository** = project execution surface for its own scoped work.
- Workers are **logical task identities**, not 1000 simultaneous GitHub Actions jobs.
- Target capacity: **10,000+ logical queued worker tasks**, with bounded real concurrency.

## Implementation plan
1. **Foundation** — worker classes, manifest and repository registration.
2. **Governance** — Task Envelope v1, policy versioning, approval classes and human final boundary.
3. **Execution** — bounded queue, dependencies, retries, evidence and deterministic tests.
4. **Cross-repo routing** — approved tasks can be routed between FM Home and Fm-computer with repository scope.
5. **Intelligence** — planner/coder/tester/auditor/designer/content/deployment/documentation/integrator roles.
6. **Verification** — CI/tests/security checks produce evidence before merge.
7. **Production readiness** — deployment preparation only; production merge/deploy remains explicitly approved.
8. **Shared status** — every project keeps this plan/status visible and updates it with implementation evidence.

## Current status
- Worker foundation: **IMPLEMENTED / MERGED** where this repository has the foundation PR.
- Governance model: **IMPLEMENTED** in FM Home.
- Execution runtime: **IMPLEMENTED** in Fm-computer.
- Task Envelope v1: **IMPLEMENTED** and runtime validation merged in Fm-computer.
- Cross-repo routing: **NEXT IMPLEMENTATION STAGE**.
- Automatic production deployment: **DISABLED by policy**.
- Automatic self-merge: **DISABLED by policy**.
- Financial/secret/destructive/production actions: **human approval required**.

## Work log
- 2026-10-07: shared 1000+ logical worker foundation introduced across FM project repositories.
- 2026-10-07: FM Home governance contract and 10,000+ logical capacity policy implemented.
- 2026-10-07: Fm-computer bounded worker runtime implemented and verified.
- 2026-10-07: Task Envelope v1 runtime validation implemented and CI-verified.
- 2026-10-07: cross-repository shared status documentation is being standardized.

## Next work
- Connect repository-scoped tasks to the central Task Envelope.
- Record task/evidence IDs and PR/CI references.
- Add cross-repo health/status reporting.
- Keep project-specific implementation details in this repository; keep shared governance in FM Home.

## Evidence rule
No status is considered complete without a verifiable commit, PR, CI/test result, or other auditable evidence. A worker result is evidence, not authority.

## Status legend
- **IMPLEMENTED / MERGED** = code is on main.
- **CI VERIFIED** = required automated verification succeeded.
- **IN PROGRESS** = implementation exists but required gate is not complete.
- **NEXT** = planned, not yet implemented.
- **BLOCKED** = stopped until an explicit dependency/approval is resolved.

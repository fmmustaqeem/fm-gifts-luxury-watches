# FM Automation Map

## Control layer

- **FM Home** is the planned central control layer for FM-wide automation, security, monitoring, and worker coordination.
- This repository is a managed project node, not the central control plane.

## Monitoring

- **FM Monitor** is the generic monitoring pattern being introduced from the proven BizToolkit monitor.
- Target definitions belong in `monitor-config.yml` rather than being hard-coded in the workflow.
- BizToolkit is the first enabled target; FM Home and FM Commerce remain disabled until their production URLs are confirmed.

## CI engine pattern

- Repository CI remains responsible for build/test/lint validation.
- FM Monitor is operational monitoring and must not replace CI checks.
- Reusable workflows or composite actions can be introduced later once the target contract is stable.

## FM Chatbot

- FM Chatbot will be added later as a managed module connected to the FM control layer.
- It should consume approved automation interfaces rather than bypass repository security controls.

## Security boundary

- Existing Phase-1 enforcement remains authoritative.
- This integration must not weaken branch protection, required reviews, status checks, CODEOWNERS, or least-privilege workflow permissions.
- Experimental automation stays isolated on `automation/integrate-biztoolkit` until validated.

## Expansion path

`FM Home (control) -> reusable automation -> project monitors/CI -> reports & alerts -> managed AI workers`

Detailed workflow inventory and ownership will be expanded after the generic monitor is validated.
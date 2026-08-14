# Branches

ThunderPHP uses the same three-branch model as HestiaCP itself:

| Branch    | Description |
|-----------|-------------|
| `main`    | Contains a snapshot of the latest development code. Not intended for production use and contains code from a merge snapshot. |
| `beta`    | Contains a snapshot of the next version which is currently in testing. Not intended for production use, however code from this branch should be stable. |
| `release` | Contains a snapshot of the latest stable release. Intended for production use. This branch contains the same code as our compiled packages. |

The [HestiaCP Quick Install integration](../hestiacp-quick-install/README.md) installs from `release`, matching how HestiaCP's own bundled apps work — a Quick Install should never hand someone code that hasn't been through `beta` first.

## How a change moves through them

1. New work — plugins, fixes, features — lands on `main` via normal commits/PRs. This is where this session's day-to-day work happens.
2. Once a batch of `main` work is judged stable enough to test as a whole, it's merged into `beta`.
3. Once `beta` has been run through real verification (migrations, a live deploy, whatever the change needs — see the rigor every feature in this repo's history has actually been tested with) and nothing's found wrong, it's merged into `release`.

There's no fixed cadence for either promotion — it happens when there's something worth promoting, verified the way it needs to be verified for what changed.

## History

Before this model existed, the repo used `main` (stable) and `nightly` (active development) — the opposite naming convention from HestiaCP's own. `nightly` was retired in favor of this model once a large batch of accumulated `nightly` work (Google Sign-In, the update checker, site overrides, `do:install`, and the HestiaCP Quick Install integration itself) had been verified end-to-end and promoted through all three branches at once as the model's starting point.

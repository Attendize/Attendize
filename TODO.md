# TODO

States: `[ ]` pending, `[~]` partial, `[!]` blocked, `[x]` done, `[-]` obsolete.

## Daily round

Filed by `~/p/bin/daily`; one bullet per finding, updated in place while it repeats.

- [ ] Dependency warning (2026-09-20): `yarn install` reports `YN0002: attendize@workspace:. doesn't provide postcss (p351ad8), requested by laravel-mix`; Grunt builds pass. Next step: audit whether Laravel Mix is needed before adding its PostCSS peer.
- [ ] <!-- daily-tasks:DEPS_BROKEN --> **DEPS_BROKEN** (first seen 2026-10-13, last seen 2026-10-13): Dependencies moved forward (non-npm round: composer:.) with composer upgrade failing in .. Fix forward; the round never downgrades. Decisive line: `Problem 5`. Re-run: `bash ~/p/bin/daily/deps-update-repo.sh ~/p/Attendize /tmp/logs`.

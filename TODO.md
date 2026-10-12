# TODO

States: `[ ]` pending, `[~]` partial, `[!]` blocked, `[x]` done, `[-]` obsolete.

## Daily round

Filed by `~/p/bin/daily`; one bullet per finding, updated in place while it repeats.

- [ ] Dependency warning (2026-09-20): `yarn install` reports `YN0002: attendize@workspace:. doesn't provide postcss (p351ad8), requested by laravel-mix`; Grunt builds pass. Next step: audit whether Laravel Mix is needed before adding its PostCSS peer.

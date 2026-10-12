# TODO Log

> Searchable record of closed project work. Active work lives in `TODO.md`.

<!-- tadu:log v1 since 2026-10-12 -->

## Moved from TODO.md (2026-10-12)

- [x] <!-- daily-tasks:BROKEN --> **BROKEN resolved** (first seen 2026-09-12, verified 2026-09-20): reproduced `node-sass@npm:4.13.1 couldn't be built successfully (exit code 1)` on Node 26.9.0; removed its unused Elixir parent without downgrades. Verification: `yarn install` → `Done with warnings in 0s 524ms`; `yarn run build-frontend` → `2 stylesheets created.` and `Done.`; `yarn why node-sass` → no matches (exit 0).
- [x] Forward fix (2026-09-20): recipe adjusted because Dart Sass was already present, there is no webpack.mix.js or Elixir build, and the actual pipeline is Grunt/Less; removed unused `laravel-elixir` and regenerated `yarn.lock`, retaining upgraded dependencies. Verification: `yarn exec grunt deploy` → `1 file created 333 kB → 155 kB` and `Done.`; `wc -c public/assets/stylesheet/application.css public/assets/stylesheet/frontend.css` → 309189 and 178705 bytes. Generated assets restored after verification to keep this dependency fix scoped; no commits made.

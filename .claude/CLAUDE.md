## Web Search
- Always prefer the SearXNG MCP tool (`searxng_web_search`) over the built-in `WebSearch` tool
- Use `web_url_read` from SearXNG MCP for reading web page content

## Code Search — ast-index (use FIRST)
- Use `ast-index search "query"` before Grep/Glob for any symbol or code lookup
- Use `ast-index class "Name"`, `ast-index symbol "name"`, `ast-index usages "Name"` for structured lookups
- Run `ast-index update` after pulling new changes to keep the index fresh
- See `.claude/rules/ast-index.md` for the full command reference

## Context Window — context-mode (active)
- `ctx_batch_execute(commands, queries)` — run multiple shell commands + search results in ONE call (primary tool)
- `ctx_execute(language, code)` — run code/shell in sandbox; only stdout enters context
- `ctx_execute_file(path, language, code)` — analyze files without dumping content into context
- `ctx_search(queries)` — query indexed content from prior batch/fetch calls
- `ctx_fetch_and_index(url, source)` — fetch URLs without raw HTML entering context
- Use `ctx stats` / `ctx doctor` to check context savings and health
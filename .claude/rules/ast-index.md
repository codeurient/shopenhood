# ast-index Rules

## Mandatory Search Rules

1. **ALWAYS use ast-index FIRST** for any code search task in this PHP/Laravel project
2. **NEVER duplicate results** — if ast-index found usages/implementations, that IS the complete answer
3. **DO NOT run grep "for completeness"** after ast-index returns results
4. **Use grep/Grep ONLY when:**
   - ast-index returns empty results
   - Searching for regex patterns (ast-index uses literal match)
   - Searching for string literals inside code (`"some text"`)
   - Searching in Blade template content or comments

## Why ast-index

ast-index is 17-69x faster than grep (1-10ms vs 200ms-3s) and returns structured, accurate results from a pre-built SQLite index.

## Command Reference (PHP/Laravel)

| Task | Command | Time |
|------|---------|------|
| Universal search | `ast-index search "query"` | ~10ms |
| Find class | `ast-index class "ClassName"` | ~1ms |
| Find symbol/method | `ast-index symbol "methodName"` | ~1ms |
| Find usages | `ast-index usages "SymbolName"` | ~8ms |
| Find implementations | `ast-index implementations "Interface"` | ~5ms |
| Call hierarchy | `ast-index call-tree "function" --depth 3` | ~1s |
| Find callers | `ast-index callers "functionName"` | ~1s |
| Module/namespace deps | `ast-index deps "ClassName"` | ~10ms |
| File outline | `ast-index outline "Controller.php"` | ~1ms |

## PHP-Specific Usage

| Task | Command |
|------|---------|
| Find controller | `ast-index class "ListingController"` |
| Find model method | `ast-index symbol "scopeActive"` |
| Find interface implementations | `ast-index implementations "ShouldQueue"` |
| Find trait usages | `ast-index usages "SoftDeletes"` |
| Find all observers | `ast-index search "Observer"` |

## Index Management

- `ast-index rebuild` — Full reindex (run after cloning or major changes)
- `ast-index update` — After git pull or adding new files
- `ast-index stats` — Show index statistics and file counts

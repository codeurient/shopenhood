# Claude Code — SearXNG Web Search Setup

## What This Does

Connects Claude Code to a local SearXNG instance so it can search the web using `searxng_web_search` and `web_url_read` tools instead of the built-in WebSearch.

---

## Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installed and running
- Python 3.12+ installed
- Claude Code (VSCode extension or CLI)

---

## Step 1 — Install the MCP Server Script

Download the SearXNG MCP server script:

```bash
mkdir -p "C:/Users/<YOUR_USERNAME>/AppData/Roaming/mcp-searxng"
```

Save `server.py` to that folder. The script bridges Claude Code ↔ SearXNG via stdio MCP protocol. It reads these environment variables:

| Variable | Default | Description |
|---|---|---|
| `SEARXNG_URL` | `https://searxng.world` | URL of your SearXNG instance |
| `SEARXNG_MAX_RESULTS` | `5` | Max results per search |
| `SEARXNG_URL_MAX_CHARS` | `4000` | Max chars when reading a URL |

Install required Python packages:

```bash
pip install httpx mcp
```

---

## Step 2 — Start SearXNG with Docker

Run SearXNG in a named container on port 8080:

```bash
docker run -d -p 8080:8080 --name searxng searxng/searxng
```

Verify it's accessible at `http://localhost:8080`.

---

## Step 3 — Enable JSON API in SearXNG

By default SearXNG only serves HTML. The MCP server needs JSON format enabled.

```bash
docker exec searxng sh -c "
sed -i '/  formats:/,/^[^ ]/{/    - html/a\\    - json
}' /etc/searxng/settings.yml
"
docker restart searxng
```

Verify JSON works:

```bash
curl "http://127.0.0.1:8080/search?q=test&format=json"
```

You should see a JSON response with results.

---

## Step 4 — Register the MCP Server in Claude Code

Open `C:/Users/<YOUR_USERNAME>/.claude.json` and add the `searxng` entry inside the `mcpServers` object:

```json
{
  "mcpServers": {
    "searxng": {
      "command": "C:/Users/<YOUR_USERNAME>/AppData/Local/Programs/Python/Python312/python.exe",
      "args": ["C:/Users/<YOUR_USERNAME>/AppData/Roaming/mcp-searxng/server.py"],
      "env": {
        "SEARXNG_URL": "http://127.0.0.1:8080",
        "SEARXNG_MAX_RESULTS": "5",
        "SEARXNG_URL_MAX_CHARS": "4000"
      }
    }
  }
}
```

Replace `<YOUR_USERNAME>` with your Windows username.

---

## Step 5 — Restart Claude Code

Fully close and reopen Claude Code. The `searxng_web_search` and `web_url_read` tools will now be available.

---

## After a PC Restart

The Docker container stops when Windows shuts down. Start it again with:

```bash
docker start searxng
```

To start it automatically with Windows, enable Docker Desktop's "Start on login" option, then run:

```bash
docker update --restart always searxng
```

---

## Troubleshooting

| Problem | Cause | Fix |
|---|---|---|
| Tool not available after restart | MCP server not in global `mcpServers` | Ensure entry is in `~/.claude.json` under `mcpServers`, not under a project entry |
| `403` error on search | JSON format not enabled | Re-run Step 3 |
| `Connection refused` | Docker container not running | Run `docker start searxng` |
| Python not found | Wrong path | Run `where python` in cmd to find the correct path |
| `mcp` module missing | Dependencies not installed | Run `pip install httpx mcp` |

---

## How It Works

```
Claude Code
    │
    ▼
MCP stdio (Python server.py)
    │
    ▼
SearXNG JSON API (http://127.0.0.1:8080)
    │
    ▼
Google / Bing / Brave / DuckDuckGo (aggregated)
```

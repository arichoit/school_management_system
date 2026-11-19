# MCP Server Integration Brief

**Service / API:** Laravel Artisan & Database MCP Bridge
**Jobs to be Done:** Allow authorized AI diagnostic tools to inspect database schema health, check logs, and run test suites safely during local development.

1. Use Model Context Protocol (MCP) filesystem and MySQL query servers.
2. Configured in `mcp.json` scoped to project root.
3. **Tools Included:** `db_schema_inspect`, `artisan_test_runner`, `log_tailer`.
4. **Environment Variables:** `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.
5. **Verification:** Test tool connection by querying table counts via MCP.
6. **Documentation:** Each tool mapped for automated debugging assistance.
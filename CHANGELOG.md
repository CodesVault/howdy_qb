# Changelog


## [2.2.1] - 2026-03-04

### Added
- `DB::truncate(<tableName>)` method for clearing all data from a table

### Fixed
- `wpdb` driver failing on DELETE without WHERE clause
- Exception handler printing HTML in CLI/terminal environments

### Internal
- Consolidated `Statement\Drop` into `Statement\Table` class for better extensibility
- Added `TableInterface` for consistent table operation API
- Renamed `QueryFactory::dropQuery()` to `tableQuery()`
- Added feature tests for truncate, drop, dropIfExists, delete-all, and update-all
- Added wpdb driver test coverage for delete and update operations

## [2.2.0] - 2026-02-18

### Added
- `AVG()`, `MIN()`, `MAX()` aggregate functions for SELECT queries
- SQL operator validation via `IdentifierValidator::validateOperator()`
- INSERT...SELECT support with aggregate functions

### Changed
- All WHERE methods now validate operators against a strict whitelist

### Internal
- Extracted WHERE clause logic into dedicated `WhereClause` trait
- Extracted JOIN clause logic into dedicated `JoinClause` trait
- Renamed `src/Expression/` to `src/Clause/`
- Added `WhereClauseInterface` for consistent WHERE API across statements
- Added `addAggregate()` shared helper for aggregate function building
- Comprehensive test coverage for aggregates and operator validation

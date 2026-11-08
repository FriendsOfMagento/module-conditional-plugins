# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

### Added

- `Platform` attribute to run a plugin or observer only on the declared edition, version and distribution.
- `Operator` attribute to combine several `Platform` conditions with `and` or `or`.
- Enums `Edition`, `Comparison`, `Distribution` and `LogicalOperator` for the attribute arguments.
- Plugins and observers that do not fit the current platform are disabled when the configuration is built.
- Support for Magento Open Source, Adobe Commerce with and without B2B, and Mage-OS.

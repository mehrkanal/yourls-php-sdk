# Architectural Decision Record (ADR)

## 2024-09-17

- This package should not use a Dependency Container to make it easily includable in small Projects.
  This decision results in more complex test code, and an optional YourlsSDK constructor parameter "$client".
- Mocks in tests are written with [codeception/stub](https://codeception.com/docs/reference/Stub)
---
name: review
description: Review ethereum-tx changes for Ethereum protocol correctness, PHP compatibility, regressions, and test coverage without modifying code unless explicitly requested.
---

# ethereum-tx code review

Use this skill when reviewing an implementation, PR, diff, bug report, or proposed refactor in this repository.

## Review process

1. Read the complete changed implementation, not only the diff hunk.
2. Read the relevant parent/child transaction class.
3. Search for callers and tests covering the behavior.
4. Determine which transaction formats are affected: legacy, EIP-2930, EIP-1559, or shared typed-transaction behavior.
5. Establish the expected protocol behavior from existing vectors or an authoritative Ethereum specification when needed.
6. Check whether the change alters serialized bytes, signing payloads, transaction hashes, signatures, sender recovery, or public API behavior.
7. Run relevant tests when execution is available.

## Correctness checklist

Check especially for:
- RLP field order and list shape.
- Typed-transaction type byte placement.
- Correct signed vs unsigned payload length.
- EIP-155 chain ID and legacy `v` behavior.
- Typed-transaction recovery parameter / `v` behavior.
- `r` and `s` encoding.
- access-list structure.
- `gas` / `gasLimit` alias behavior.
- zero, empty string, null, and missing-field handling.
- `0x` prefix handling.
- odd-length or malformed hexadecimal input.
- leading-zero behavior.
- address length and transaction-field length validation.
- PHP integer/float pitfalls for Ethereum-sized numbers.
- accidental mutation of transaction state while hashing.
- PHP 7.1 compatibility unless compatibility policy is intentionally changed.

## Security checklist

Check for:
- private-key leakage through logs/errors/tests;
- unsafe validation changes;
- signing different bytes than the caller expects;
- malformed input producing valid-looking transactions;
- dependency/security changes affecting secp256k1, Keccak, RLP, or utility code.

Do not label ordinary style issues as security vulnerabilities.

## Tests

Prefer deterministic protocol vectors over tests that merely assert implementation-specific behavior.

For a bug fix, require a regression test that fails before the fix and passes afterward when practical.

For serialization/signing changes, compare exact hexadecimal output or hash values. Do not use loose assertions for byte-level behavior.

## Output

Report findings in descending severity. For each finding include:
- affected file/method;
- concrete failure scenario;
- why it is incorrect or risky;
- smallest reasonable fix;
- test/vector that should cover it.

Separate confirmed correctness issues from questions or modernization suggestions.

If no correctness issue is found, say so explicitly and mention any meaningful test-coverage gaps separately.

Do not modify files during a review unless the user explicitly asks you to implement the fixes.

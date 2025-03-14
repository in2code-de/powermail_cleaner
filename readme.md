# EXT:powermail_cleaner

## What does it do?

Powermail Cleaner offers a new command (scheduler task) to delete old form answers including related uploads.
Further you get a new option in the backend view to cleanup all answers (or only the selected page) restricted to a
given time period in seconds.

## Version Matrix

| Branch | PM - Cleaner | Powermail    | TYPO3 | Purpose                                  |
|--------|--------------|--------------|-------|------------------------------------------|
| master | 5.0.0        | ^12          | ^12   | Latest stable releases for TYPO3 12      |
| v4     | 4.0.0        | ^10          | ^11   | Stable releases for TYPO3 11             |
| v3     | 3.0.0        | ^8.0         | ^10   | Development for TYPO3 10                 |
| v2     | 2.0.0        | ^6.2,  ^7.,4 | ^9    | Development for TYPO3 9                  |

## Installation

Plug and Play - take care that all caches are cleared

## Configuration

Plug and Play - you can overwrite templates via typoscript:

```
plugin.tx_powermail {
    view {
        templateRootPaths {
            10 = EXT:powermail_cleaner/Resources/Private/Templates/
        }

        partialRootPaths {
            10 = EXT:powermail_cleaner/Resources/Private/Partials/
        }

        layoutRootPaths {
            10 = EXT:powermail_cleaner/Resources/Private/Layouts/
        }
    }
}
```

As you can see, the original template from powermail are overwritten - this is necessary to use the functionality of Powermail cleaner.

## Development 

Local development is done via DDEV. Use ddev start and import assets via "ddev import-db" from .project/data/db.sql.gz

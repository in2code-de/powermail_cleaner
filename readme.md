# EXT:powermail_cleaner

## What does it do?

EXT:powermail_cleaner adds some features to EXT:powermail to improve GDPR compliance and handling of sent emails.

The features are: 

* add texts for GDPR purposes to send emails
* set a date for deletion to emails (based on a fixed date or a period after sending)
* delete emails based on the saved date via a command / scheduler task
* inform receivers about upcoming deletions of mails
* (force) delete old mails including files after a certain period of time vi command or scheduler task 


## Version Matrix

| Branch     | PM - Cleaner | Powermail    | TYPO3 |
|------------|--------------|--------------|-------| 
| master     | 5.0.0        | ^12.0        | ^12.4 |
| develop-v4 | 4.0.0        | ^10.7        | ^11.5 |
| develop-v3 | 3.0.0        | ^8.0         | ^10.4 |
| develop-v2 | 2.0.0        | ^6.2,  ^7.,4 | ^9.5  |

## Installation

The extension can be installed via composer. 

```
composer req in2code/powermail_cleaner 
```

If you want to use the extension in a legacy installation of TYPO3, you must download the code from the github repo and
provide it locally. A distribution via TER is not planned, because it is a commercial (PRO) extension by in2code.

## Configuration

### TypoScript

* add the static typoscript to your installation
* set the from address and name (esp. necessary for preventing SPF defiance)
* set the subject for reminder emails

### Scheduler

* add commands to the scheduler

The "informReceivers" command must be run once a day. If it is run more often, receivers get the same reminder mail
more than once a day. If it is run f.e. only once per week, receiver will miss some mails, that will be deleted.

There is room for improvement to make the sending of reminder mails more flexible (once a week f.e.).

## Development

A ddev based development environment comes with this extension. 

* clone the repository
* run `ddev start`
* run `ddev initialize`

and you are set to contribute to this extension

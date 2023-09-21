# Development

DDEV is used for development setup. 

For basic installation see https://ddev.readthedocs.io/en/stable/users/install/ddev-installation/ 

To setup a development environment for `powermail_cleaner`, just run the following commands:

```
git clone git@github.com:in2code-pro/powermail_cleaner.git
cd powermail_cleaner
ddev start
ddev initialize 
```

The backend login available with user `admin` and password `password`.

## Test cases

### BE Module

* Form Overview: Shows data deletion period
* Form "Cleanup": Allows to delete mails older then given age

### Plugin

* Plugin Preview:
* Register: data protection / clean up
  * Clean-up of form data 
  * Clean-up after the specified date
  * Contact for objection and deletion option
  * Amendments to the Data Protection Policy

### Scheduler

Cleans all answers according to their delection settings

### Frontend
* Shows the Amendments to the Data Protection Policy
